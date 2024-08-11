<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
  use HasFactory;

  protected $fillable = [ 'otp', 'ref', 'data', 'expir' ];

  protected $casts = ['datas' => AsArrayObject::class];
}
