<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Controllers\User\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * ユーザー用のパスワードリセットの為のメソッド。
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = url("reset-password/${token}");
        $this->notify(new ResetPasswordNotification($url));
    }

    /**
     * Memoモデルとのリレーション（一対多）
     * @return HasMany
     */
    public function memos(): HasMany
    {
        return $this->hasMany(Memo::class);
    }

    /**
     * ユーザーを、新しい順に取得する為のスコープ。
     * @param Builder $query
     * @return void
     */
    public function scopeAvailableAllUsers(Builder $query): void
    {
        $query
            ->orderBy('updated_at', 'desc');
    }

    /**
     * 選択したユーザーを取得する為のスコープ。
     * @param Builder $query
     * @param int $request_user_id
     * @return void
     */
    public function scopeAvailableSelectUser(Builder $query, int $request_user_id): void
    {
        $query
            ->where('id', $request_user_id);
    }

    /**
     * 検索したメールアドレスを表示するの為のスコープ。
     * @param $query
     * @param $keyword
     * @return void
     */
    public function scopeSearchKeyword($query, $keyword): void
    {
        // もしメールアドレスの検索があったら
        if (!is_null($keyword)) {
            // 全角スペースを半角に変換
            $spaceConvert = mb_convert_kana($keyword, 's');
            // 空白で区切る
            $keywords = preg_split('/\s+/', $spaceConvert, -1, PREG_SPLIT_NO_EMPTY);
            // 単語をループで回す
            foreach ($keywords as $word) {
                $query->where('users.email', 'like', '%' . $word . '%');
            }
        }
    }
}
