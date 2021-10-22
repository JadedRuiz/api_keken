<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'rl_catempresas';
    protected $primaryKey = 'id_empresa';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_mod';
    protected $fillable = [
        'id_foto', 'razon_social', 'rfc', 'curp', 'representante', 'usuario_creacion', 'usuario_mod', 'activo'
    ];
}
