<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Searchable;

class Enseignement extends Model
{
  use HasFactory, HasUuids, SoftDeletes, Searchable;

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

  /**
   * The data type of the primary key ID.
   *
   * @var string
   */
  protected $keyType = 'string';

  /**
   * Get the value used to index the model.
   */
  public function getScoutKey(): mixed
  {
    return $this->id;
  }

  /**
   * Get the indexable data array for the model.
   *
   * @return array<string, mixed>
   */
  // #[SearchUsingPrefix(['id', 'email'])]
  #[SearchUsingFullText(['title', 'text', 'verse', 'predicator'])]
  public function toSearchableArray(): array
  {
    // $data = $this->toArray();
    // Log::debug($this->toJson());
    // return $data;

    return $this->id ? [
      'id'          => $this->id,
      'title'       => $this->title,
      'text'        => $this->text,
      'verse'       => $this->verse,
      'predicator'  => $this->predicator,
    ] : [];
  }
}
