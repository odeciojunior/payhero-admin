<?php

namespace App\Traits;

use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Trait FoxModelTrait
 * @package App\Traits
 */
trait FoxModelTrait
{
    /**
     * Boot FoxModelTrait
     */
    public static function bootFoxModelTrait(): void
    {
        static::retrieved(function($model) {
            $model->appends = array_merge($model->appends, [
                'code',
            ]);
        });
    }

    /**
     * @return string
     */
    public function getCodeAttribute()
    {
        return HashIds::encode($this->id);
    }

    /**
     * @param null $enum
     * @param bool $translate
     * @return array|false|null
     */
    public function getEnumArray($enum = null, $translate = false)
    {
        $enumArray = empty($enum) ? ($this->enum ?? null) : ($this->enum[$enum] ?? null);

        return ($translate && !empty($enumArray)) ? array_combine(
            array_keys($enumArray),
            array_map(
                function($enumItem) use ($enumArray, $enum) {
                    $items = $enumArray[$enumItem];
                    if (is_string($items)) {
                        return Lang::get('definitions.enum.' . $enum . '.' . $items);
                    } else if (is_array($items)) {
                        return array_map(
                            function($value) use ($enumItem) {
                                return Lang::get('definitions.enum.' . $enumItem . '.' . $value);
                            }, $items
                        );
                    } else {
                        return $items;
                    }
                }
                , array_keys($enumArray)
            )
        ) : $enumArray;
    }

    /**
     * @param $enum
     * @param null $value
     * @param bool $translate
     * @return array|false|int|string|null
     */
    public function getEnum($enum, $value = null, $translate = false)
    {
        if (is_numeric($value)) {
            $value = $this->enum[$enum][$value] ?? '';
            if ($translate) {
                $value = Lang::get('definitions.enum.' . $enum . '.' . $value);
            }

            return $value;
        } else {
            if ($value == null) {
                return $this->enum;
            }

            return array_search($value, $this->getEnumArray($enum));
        }
    }

}
