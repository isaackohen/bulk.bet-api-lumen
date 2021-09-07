<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gametransactions extends Model
{
    use HasFactory;

    public $timestamps = true;
    
    protected $table = 'gametransactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'casinoid', 'player', 'ownedBy', 'bet', 'win', 'currency', 'gameid', 'txid', 'created_at', 'type', 'rawdata', 'updated_at'
    ];
}

