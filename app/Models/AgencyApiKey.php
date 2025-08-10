<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyApiKey extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
