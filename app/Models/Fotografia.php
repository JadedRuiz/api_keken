<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fotografia extends Model
{
    protected $table = 'rl_catfotografias';
    protected $primaryKey = 'id_foto';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_mod';
    protected $fillable = [
        'nombre_foto', 'url_foto', 'usuario_creacion', 'usuario_mod', 'activo'
    ];
}
