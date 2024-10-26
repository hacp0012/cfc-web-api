<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Searchable;

class Communique extends Model
{
  use HasFactory, HasUuids, SoftDeletes, Searchable;

  protected $fillable = [
    'published_by',
    'state',
    'visibility',
    'title',
    'text',
    'picture',
    'document',
    'status',
  ];

  protected function casts(): array
  {
    return ['visibility' => 'array'];
  }

  /**
   * Get the indexable data array for the model.
   *
   * @return array<string, mixed>
   */
  // #[SearchUsingPrefix(['id'])]
  #[SearchUsingFullText(['title', 'text'])]
  public function toSearchableArray(): array
  {
    return $this->id ? [
      'id'    => $this->id,
      'title' => $this->title,
      'text'  => $this->text,
    ] : [];
  }
}
