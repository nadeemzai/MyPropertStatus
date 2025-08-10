<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ListingStatusHistory::class);
    }
}
