<?php

namespace App\Traits;

/**
 * Trait FoxPresenterTrait
 * @package App\Traits
 */
trait FoxPresenterTrait
{
    /**
     * @param $enum
     * @param int|string $value
     * @return array|int|string
     */
    public function getEnumGeneric($enum, $value = null)
    {
        return is_numeric($value) ? $this->getEnumValueByKey($enum, $value) : $this->getEnumKeyByValue($enum, $value);
    }

    /**
     * @param string $enum
     * @param int|string $key
     * @return string
     */
    public function getEnumValueByKey($enum, $key)
    {
        return $this->enum[$enum][$key] ?? '';
    }

    /**
     * @param $enum
     * @param null $value
     * @return array|false|int|string
     */
    public function getEnumKeyByValue($enum, $value = null)
    {
        if (empty($value)) {
            return $this->getEnumArray($enum);
        }

        return array_search($value, $this->getEnumArray($enum));
    }

    /**
     * @param string $enum
     * @return array
     */
    public function getEnumArray($enum = null)
    {
        $presenterEnum = ($this->enum ?? []);
        if (empty($enum) || $enum == ['*']) {
            $enumArray = $presenterEnum;
        } else if (is_array($enum)) {
            $enumArray = array_intersect_key($presenterEnum, array_flip($enum));
        } else {
            $enumArray = $presenterEnum[$enum] ?? null;
        }

        return $enumArray;
    }
}
