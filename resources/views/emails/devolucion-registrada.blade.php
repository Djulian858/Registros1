<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body{margin:0;padding:0;background:#f4f7fb;color:#2d3d51;font-family:Arial,sans-serif;}
        .email-card{max-width:640px;margin:0 auto;padding:24px;background:#ffffff;border-radius:20px;box-shadow:0 24px 80px rgba(15,64,129,.12);}
        .email-header{border-radius:18px;background:#e6f2f0;padding:18px;text-align:center;margin-bottom:22px;}
        .email-header h1{font-size:26px;color:#1a6b4f;margin:0 0 6px;}
        .email-subtitle{margin:0;color:#2a5a47;font-size:14px;line-height:1.5;}
        .email-text{font-size:16px;line-height:1.7;margin:0 0 22px;color:#455a75;}
        .email-box{background:#f5fdf9;border:1px solid #d9e8e3;border-radius:14px;padding:18px 20px;margin-bottom:22px;}
        .email-box h2{font-size:18px;color:#1a6b4f;margin:0 0 14px;}
        .email-list{list-style:none;padding:0;margin:0;}
        .email-list li{padding:10px 0;border-bottom:1px solid #e9f2ef;display:flex;justify-content:space-between;align-items:flex-start;}
        .email-list li:last-child{border-bottom:none;}
        .email-list .label{font-weight:700;color:#1a6b4f;width:160px;white-space:nowrap;}
        .email-list .value{color:#3a4a62;flex:1;}
        .email-footer{font-size:14px;color:#6b7b94;margin-top:16px;}
    </style>
</head>
<body>
<div class="email-card">
    <div class="email-header">
        <h1>Equipo devuelto</h1>
        <p class="email-subtitle">
            {{ $destinatario === 'colaborador' ? 'Confirmacion de devolucion' : 'Registro de devolucion' }}
        </p>
    </div>

    @if($destinatario === 'colaborador')
    <p class="email-text">Hola <strong>{{ $prestamo->colaborador }}</strong>,</p>
    <p class="email-text">Se ha registrado la devolucion de tu equipo. Gracias por cuidarlo.</p>
    @else
    <p class="email-text">El colaborador <strong>{{ $prestamo->colaborador }}</strong> ha devuelto un equipo.</p>
    @endif

    <div class="email-box">
        <h2>Datos de la devolucion</h2>
        <ul class="email-list">
            @if($destinatario === 'admin')
            <li><span class="label">Colaborador:</span><span class="value">{{ $prestamo->colaborador }}</span></li>
            <li><span class="label">Correo:</span><span class="value">{{ $prestamo->correo_colaborador }}</span></li>
            @endif
            <li><span class="label">Equipo:</span><span class="value">{{ $prestamo->tipo_equipo }}</span></li>
            <li><span class="label">Modelo:</span><span class="value">{{ $prestamo->modelo_equipo }}</span></li>
            <li><span class="label">Serial:</span><span class="value">{{ $prestamo->serial_pc }}</span></li>
            <li><span class="label">Fecha de devolucion:</span><span class="value">{{ $prestamo->fecha_devolucion }}</span></li>
            <li><span class="label">Hora de devolucion:</span><span class="value">{{ $prestamo->hora_devolucion }}</span></li>
        </ul>
    </div>

    <p class="email-footer">
        {{ $destinatario === 'colaborador' ? 'Gracias por usar nuestro sistema de prestamos.' : 'Equipo devuelto correctamente.' }}
    </p>
</div>
</body>
</html>
