<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agency extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true; // Add role checks if needed
    }

    use HasFactory, SoftDeletes, Notifiable;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'phone', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guarded = [];

    // Automatically hash password on set
    public function setPasswordAttribute($value)
    {
       if ($value) $this->attributes['password'] = bcrypt($value);
    }

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
