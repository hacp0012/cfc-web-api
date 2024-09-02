<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enseignement extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'published_by',
    'state',
    'visibility',
    'title',
    'date',
    'verse',
    'predicator',
    'text',
    'picture',
    'audio',
    'document',
  ];

  protected function casts(): array
  {
    return ['visibility' => 'array'];
  }
}