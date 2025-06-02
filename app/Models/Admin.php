<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticate;

class Admin extends Authenticate
{
    use HasFactory;
    protected $guard = 'admin';
    protected $fillable = 
    [
        'name',
        'type',
        'investor_id',
        'mobile',
        'username',
        'email',
        'password',
        'image',
        'status',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'type');
    }
}       
