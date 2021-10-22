<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audiencia extends Model
{
    protected $table = 'mov_audiencias';
    protected $primaryKey = 'id_audiencia';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    const CREATED_AT = 'fecha_c';
    const UPDATED_AT = 'fecha_m';
    protected $fillable = [
        'id_audiencia', 'id_demanda', 'fecha_documento', 'fecha_recepcion', 'fecha_audiencia', 'observaciones', 'documento', 'extension','usuario_c', 'usuario_m', 'activo'
    ];
}
