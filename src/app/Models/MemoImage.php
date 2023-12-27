<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'memo_id',
        'image_id',
    ];

    /**
     * データベースのタイムスタンプを有効または無効にするためのプロパティ。
     * @var bool
     */
    public $timestamps = false;
}
