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
    CONST ACHIEVEMENT_TYPE_0 = 0; // ACHIEVEMENT
    CONST ACHIEVEMENT_TYPE_1 = 1; // LEVEL

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
            $benefits = $user->benefits->where('enabled', true)->last()->toArray();

            return [
                "name" => $data['name'],
                "description" => $data['description'],
                "storytelling" => $data['storytelling'],
                "icon" => $data['icon'],
                'achievement' => \hashids()->encode($this->id),
                'type' => self::ACHIEVEMENT_TYPE_1,
                'benefits' => $benefits['description']
            ];
        }

        if ($this->subject_type == UpdateUserAchievements::class) {
            $data = Achievement::find($this->subject_id)->toArray();

            return [
                "name" => $data['name'],
                "description" => $data['description'],
                "storytelling" => $data['storytelling'],
                "icon" => $data['icon'],
                'achievement' => \hashids()->encode($this->id),
                'type' => self::ACHIEVEMENT_TYPE_0,
                'benefits' => null
            ];
        }

        return [];
    }
}
