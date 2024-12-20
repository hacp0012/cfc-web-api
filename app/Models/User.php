<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'fullname',
    'state',
    'civility',
    'd_naissance',
    'genre',
    'pool',
    'com_loc',
    'noyau_af',
    'pcn_in_waiting_validation',
    'parain',
    'parent',
    'couple',
    'can',
    'role',
    'telephone',
    'email',
    'email_verified_at',
    'password',
    'child_can_be_maried',
    'child_state',
    'address',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'telephone' => 'array',
      'role' => AsArrayObject::class,
      'pcn_in_waiting_validation' => 'array',
    ];
  }
}
