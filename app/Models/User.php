<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    // ---- Role helpers ----

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isStaff(): bool   { return in_array($this->role, ['admin', 'staff']); }
    public function isViewer(): bool  { return $this->role === 'viewer'; }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'  => 'Administrator',
            'staff'  => 'Staff',
            'viewer' => 'Viewer (Read Only)',
            default  => $this->role,
        };
    }

    public function getRoleBadgeColorAttribute(): string
    {
        return match($this->role) {
            'admin'  => '#ff6b35',
            'staff'  => '#06d6a0',
            'viewer' => '#a7a5c0',
            default  => '#a7a5c0',
        };
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper($word[0] ?? '');
        }
        return $initials ?: 'U';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && file_exists(public_path('uploads/avatars/' . $this->avatar))) {
            return asset('uploads/avatars/' . $this->avatar);
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
