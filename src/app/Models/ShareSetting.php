<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ShareSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'sharing_user_id',
        'memo_id',
        'edit_access',
    ];

    /**
     * Memoモデルとのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function memo(): BelongsTo
    {
        return $this->belongsTo(Memo::class);
    }

    /**
     * Userモデルとのリレーションを返す（一対多）。
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sharing_user_id');
    }

    /**
     * 共有された、全てのメモを取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableSharesMemoAll(Builder $query): void
    {
        $query
            ->with('memo.user')
            ->where('sharing_user_id', Auth::id())
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 共有設定を、一件に、絞り込むスコープ。
     * @param Builder $query
     * @param $shared_user
     * @param $request
     * @return void
     */
    public function scopeAvailableSelectSetting(Builder $query, $shared_user, $request): void
    {
        $query
            ->where('sharing_user_id', $shared_user->id)
            ->where('memo_id', $request->memoId);
    }

    /**
     *  共有されていないメモを見られなくする為のスコープ。
     * @param Builder $query
     * @param $id
     * @return void
     */
    public function scopeAvailableSettingCheck(Builder $query, $id): void
    {
        $query
            ->with('memo')
            ->where('sharing_user_id', Auth::id())
            ->where('memo_id', $id);
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
