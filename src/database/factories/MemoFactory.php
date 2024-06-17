<?php

namespace Database\Factories;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Memo>
 */
class MemoFactory extends Factory
{
    protected $model = Memo::class;

    /**
     * モデルのデフォルト状態を定義。
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'user_id' => User::factory(),
        ];
    }
}
