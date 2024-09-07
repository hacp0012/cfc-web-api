<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Echos extends Model
{
  use HasFactory, HasUuids, SoftDeletes;

  protected $table = "echos";

  protected $fillable = ['published_by', 'state', 'visibility', 'title', 'text', 'audio', 'document'];

  function casts(): array
  {
    return ['visibility' => 'array'];
  }
}
