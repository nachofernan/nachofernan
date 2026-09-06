<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrada extends Model
{
    protected $fillable = [
        'categoria_id',
        'titulo',
        'slug',
        'contenido',
        'portada',
        'tipo',
        'estado',
        'publicada_en',
    ];

    protected $casts = [
        'publicada_en' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function esCita(): bool
    {
        return $this->tipo === 'cita';
    }

    public function estaPublicada(): bool
    {
        return $this->estado === 'publicada';
    }

    public function scopePublicadas(Builder $query): Builder
    {
        return $query->where('estado', 'publicada');
    }

    public function scopeConCategoria(Builder $query): Builder
    {
        return $query->with('categoria');
    }

    public function scopeRecientesPrimero(Builder $query): Builder
    {
        return $query->latest('publicada_en');
    }
}
