<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body{margin:0;padding:0;background:#f4f7fb;color:#2d3d51;font-family:Arial,sans-serif;}
        .email-card{max-width:640px;margin:0 auto;padding:24px;background:#ffffff;border-radius:20px;box-shadow:0 24px 80px rgba(15,64,129,.12);}
        .email-header{border-radius:18px;padding:18px;text-align:center;margin-bottom:22px;}
        .email-header.colaborador{background:#e6f0ff;}
        .email-header.admin{background:#fff3e6;}
        .email-header h1{font-size:26px;color:#0f4c81;margin:0 0 6px;}
        .email-subtitle{margin:0;font-size:14px;line-height:1.5;}
        .email-subtitle.colaborador{color:#2a4a74;}
        .email-subtitle.admin{color:#74512a;}
        .email-text{font-size:16px;line-height:1.7;margin:0 0 22px;color:#455a75;}
        .email-box{border-radius:14px;padding:18px 20px;margin-bottom:22px;}
        .email-box.colaborador{background:#f5fbff;border:1px solid #d9e8f7;}
        .email-box.admin{background:#fff9f2;border:1px solid #ffe0c9;}
        .email-box h2{font-size:18px;margin:0 0 14px;}
        .email-box.colaborador h2{color:#0f4c81;}
        .email-box.admin h2{color:#a94f00;}
        .email-list{list-style:none;padding:0;margin:0;}
        .email-list li{padding:10px 0;display:flex;justify-content:space-between;align-items:flex-start;}
        .email-list li{border-bottom:1px solid #e9eff5;}
        .email-list li:last-child{border-bottom:none;}
        .email-list .label{font-weight:700;width:160px;white-space:nowrap;}
        .email-list.colaborador .label{color:#0f4c81;}
        .email-list.admin .label{color:#a94f00;}
        .email-list .value{color:#3a4a62;flex:1;}
        .email-footer{font-size:14px;color:#6b7b94;margin-top:16px;}
    </style>
</head>
<body>
<div class="email-card">
    @if($destinatario === 'colaborador')
    <div class="email-header colaborador">
        <h1>Prestamo registrado</h1>
        <p class="email-subtitle colaborador">Informe de entrega de equipo</p>
    </div>
    <p class="email-text">Hola <strong>{{ $prestamo->colaborador }}</strong>,</p>
    <p class="email-text">Se ha registrado un prestamo a tu nombre con los siguientes datos:</p>
    <div class="email-box colaborador">
        <h2>Datos del prestamo</h2>
        <ul class="email-list colaborador">
            <li><span class="label">Equipo:</span><span class="value">{{ $prestamo->tipo_equipo }}</span></li>
            <li><span class="label">Modelo:</span><span class="value">{{ $prestamo->modelo_equipo }}</span></li>
            <li><span class="label">Serial:</span><span class="value">{{ $prestamo->serial_pc }}</span></li>
            <li><span class="label">Fecha:</span><span class="value">{{ $prestamo->fecha_entrega }}</span></li>
            <li><span class="label">Hora:</span><span class="value">{{ $prestamo->hora_entrega }}</span></li>
            <li><span class="label">Entregado por:</span><span class="value">{{ $prestamo->entregado_por }}</span></li>
        </ul>
    </div>
    <p class="email-footer">Por favor conserva esta informacion.</p>

    @else
    <div class="email-header admin">
        <h1>Equipo entregado</h1>
        <p class="email-subtitle admin">Resumen de la entrega al colaborador</p>
    </div>
    <p class="email-text">Hola,</p>
    <p class="email-text">Prestaste el computador a <strong>{{ $prestamo->colaborador }}</strong>.</p>
    <div class="email-box admin">
        <h2>Detalles de la entrega</h2>
        <ul class="email-list admin">
            <li><span class="label">Correo colaborador:</span><span class="value">{{ $prestamo->correo_colaborador }}</span></li>
            <li><span class="label">Equipo:</span><span class="value">{{ $prestamo->tipo_equipo }}</span></li>
            <li><span class="label">Modelo:</span><span class="value">{{ $prestamo->modelo_equipo }}</span></li>
            <li><span class="label">Serial:</span><span class="value">{{ $prestamo->serial_pc }}</span></li>
            <li><span class="label">Fecha de entrega:</span><span class="value">{{ $prestamo->fecha_entrega }}</span></li>
            <li><span class="label">Hora de entrega:</span><span class="value">{{ $prestamo->hora_entrega }}</span></li>
            <li><span class="label">Observaciones:</span><span class="value">{{ $prestamo->observaciones_prestamo }}</span></li>
        </ul>
    </div>
    <p class="email-footer">Gracias por registrar la entrega.</p>
    @endif
</div>
</body>
</html>
