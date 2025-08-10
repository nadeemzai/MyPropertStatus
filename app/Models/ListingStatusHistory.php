<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingStatusHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function changedByAgency()
    {
        return $this->belongsTo(Agency::class, 'changed_by_agency_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
