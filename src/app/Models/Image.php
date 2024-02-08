<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

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
     * Userモデルへのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 自分自身の、全ての画像のデータを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableAllImages(Builder $query): void
    {
        $query->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 自分自身の、選択したの画像のデータを取得する為のスコープ。
     * @param Builder $query
     * @param int $id
     * @return void
     */
    public function scopeAvailableSelectImage(Builder $query, int $id): void
    {
        $query->where('id', $id)
            ->where('user_id', Auth::id());
    }

    /**
     * 画像をDBに保存する為のスコープ。
     * @param Builder $query
     * @param string $only_one_file_name
     * @return void
     */
    public function scopeAvailableCreateImage(Builder $query, string $only_one_file_name): void
    {
        $query->create([
            'user_id' => Auth::id(),
            'filename' => $only_one_file_name]);
    }
}
