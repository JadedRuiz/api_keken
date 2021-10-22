<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelacionLaboral extends Model
{
    protected $table = 'relacion_laboral';
    protected $primaryKey = 'id_RelacionLaboral';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    const CREATED_AT = 'fecha_c';
    const UPDATED_AT = 'fecha_m';
    protected $fillable = [
        'id_RelacionLaboral', 'id_fotografia', 'numero_nomina', 'apellido_p', 'apellido_m', 'nombres', 'fecha_nacimiento', 'edad', 'curp', 'rfc', 'correo', 'telefono', 'direccion', 'puesto', 'departamento', 'id_sucursal', 'sueldo_mensual', 'sueldo_neto', 'fecha_ingreso', 'usuario_c', 'usuario_m','activo'
    ];
}
