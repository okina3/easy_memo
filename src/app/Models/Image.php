<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
    ];

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
