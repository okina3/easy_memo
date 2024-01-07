<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    /**
     * Memoモデルとの多対多のリレーションを定義。
     * @return BelongsToMany
     */
    public function memos(): BelongsToMany
    {
        return $this->belongsToMany(Memo::class, 'memo_tags');
    }

    /**
     * 自分自身の、全てのタグを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableTagAll(Builder $query): void
    {
        $query
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
    }

    /**
     * タグが重複していないか調べる為のスコープ。
     * @param Builder $query
     * @param $request
     * @return void
     */
    public function scopeAvailableTagExists(Builder $query, $request): void
    {
        $query
            ->where('name', $request->new_tag)
            ->where('user_id', Auth::id());
    }
}
