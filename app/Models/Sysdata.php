<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sysdata extends Model
{
  use HasFactory;

  protected $fillable = ['label', 'key', 'data', 'type'];

  protected function casts(): array
  {
    return ['data' => 'array'];
  }
}
