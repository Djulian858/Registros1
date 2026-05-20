<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("conexion.php");
include("includes/header.php");

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
if (!isset($_POST['id'])) {
    die("❌ Error: ID no proporcionado <br><a href='ver.php'>Volver</a>");
}

$id = intval($_POST['id']);
$fecha_devolucion = isset($_POST['fecha_devolucion']) && !empty($_POST['fecha_devolucion']) ? $_POST['fecha_devolucion'] : NULL;
$hora_devolucion_24 = isset($_POST['hora_devolucion']) && !empty($_POST['hora_devolucion']) ? $_POST['hora_devolucion'] : NULL;
$hora_devolucion = $hora_devolucion_24 ? convertirAMPM($hora_devolucion_24) : NULL;
$observaciones_devolucion = isset($_POST['observaciones_devolucion']) ? $conexion->real_escape_string($_POST['observaciones_devolucion']) : '';

// Obtener los datos del registro para enviar el email
$sql_select = "SELECT colaborador, correo_colaborador, tipo_equipo, modelo_equipo, serial_pc FROM prestamos WHERE id = ?";
$stmt_select = $conexion->prepare($sql_select);
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$resultado = $stmt_select->get_result();
$registro = $resultado->fetch_assoc();
$stmt_select->close();

// Preparar la actualización
$sql = "UPDATE prestamos SET fecha_devolucion = ?, hora_devolucion = ?, observaciones_devolucion = ? WHERE id = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("❌ Error en la consulta: " . $conexion->error);
}

$stmt->bind_param("sssi", $fecha_devolucion, $hora_devolucion_24, $observaciones_devolucion, $id);

