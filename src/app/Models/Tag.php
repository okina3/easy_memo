<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

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
     * Userモデルへのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 自分自身の、全てのタグを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableAllTags(Builder $query): void
    {
        $query->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 自分自身の、選択したタグを取得する為のスコープ。
     * @param Builder $query
     * @param int $get_url_tag
     * @return void
     */
    public function scopeAvailableSelectTag(Builder $query, int $get_url_tag): void
    {
        $query->with('memos.shareSettings')
            ->where('id', $get_url_tag)
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
    }

    /**
     * タグをDBに保存する為のスコープ。
     * @param Builder $query
     * @param string $request_new_tag
     * @return void
     */
    public function scopeAvailableCreateTag(Builder $query, string $request_new_tag): void
    {
        $query->create([
            'name' => $request_new_tag,
            'user_id' => Auth::id()
        ]);
    }

    /**
     * タグが重複していないか調べる為のスコープ。
     * @param Builder $query
     * @param $request_new_tag
     * @return void
     */
    public function scopeAvailableCheckDuplicateTag(Builder $query, $request_new_tag): void
    {
        $query->where('name', $request_new_tag)
            ->where('user_id', Auth::id());
    }
}
