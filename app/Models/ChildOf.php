<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildOf extends Model
{
  use HasFactory, HasUuids;

  // protected string $table = "children_of";

  protected $fillable = ['data', 'couple', 'type', 'parent_type', 'child'];

  protected function casts(): array
  {
    return ['data' => 'array'];
  }
}
