<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Memo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    /**
     * Tagモデルとの多対多のリレーションを定義。
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'memo_tags');
    }

    /**
     * Imageモデルとの多対多のリレーションを定義。
     * @return BelongsToMany
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'memo_images');
    }

    /**
     * ShareSettingモデルとの一対多のリレーションを定義。
     * @return HasMany
     */
    public function shareSettings(): HasMany
    {
        return $this->hasMany(ShareSetting::class);
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
     * 自分自身の、全てのメモを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableAllMemos(Builder $query): void
    {
        $query
            ->with('shareSettings')
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 自分自身の、選択したメモを取得する為のスコープ。
     * @param Builder $query
     * @param $id
     * @return void
     */
    public function scopeAvailableSelectMemo(Builder $query, $id): void
    {
        $query
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 自分自身の、全ての削除済みのメモを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableAllTrashedMemos(Builder $query): void
    {
        $query
            ->where('user_id', Auth::id())
            ->orderBy('deleted_at', 'desc');
    }

    /**
     * 自分自身の、選択した削除済みのメモを取得する為のスコープ。
     * @param Builder $query
     * @param $request_memo_id
     * @return void
     */
    public function scopeAvailableSelectTrashedMemo(Builder $query, $request_memo_id): void
    {
        $query
            ->where('id', $request_memo_id)
            ->where('user_id', Auth::id());
    }
}
