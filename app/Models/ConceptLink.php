<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConceptLink extends Model
{
    protected $fillable = ['concept_id', 'title', 'url', 'type'];
}
