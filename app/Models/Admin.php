<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true; // Add role checks if needed
    }
    use HasFactory;

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