if ($stmt->execute()) {
    // Si hay fecha de devolución, enviar email de confirmación
    if (!empty($fecha_devolucion) && $registro) {
        $colaborador = $registro['colaborador'];
        $correo = $registro['correo_colaborador'];
        $tipo_equipo = $registro['tipo_equipo'];
        $modelo_equipo = $registro['modelo_equipo'];
        $serial_pc = $registro['serial_pc'];
        
        $asunto_devolucion = "Confirmacion de devolucion de equipo";
        $mensaje_devolucion = "<!DOCTYPE html>" .
            "<html lang='es'><head><meta charset='UTF-8'><style>" .
            "body{margin:0;padding:0;background:#f4f7fb;color:#2d3d51;font-family:Arial,sans-serif;}" .
            ".email-card{max-width:640px;margin:0 auto;padding:24px;background:#ffffff;border-radius:20px;box-shadow:0 24px 80px rgba(15,64,129,.12);}" .
            ".email-header{border-radius:18px;background:#e6f2f0;padding:18px;text-align:center;margin-bottom:22px;}" .
            ".email-header img{display:block;width:120px;height:auto;margin:0 auto 12px;border-radius:18px;box-shadow:0 6px 15px rgba(0,0,0,.08);}" .
            ".email-header h1{font-size:26px;color:#1a6b4f;margin:0 0 6px;}" .
            ".email-subtitle{margin:0;color:#2a5a47;font-size:14px;line-height:1.5;}" .
            ".email-text{font-size:16px;line-height:1.7;margin:0 0 22px;color:#455a75;}" .
            ".email-box{background:#f5fdf9;border:1px solid #d9e8e3;border-radius:14px;padding:18px 20px;margin-bottom:22px;}" .
            ".email-box h2{font-size:18px;color:#1a6b4f;margin:0 0 14px;}" .
            ".email-list{list-style:none;padding:0;margin:0;}" .
            ".email-list li{padding:10px 0;border-bottom:1px solid #e9f2ef;display:flex;justify-content:space-between;align-items:flex-start;}" .
            ".email-list li:last-child{border-bottom:none;}" .
            ".email-list .label{font-weight:700;color:#1a6b4f;width:160px;white-space:nowrap;}" .
            ".email-list .value{color:#3a4a62;flex:1;}" .
            ".email-footer{font-size:14px;color:#6b7b94;margin-top:16px;}" .
            "</style></head><body><div class='email-card'><div class='email-header'><img src='cid:logo_cid' alt='Logo'><h1>Equipo devuelto</h1><p class='email-subtitle'>Confirmacion de devolucion</p></div>" .
            "<p class='email-text'>Hola <strong>$colaborador</strong>,</p>" .
            "<p class='email-text'>Se ha registrado la devolucion de tu equipo. Gracias por cuidarlo.</p>" .
            "<div class='email-box'><h2>Datos de la devolucion</h2><ul class='email-list'>" .
            "<li><span class='label'>Equipo:</span><span class='value'>$tipo_equipo</span></li>" .
            "<li><span class='label'>Modelo:</span><span class='value'>$modelo_equipo</span></li>" .
            "<li><span class='label'>Serial:</span><span class='value'>$serial_pc</span></li>" .
            "<li><span class='label'>Fecha de devolucion:</span><span class='value'>$fecha_devolucion</span></li>" .
            "<li><span class='label'>Hora de devolucion:</span><span class='value'>$hora_devolucion</span></li>" .
            "</ul></div>" .
            "<p class='email-footer'>Gracias por usar nuestro sistema de prestamos.</p></div></body></html>";

        function enviarCorreoDevolucion($to, $name, $subject, $message) {
            if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
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
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                $mail->SMTPDebug = 0;
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ];

                $mail->setFrom(GMAIL_FROM, GMAIL_FROM_NAME);
                $mail->addReplyTo(GMAIL_FROM, 'No responder');
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
                error_log('Mailer Error: ' . $mail->ErrorInfo);
                return "Error enviando correo: " . $mail->ErrorInfo;
            }
        }

        // Enviar correo al colaborador
        $resultado_email_colaborador = enviarCorreoDevolucion($correo, $colaborador, $asunto_devolucion, $mensaje_devolucion);
        
        // Enviar correo al admin
        $asunto_admin = "Confirmacion: Equipo devuelto por $colaborador";
        $mensaje_admin = "<!DOCTYPE html>" .
            "<html lang='es'><head><meta charset='UTF-8'><style>" .
            "body{margin:0;padding:0;background:#f4f7fb;color:#2d3d51;font-family:Arial,sans-serif;}" .
            ".email-card{max-width:640px;margin:0 auto;padding:24px;background:#ffffff;border-radius:20px;box-shadow:0 24px 80px rgba(15,64,129,.12);}" .
            ".email-header{border-radius:18px;background:#e6f2f0;padding:18px;text-align:center;margin-bottom:22px;}" .
            ".email-header img{display:block;width:120px;height:auto;margin:0 auto 12px;border-radius:18px;box-shadow:0 6px 15px rgba(0,0,0,.08);}" .
            ".email-header h1{font-size:26px;color:#1a6b4f;margin:0 0 6px;}" .
            ".email-subtitle{margin:0;color:#2a5a47;font-size:14px;line-height:1.5;}" .
            ".email-text{font-size:16px;line-height:1.7;margin:0 0 22px;color:#455a75;}" .
            ".email-box{background:#f5fdf9;border:1px solid #d9e8e3;border-radius:14px;padding:18px 20px;margin-bottom:22px;}" .
            ".email-box h2{font-size:18px;color:#1a6b4f;margin:0 0 14px;}" .
            ".email-list{list-style:none;padding:0;margin:0;}" .
            ".email-list li{padding:10px 0;border-bottom:1px solid #e9f2ef;display:flex;justify-content:space-between;align-items:flex-start;}" .
            ".email-list li:last-child{border-bottom:none;}" .
            ".email-list .label{font-weight:700;color:#1a6b4f;width:160px;white-space:nowrap;}" .
            ".email-list .value{color:#3a4a62;flex:1;}" .
            ".email-footer{font-size:14px;color:#6b7b94;margin-top:16px;}" .
            "</style></head><body><div class='email-card'><div class='email-header'><img src='cid:logo_cid' alt='Logo'><h1>Equipo devuelto</h1><p class='email-subtitle'>Registro de devolucion</p></div>" .
            "<p class='email-text'>El colaborador <strong>$colaborador</strong> ha devuelto un equipo.</p>" .
            "<div class='email-box'><h2>Datos de la devolucion</h2><ul class='email-list'>" .
            "<li><span class='label'>Colaborador:</span><span class='value'>$colaborador</span></li>" .
            "<li><span class='label'>Correo:</span><span class='value'>$correo</span></li>" .
            "<li><span class='label'>Equipo:</span><span class='value'>$tipo_equipo</span></li>" .
            "<li><span class='label'>Modelo:</span><span class='value'>$modelo_equipo</span></li>" .
            "<li><span class='label'>Serial:</span><span class='value'>$serial_pc</span></li>" .
            "<li><span class='label'>Fecha de devolucion:</span><span class='value'>$fecha_devolucion</span></li>" .
            "<li><span class='label'>Hora de devolucion:</span><span class='value'>$hora_devolucion</span></li>" .
            "</ul></div>" .
            "<p class='email-footer'>Equipo devuelto correctamente.</p></div></body></html>";
        
        $resultado_email_admin = enviarCorreoDevolucion(ADMIN_EMAIL, ADMIN_NAME, $asunto_admin, $mensaje_admin);
        
        if ($resultado_email_colaborador === true && $resultado_email_admin === true) {
            $mensaje = "Devolucion registrada y correos enviados correctamente";
            $clase = 'alert--success';
        } else {
            $errores = [];
            if ($resultado_email_colaborador !== true) $errores[] = "Correo colaborador: $resultado_email_colaborador";
            if ($resultado_email_admin !== true) $errores[] = "Correo admin: $resultado_email_admin";
            $mensaje = "Devolucion registrada pero hubo error al enviar correos: " . implode(" | ", $errores);
            $clase = 'alert--warning';
        }
    } else {
        $mensaje = "Registro actualizado correctamente";
        $clase = 'alert--success';
    }

    echo "<div class='alert " . $clase . "'>";
    echo "<p class='alert__title'>Resultado</p>";
    echo "<p class='alert__text'>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</p>";
    echo "<p><a href='ver.php' class='alert__link'>Volver a lista de prestamos</a></p>";
    echo "</div>";
    include("includes/footer.php");
} else {
    echo "❌ Error al actualizar: " . $conexion->error . " <br><a href='ver.php'>Volver</a>";
}

$stmt->close();
$conexion->close();
?>
