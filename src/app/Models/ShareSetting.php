<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'sharing_user_id',
        'memo_id',
        'edit_access',
    ];
}
