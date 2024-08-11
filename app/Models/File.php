<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'type',
    'pid',
    'size',
    'mime',
    'label',
    'hashed_name',
    'folder_path',
    'original_name',
    'owner',
    'owner_group',
    'content_group',
  ];
}
