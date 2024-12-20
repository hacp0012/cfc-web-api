<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'visibility',
    'start',
    'end',
    'summary',
    'description',
    'color',
    'metadata',
    'created_by',
  ];

  protected function casts(): array
  {
    return [
      'visibility' => 'array',
      'metadata' => 'array',
    ];
  }
}
