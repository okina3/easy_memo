<?php

namespace Database\Factories;

use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShareSetting>
 */
class ShareSettingFactory extends Factory
{
    protected $model = ShareSetting::class;

    /**
     * モデルのデフォルト状態を定義。
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'memo_id' => Memo::factory(),
            'sharing_user_id' => User::factory(),
            'edit_access' => $this->faker->boolean,
        ];
    }
}
