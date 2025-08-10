<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(AgencyApiKey::class);
    }

    public function connections()
    {
        return $this->hasMany(Connection::class);
    }
}
