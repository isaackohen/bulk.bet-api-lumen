<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gamelist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'gamelist';

    protected $fillable = [
        'game_id',
        'game_name',
        'game_desc',
        'game_provider',
        'extra_id',
        'type',
        'api_ext',
        'disabled'
    ];

}
