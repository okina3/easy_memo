<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'sharing_user_id',
        'memo_id',
        'edit_access',
    ];

    /**
     * Userモデルとのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sharing_user_id');
    }

    /**
     *  自分が共有しているメモの、共有情報を取得する為のスコープ。
     * @param Builder $query
     * @param $id
     * @return void
     */
    public function scopeAvailableSettingInUser(Builder $query, $id): void
    {
        $query
            ->with('user')
            ->where('memo_id', $id)
            ->orderBy('updated_at', 'desc');
    }
}
