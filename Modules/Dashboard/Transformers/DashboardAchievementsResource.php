<?php

namespace Modules\Dashboard\Transformers;

use App\Console\Commands\UpdateUserAchievements;
use App\Console\Commands\UpdateUserLevel;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Achievement;
use Modules\Core\Services\Performance\UserLevel;

/**
 * Class DashboardAchievementsResource
 * @package Modules\Dashboard\Transformers
 */
class DashboardAchievementsResource extends JsonResource
{
    const ACHIEVEMENT_TYPE_0 = 0; // ACHIEVEMENT
    const ACHIEVEMENT_TYPE_1 = 1; // LEVEL

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->subject_type == UpdateUserLevel::class) {
            $user = auth()->user();
            $data = (new UserLevel())->getLevelData($this->subject_id);
            $benefits = $user->benefits->where("enabled", true)->toArray();
            $data["benefits"] = null;

            if (!empty($benefits)) {
                $benefitsDescription = array_column($benefits, "description");
                $data["benefits"] = $this->arrayToString($benefitsDescription);
            }

            return [
                "name" => $data["name"],
                "description" => $data["description"],
                "storytelling" => $data["storytelling"],
                "icon" => $data["icon"],
                "achievement" => \hashids()->encode($this->id),
                "type" => self::ACHIEVEMENT_TYPE_1,
                "benefits" => $data["benefits"],
            ];
        }

        if ($this->subject_type == UpdateUserAchievements::class) {
            $data = Achievement::find($this->subject_id)->toArray();

            return [
                "name" => $data["name"],
                "description" => $data["description"],
                "storytelling" => $data["storytelling"],
                "icon" => $data["icon"],
                "achievement" => \hashids()->encode($this->id),
                "type" => self::ACHIEVEMENT_TYPE_0,
                "benefits" => null,
            ];
        }

        return [];
    }

    public function arrayToString($array)
    {
        if (count($array) > 1) {
            $lastItem = array_pop($array);
            $text = implode(", ", $array);
            $text .= " e " . $lastItem;

            return $text;
        }

        return current($array);
    }
}
