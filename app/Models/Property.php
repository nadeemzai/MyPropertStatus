<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'price' => 'decimal:2',
            'available_from' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * The listing that should represent this property publicly: the most
     * recently owner-approved one that isn't archived.
     */
    public function activeListing(): ?Listing
    {
        return $this->listings
            ->where('user_approved', true)
            ->whereNotIn('status', ['archived'])
            ->sortByDesc('approved_at')
            ->first();
    }

    public function media()
    {
        return $this->hasMany(PropertyMedia::class);
    }

    public function connections()
    {
        return $this->hasMany(Connection::class);
    }
}
