<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersData extends Model
{
  use HasFactory, HasUuids;

  protected $table = 'users_datas';

  protected $fillable = ['data', 'owner', 'key'];

  protected function casts(): array
  {
    return ['data' => 'array'];
  }
}
