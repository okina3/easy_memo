<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
    ];

    /**
     * Memoモデルとの多対多のリレーションを定義。
     * @return BelongsToMany
     */
    public function memos(): BelongsToMany
    {
        return $this->belongsToMany(Memo::class, 'memo_images');
    }

    /**
     * 自分自身の画像のデータを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableImageAll(Builder $query): void
    {
        $query
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc');
    }
}
