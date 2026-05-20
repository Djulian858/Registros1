<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $fillable = [
        'colaborador',
        'correo_colaborador',
        'serial_pc',
        'tipo_equipo',
        'modelo_equipo',
        'fecha_entrega',
        'hora_entrega',
        'entregado_por',
        'observaciones_prestamo',
        'fecha_devolucion',
        'hora_devolucion',
        'observaciones_devolucion',
    ];
}
