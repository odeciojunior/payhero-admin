<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Exceptions\Services\ServiceException;

class DigitalOceanFileService
{
    /**
     * @var App
     */
    protected $app;
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $disk;
    /**
     * @var string
     */
    protected $selectedDiskName;
    /**
     * @var string
     */
    private $extension;
    /**
     * @var array
     */
    private $arrayExtensions = ['jpeg', 'jpg', 'pdf', 'png', 'gif', 'docx', 'jfif'];
    /**
     * @var array
     */
    private $availableDisks = ['downloadSpaces', 'openSpaces'];

    /**
     * AbstractUploadApi constructor.
     * @param App $app
     * @throws Exception
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeDisk();
    }

    /**
     * @return string
     */
    public function disk()
    {
        return $this->selectedDiskName ?? $this->defaultDisk();
    }

    /**
     * @return mixed
     */
    public function defaultDisk()
    {
        return $this->selectedDiskName = 'openSpaces';
    }

    /**
     * @param string $folder
     * @param string $file
     * @param null|string $name
     * @param bool $rename
     * @param string $type
     * @return bool|string
     * @throws ServiceException
     */
    public function uploadFile($folder, $file, $name = null, $rename = false, $type = 'public')
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $url       = $this->disk->put($folder, $file, $type);

            if ($rename) {
                if ($this->disk->move($url, $folder . $name . '.' . $extension))
                    $url = $folder . $name . '.' . $extension;
                else
                    return false;
            }

            return $this->getUrlFile($url);
        } catch (Exception $ex) {
            report($ex);
            throw new ServiceException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $path
     * @param null|string $extension
     * @return bool
     * @throws ServiceException
     */
    public function checkIfFileExists($path, $extension = null)
    {
        try {
            $path = $this->getPath($path);
            if ($extension) {
                if ($this->disk->exists($path))
                    return true;
                else
                    return false;
            } else {
                foreach ($this->arrayExtensions as $extension) {
                    if ($this->disk->exists($path . '.' . $extension)) {
                        $this->extension = $extension;

                        return true;
                    }
                }

                return false;
            }
        } catch (Exception $ex) {
            report($ex);
            throw new ServiceException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $path
     * @return null|string
     * @throws ServiceException
     */
    public function getUrlFile($path)
    {
        try {
            $path  = $this->getPath($path);
            $check = $this->check($path);

            if ($check == true) {
                if ($this->extension == null) {
                    return $this->disk->url($path);
                } else {
                    return $this->disk->url($path . '.' . $this->extension);
                }
            }

            return null;
        } catch (Exception $ex) {
            report($ex);
            throw new ServiceException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param null|string $path
     * @param bool $softDelete
     * @return bool
     * @throws ServiceException
     */
    public function deleteFile($path = null, $softDelete = true)
    {
        try {
            $path = $this->getPath($path);
            if ($this->check($path)) {
                if ($softDelete) {
                    $date  = str_replace(".", "", microtime(true));
                    $array = explode("/", $path);
                    $name  = $date . end($array);
                    if ($this->disk->move($path, 'trash/' . $name))
                        return true;
                } else if ($this->disk->delete($path)) {
                    return true;
                }
            }

            return false;
        } catch (Exception $ex) {
            report($ex);
            throw new ServiceException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getPath($path)
    {
        $result         = parse_url($path);
        $result['path'] = ltrim($result['path'], '/');

        return $result['path'];
    }

    /**
     * @param string $path
     * @return bool
     * @throws ServiceException
     */
    public function check($path)
    {
        try {
            $path        = $this->getPath($path);
            $pathExplode = explode(".", $path);
            if (isset($pathExplode[1]))
                return $this->checkIfFileExists($path, true);
            else
                return $this->checkIfFileExists($pathExplode[0], null);
        } catch (Exception $ex) {
            report($ex);
            throw new ServiceException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @return mixed
     * @throws ServiceException
     */
    public function makeDisk()
    {
        try {
            return $this->setDisk($this->selectedDiskName ?? $this->defaultDisk());
        } catch (Exception $ex) {
            report($ex);
            throw new ServiceException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param $disk
     * @return $this
     * @throws ServiceException
     */
    public function changeDisk($disk)
    {
        try {
            if (in_array($disk, $this->availableDisks)) {
                $this->selectedDiskName = $disk;
            } else {
                $this->selectedDiskName = $this->defaultDisk();
            }
            $this->makeDisk();

            return $this;
        } catch (Exception $ex) {
            report($ex);
            throw new ServiceException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Set Disk to instantiate
     * @param $disk
     * @return mixed
     * @throws ServiceException
     */
    public function setDisk($disk)
    {
        try {
            $this->disk = Storage::disk($disk);
        } catch (Exception $ex) {
            report($ex);
            throw new ServiceException($ex->getMessage(), $ex->getCode(), $ex);
        }

        return $this->disk;
    }
}
