<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    protected $table = 'libros';

    public $timestamps = false;

    protected $fillable = [
        'isbn',
        'titulo',
        'autor',
        'anio_publicacion',
        'ejemplares_disponibles',
    ];

    protected $casts = [
        'id' => 'integer',
        'anio_publicacion' => 'integer',
        'ejemplares_disponibles' => 'integer',
    ];
}
