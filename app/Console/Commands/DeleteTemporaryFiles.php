<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteTemporaryFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:deleteTemporaryFiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Arquivos/Diretorios temporarios no registro';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $sDrive = Storage::disk('s3_documents');
            $files = $sDrive->allFiles('uploads/register/user');

            $totalDeleteFiles = 0;

            foreach ($files as $file) {

                if ($sDrive->lastModified($file) < Carbon::yesterday()->unix()) {
                    $sDrive->delete($file);
                    $totalDeleteFiles++;
                }

            }
            print('DeleteTemporaryFiles - Success !' . PHP_EOL);
            print('Deleted temp files: ' . $totalDeleteFiles . PHP_EOL);
        } catch (Exception $e) {
            Log::warning('DeleteTemporaryFiles - Error command ');
            report($e);
        }
    }
}
