<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Memo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    /**
     * ShareSettingモデルとの一対多のリレーションを定義。
     * @return HasMany
     */
    public function shareSettings(): HasMany
    {
        return $this->hasMany(ShareSetting::class);
    }

    /**
     * 自分自身の、全てのメモを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableMemoAll(Builder $query): void
    {
        $query
            ->with('shareSettings')
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
    }
}
