<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * Campos que se permiten asignar en masa (mass assignment).
     *
     * Cuando usas Category::create(['title' => '...']) o Category::update(...),
     * Laravel solo acepta los campos listados aquí.
     * Es una medida de seguridad: si alguien intenta inyectar campos
     * no autorizados (como 'id' o 'is_admin'), Laravel los ignora.
     *
     * Debes agregar aquí cada columna de la tabla que quieras
     * poder crear/actualizar con create() o update().
     */
    protected $fillable = ['title'];
}
