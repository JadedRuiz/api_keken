<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'rl_usuarios';
    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_mod';
    protected $fillable = [
        'id_empresa', 'id_foto', 'id_perfil', 'nombre', 'usuario', 'contra', 'usuario_creacion', 'usuario_mod', 'activo'
    ];
}
