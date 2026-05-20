<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("conexion.php");
include("includes/header.php");

// Configurar timeouts para conexiones SMTP
ini_set('default_socket_timeout', 30);
set_time_limit(120);

// Funcion para convertir hora a formato 12 horas con AM/PM
function convertirAMPM($hora_24) {
    if (empty($hora_24)) return '';
    $partes = explode(':', $hora_24);
    $horas = intval($partes[0]);
    $minutos = $partes[1];
    $periodo = 'AM';
    
    if ($horas >= 12) {
        $periodo = 'PM';
        if ($horas > 12) {
            $horas -= 12;
        }
    }
    if ($horas == 0) {
        $horas = 12;
    }
    
    return $horas . ':' . $minutos . ' ' . $periodo;
}

// Validar que los datos requeridos existan
if (!isset($_POST['colaborador']) || !isset($_POST['serial_pc'])) {
    die("❌ Error: Faltan datos requeridos <br><a href='index.php'>Volver</a>");
}

$colaborador = $conexion->real_escape_string($_POST['colaborador']);
$correo_colaborador = $conexion->real_escape_string($_POST['correo_colaborador']);
$serial_pc = $conexion->real_escape_string($_POST['serial_pc']);
$tipo_equipo = $conexion->real_escape_string($_POST['tipo-equipo']);
$modelo_equipo = $conexion->real_escape_string($_POST['modelo_equipo']);
$fecha_entrega = $conexion->real_escape_string($_POST['fecha_entrega']);
$hora_entrega_24 = $conexion->real_escape_string($_POST['hora_entrega']);
$hora_entrega = convertirAMPM($hora_entrega_24);
$entregado_por = $conexion->real_escape_string($_POST['entregado_por']);
$observaciones_prestamo = $conexion->real_escape_string($_POST['observaciones_prestamo']);

