<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couple extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = ['nom', 'epoue', 'epouse', 'enfants', 'd_mariage', 'adresse', 'phone', 'photo'];

  protected $casts = ['enfants' => AsArrayObject::class];
}
