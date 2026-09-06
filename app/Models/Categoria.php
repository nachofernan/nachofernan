<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'slug'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function entradas(): HasMany
    {
        return $this->hasMany(Entrada::class);
    }
}