$sql = "INSERT INTO prestamos 
(colaborador, correo_colaborador, serial_pc, tipo_equipo, modelo_equipo, fecha_entrega, hora_entrega, entregado_por, observaciones_prestamo)
VALUES 
(?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssssssss", $colaborador, $correo_colaborador, $serial_pc, $tipo_equipo, $modelo_equipo, $fecha_entrega, $hora_entrega_24, $entregado_por, $observaciones_prestamo);

if ($stmt->execute()) {
    $asunto_colaborador = "Tienes un prestamo registrado";
    $mensaje_colaborador = "<!DOCTYPE html>" .
        "<html lang='es'><head><meta charset='UTF-8'><style>" .
        "body{margin:0;padding:0;background:#f4f7fb;color:#2d3d51;font-family:Arial,sans-serif;}" .
        ".email-card{max-width:640px;margin:0 auto;padding:24px;background:#ffffff;border-radius:20px;box-shadow:0 24px 80px rgba(15,64,129,.12);}" .
        ".email-header{border-radius:18px;background:#e6f0ff;padding:18px;text-align:center;margin-bottom:22px;}" .
        ".email-header img{display:block;width:120px;height:auto;margin:0 auto 12px;border-radius:18px;box-shadow:0 6px 15px rgba(0,0,0,.08);}" .
        ".email-header h1{font-size:26px;color:#0f4c81;margin:0 0 6px;}" .
        ".email-subtitle{margin:0;color:#2a4a74;font-size:14px;line-height:1.5;}" .
        ".email-text{font-size:16px;line-height:1.7;margin:0 0 22px;color:#455a75;}" .
        ".email-box{background:#f5fbff;border:1px solid #d9e8f7;border-radius:14px;padding:18px 20px;margin-bottom:22px;}" .
        ".email-box h2{font-size:18px;color:#0f4c81;margin:0 0 14px;}" .
        ".email-list{list-style:none;padding:0;margin:0;}" .
        ".email-list li{padding:10px 0;border-bottom:1px solid #e9eff5;display:flex;justify-content:space-between;align-items:flex-start;}" .
        ".email-list li:last-child{border-bottom:none;}" .
        ".email-list .label{font-weight:700;color:#0f4c81;width:160px;white-space:nowrap;}" .
        ".email-list .value{color:#3a4a62;flex:1;}" .
        ".email-footer{font-size:14px;color:#6b7b94;margin-top:16px;}" .
        "</style></head><body><div class='email-card'><div class='email-header'><img src='cid:logo_cid' alt='Logo'><h1>Prestamo registrado</h1><p class='email-subtitle'>Informe de entrega de equipo</p></div>" .
        "<p class='email-text'>Hola <strong>$colaborador</strong>,</p>" .
        "<p class='email-text'>Se ha registrado un prestamo a tu nombre con los siguientes datos:</p>" .
        "<div class='email-box'><h2>Datos del prestamo</h2><ul class='email-list'>" .
        "<li><span class='label'>Equipo:</span><span class='value'>$tipo_equipo</span></li>" .
        "<li><span class='label'>Modelo:</span><span class='value'>$modelo_equipo</span></li>" .
        "<li><span class='label'>Serial:</span><span class='value'>$serial_pc</span></li>" .
        "<li><span class='label'>Fecha:</span><span class='value'>$fecha_entrega</span></li>" .
        "<li><span class='label'>Hora:</span><span class='value'>$hora_entrega</span></li>" .
        "<li><span class='label'>Entregado por:</span><span class='value'>$entregado_por</span></li>" .
        "</ul></div>" .
        "<p class='email-footer'>Por favor conserva esta informacion.</p></div></body></html>";

    $asunto_prestador = "Equipo entregado correctamente";
    $mensaje_prestador = "<!DOCTYPE html>" .
        "<html lang='es'><head><meta charset='UTF-8'><style>" .
        "body{margin:0;padding:0;background:#f4f7fb;color:#2d3d51;font-family:Arial,sans-serif;}" .
        ".email-card{max-width:640px;margin:0 auto;padding:24px;background:#ffffff;border-radius:20px;box-shadow:0 24px 80px rgba(15,64,129,.12);}" .
        ".email-header{border-radius:18px;background:#fff3e6;padding:18px;text-align:center;margin-bottom:22px;}" .
        ".email-header img{display:block;width:120px;height:auto;margin:0 auto 12px;border-radius:18px;box-shadow:0 6px 15px rgba(0,0,0,.08);}" .
        ".email-header h1{font-size:26px;color:#0f4c81;margin:0 0 6px;}" .
        ".email-subtitle{margin:0;color:#74512a;font-size:14px;line-height:1.5;}" .
        ".email-text{font-size:16px;line-height:1.7;margin:0 0 22px;color:#455a75;}" .
        ".email-box{background:#fff9f2;border:1px solid #ffe0c9;border-radius:14px;padding:18px 20px;margin-bottom:22px;}" .
        ".email-box h2{font-size:18px;color:#a94f00;margin:0 0 14px;}" .
        ".email-list{list-style:none;padding:0;margin:0;}" .
        ".email-list li{padding:10px 0;border-bottom:1px solid #f2e9df;display:flex;justify-content:space-between;align-items:flex-start;}" .
        ".email-list li:last-child{border-bottom:none;}" .
        ".email-list .label{font-weight:700;color:#a94f00;width:160px;white-space:nowrap;}" .
        ".email-list .value{color:#4b3d2f;flex:1;}" .
        ".email-footer{font-size:14px;color:#6b7b94;margin-top:16px;}" .
        "</style></head><body><div class='email-card'><div class='email-header'><img src='cid:logo_cid' alt='Logo'><h1>Equipo entregado</h1><p class='email-subtitle'>Resumen de la entrega al colaborador</p></div>" .
        "<p class='email-text'>Hola,</p>" .
        "<p class='email-text'>Prestaste el computador a <strong>$colaborador</strong>.</p>" .
        "<div class='email-box'><h2>Detalles de la entrega</h2><ul class='email-list'>" .
        "<li><span class='label'>Correo del colaborador:</span><span class='value'>$correo_colaborador</span></li>" .
        "<li><span class='label'>Equipo:</span><span class='value'>$tipo_equipo</span></li>" .
        "<li><span class='label'>Modelo:</span><span class='value'>$modelo_equipo</span></li>" .
        "<li><span class='label'>Serial:</span><span class='value'>$serial_pc</span></li>" .
        "<li><span class='label'>Fecha de entrega:</span><span class='value'>$fecha_entrega</span></li>" .
        "<li><span class='label'>Hora de entrega:</span><span class='value'>$hora_entrega</span></li>" .
        "<li><span class='label'>Observaciones:</span><span class='value'>$observaciones_prestamo</span></li>" .
        "</ul></div>" .
        "<p class='email-footer'>Gracias por registrar la entrega.</p></div></body></html>";

    $smtpErrors = [];

    function enviarCorreoGmail($to, $name, $subject, $message, $replyTo = null, $replyToName = null) {
        global $smtpErrors;

        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $smtpErrors[] = "Dirección no válida: $to";
            return "Dirección de correo inválida";
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = trim(GMAIL_USER);
            $mail->Password = str_replace(' ', '', trim(GMAIL_PASSWORD));
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->Timeout = 30;
            $mail->ConnectTimeout = 30;
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer debug [$level]: $str");
            };
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->setFrom(GMAIL_FROM, GMAIL_FROM_NAME);
            if (!empty($replyTo) && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($replyTo, $replyToName ?: $replyTo);
            } else {
                $mail->addReplyTo(GMAIL_FROM, 'No responder');
            }
            $mail->addAddress($to, $name);
            if (file_exists(__DIR__ . '/img/logo.png')) {
                $mail->addEmbeddedImage(__DIR__ . '/img/logo.png', 'logo_cid');
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = strip_tags(str_replace(["<br>","<br />","</p>"], "\n", $message));

            $mail->send();
            return true;
        } catch (Exception $e) {
            $errorInfo = $mail->ErrorInfo ?: $e->getMessage();
            $smtpErrors[] = $errorInfo;
            error_log('Mailer Error: ' . $errorInfo);
            error_log('PHPMailer Exception: ' . $e->getMessage());
            return $errorInfo;
        }
    }

    $enviado_colaborador = enviarCorreoGmail(
        $correo_colaborador,
        $colaborador,
        $asunto_colaborador,
        $mensaje_colaborador,
        ADMIN_EMAIL,
        ADMIN_NAME
    );
    $enviado_admin = enviarCorreoGmail(
        ADMIN_EMAIL,
        ADMIN_NAME,
        $asunto_prestador,
        $mensaje_prestador,
        $correo_colaborador,
        $colaborador
    );

    $resultado_mensaje = "✅ Guardado correctamente";
    $erroresEnvio = [];

    if ($enviado_colaborador !== true) {
        $erroresEnvio[] = "Correo colaborador: $enviado_colaborador";
    }
    if ($enviado_admin !== true) {
        $erroresEnvio[] = "Correo admin: $enviado_admin";
    }

    if (!empty($erroresEnvio)) {
        $resultado_mensaje .= " — Advertencia: no se pudo enviar uno o más correos.";
        $resultado_mensaje .= " Detalle: " . implode(' | ', $erroresEnvio);
        $resultado_mensaje .= " Revisa tu cuenta Gmail y la contraseña de aplicación.";
        $resultado_class = 'alert--warning';
    } else {
        $resultado_class = 'alert--success';
    }

    echo "<div class='alert " . $resultado_class . "'>";
    echo "<p class='alert__title'>Resultado</p>";
    echo "<p class='alert__text'>" . htmlspecialchars($resultado_mensaje, ENT_QUOTES, 'UTF-8') . "</p>";
    echo "<div class='alert__actions'><a class='alert__link' href='index.php'>Volver</a></div>";
    echo "</div>";
} else {
    echo "<div class='alert alert--error'>";
    echo "<p class='alert__title'>Error</p>";
    echo "<p class='alert__text'>" . htmlspecialchars("Error: " . $conexion->error, ENT_QUOTES, 'UTF-8') . "</p>";
    echo "<div class='alert__actions'><a class='alert__link' href='index.php'>Volver</a></div>";
    echo "</div>";
}

$stmt->close();
$conexion->close();
include("includes/footer.php");
?>