<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

     use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'agency_id',
        'status',
        'agency_proposed',
        'user_approved',
        'approved_at',
        'agency_notes',
        'meta',
    ];

    protected $casts = [
        'agency_proposed' => 'boolean',
        'user_approved' => 'boolean',
        'approved_at' => 'datetime',
        'meta' => 'array',
    ];

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
