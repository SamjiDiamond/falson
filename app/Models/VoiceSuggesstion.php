<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoiceSuggesstion extends Model
{
    protected $table='tbl_voice_suggesstion';

    protected $fillable = [
        'find', 'response'
    ];
}
