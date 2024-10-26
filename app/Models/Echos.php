<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Searchable;

class Echos extends Model
{
  use HasFactory, HasUuids, SoftDeletes, Searchable;

  protected $table = "echos";

  protected $fillable = ['published_by', 'state', 'visibility', 'title', 'text', 'audio', 'document'];

  function casts(): array
  {
    return ['visibility' => 'array'];
  }

  /**
   * Get the indexable data array for the model.
   *
   * @return array<string, mixed>
   */
  #[SearchUsingFullText(['title', 'text'])]
  public function toSearchableArray(): array
  {
    Log::debug($this->toJson());

    return $this->id ? [
      'id'    => $this->id,
      'title' => $this->title,
      // 'text'  => $this->text,
    ] : [];
  }
}
