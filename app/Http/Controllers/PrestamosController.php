<?php

namespace App\Http\Controllers;

use App\Mail\DevolucionRegistrada;
use App\Mail\PrestamoRegistrado;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PrestamosController extends Controller
{
    public function index()
    {
        return view('prestamos.index', [
            'defaultEntregadoPor' => config('app.default_entregado_por'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'colaborador'  => 'required|string|max:255',
            'serial_pc'    => 'required|string|max:255',
            'tipo-equipo'  => 'required|string|max:255',
            'fecha_entrega' => 'required|date',
            'hora_entrega'  => 'required',
            'entregado_por' => 'required|email',
        ]);

        $prestamo = Prestamo::create([
            'colaborador'           => $request->colaborador,
            'correo_colaborador'    => $request->correo_colaborador,
            'serial_pc'             => $request->serial_pc,
            'tipo_equipo'           => $request->input('tipo-equipo'),
            'modelo_equipo'         => $request->modelo_equipo,
            'fecha_entrega'         => $request->fecha_entrega,
            'hora_entrega'          => $request->hora_entrega,
            'entregado_por'         => $request->entregado_por,
            'observaciones_prestamo' => $request->observaciones_prestamo,
        ]);

        $errores = [];

        try {
            Mail::to($prestamo->correo_colaborador, $prestamo->colaborador)
                ->send(new PrestamoRegistrado($prestamo, 'colaborador'));
        } catch (\Exception $e) {
            $errores[] = 'Correo colaborador: ' . $e->getMessage();
        }

        try {
            Mail::to(config('mail.from.address'), config('app.admin_name'))
                ->send(new PrestamoRegistrado($prestamo, 'admin'));
        } catch (\Exception $e) {
            $errores[] = 'Correo admin: ' . $e->getMessage();
        }

        $mensaje = '✅ Guardado correctamente';
        $clase   = 'alert--success';

        if (!empty($errores)) {
            $mensaje .= ' — Advertencia: no se pudo enviar uno o más correos. ' . implode(' | ', $errores);
            $clase = 'alert--warning';
        }

        return view('prestamos.resultado', compact('mensaje', 'clase'));
    }

    public function ver()
    {
        $prestamos = Prestamo::orderByDesc('id')->get();
        $mensaje   = request('mensaje');

        return view('prestamos.ver', compact('prestamos', 'mensaje'));
    }

    public function update(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:prestamos,id']);

        $prestamo = Prestamo::findOrFail($request->id);

        $prestamo->update([
            'fecha_devolucion'        => $request->fecha_devolucion ?: null,
            'hora_devolucion'         => $request->hora_devolucion ?: null,
            'observaciones_devolucion' => $request->observaciones_devolucion,
        ]);

        $errores = [];

        if ($request->filled('fecha_devolucion')) {
            try {
                Mail::to($prestamo->correo_colaborador, $prestamo->colaborador)
                    ->send(new DevolucionRegistrada($prestamo, 'colaborador'));
            } catch (\Exception $e) {
                $errores[] = 'Correo colaborador: ' . $e->getMessage();
            }

            try {
                Mail::to(config('mail.from.address'), config('app.admin_name'))
                    ->send(new DevolucionRegistrada($prestamo, 'admin'));
            } catch (\Exception $e) {
                $errores[] = 'Correo admin: ' . $e->getMessage();
            }
        }

        $sufijo = empty($errores)
            ? ''
            : ' — Error al enviar correos: ' . implode(' | ', $errores);

        $msgParam = empty($errores) ? 'actualizado' : 'actualizado_con_error';

        return redirect()->route('prestamos.ver')->with('mensaje', $msgParam . $sufijo);
    }

    public function destroy($id)
    {
        Prestamo::findOrFail($id)->delete();

        return redirect()->route('prestamos.ver')->with('mensaje', 'eliminado');
    }
}
