<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concept extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'tldr',
        'summary',
        'field_notes',
        'image_path',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(ConceptLink::class);
    }
}