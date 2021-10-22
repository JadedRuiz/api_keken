<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demanda extends Model
{
    protected $table = 'demandas_laborales';
    protected $primaryKey = 'id_demanda';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    const CREATED_AT = 'fecha_c';
    const UPDATED_AT = 'fecha_m';
    protected $fillable = [
        'id_demanda', 'id_RelacionLaboral', 'id_catDespacho', 'id_estatus', 'nombre_abogadoDemandante', 'telefono_abogadoDemandante', 'correo_abogadoDemandante', 'tipo_riesgo', 'nombre_abogadoAtendio', 'usuario_c', 'usuario_m', 'activo'
    ];
}
