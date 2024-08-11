<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validable extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = ['type', 'receiver', 'sender', 'datas', 'expiration', 'key'];

  protected function casts(): array
  {
    return ['datas' => 'array'];
  }
}
