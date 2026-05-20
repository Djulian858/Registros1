# Sistema de Registro de Préstamo de Equipos

## Stack actual

- **Framework:** Laravel 13 (PHP 8.4)
- **Base de datos:** MySQL — base de datos `trabajo`
- **Correo:** Laravel Mail con SMTP Gmail (puerto 465 / SSL)
- **Credenciales:** gestionadas exclusivamente desde `.env`

---

## Estructura del proyecto

```
Registros1/
├── app/
│   ├── Http/Controllers/PrestamosController.php   ← lógica principal
│   ├── Mail/PrestamoRegistrado.php                ← correo de nuevo préstamo
│   ├── Mail/DevolucionRegistrada.php              ← correo de devolución
│   └── Models/Prestamo.php                        ← modelo Eloquent
├── database/
│   └── migrations/
│       └── ..._create_prestamos_table.php
├── public/                                        ← raíz del servidor web
│   ├── css/estilos.css
│   ├── js/script.js
│   └── img/
├── resources/
│   └── views/
│       ├── layouts/app.blade.php                  ← layout principal
│       ├── prestamos/index.blade.php              ← formulario de préstamo
│       ├── prestamos/ver.blade.php                ← lista de registros
│       ├── prestamos/resultado.blade.php          ← pantalla de resultado
│       └── emails/                               ← plantillas de correo
├── routes/web.php
└── .env                                           ← credenciales (no va al git)
```

---

## Configuración inicial (primera vez)

### 1. Copiar el archivo de entorno

```bash
cp .env.example .env
```

Luego edita `.env` con tus datos reales de base de datos y correo.

### 2. Instalar dependencias

```bash
composer install
```

### 3. Generar clave de aplicación

```bash
php artisan key:generate
```

### 4. Crear la tabla en MySQL

Asegúrate de que la base de datos `trabajo` existe, luego:

```bash
php artisan migrate
```

### 5. Levantar el servidor

```bash
php artisan serve
# → http://localhost:8000
```

---

## Variables del .env relevantes para este proyecto

| Variable | Descripción |
|---|---|
| `DB_DATABASE` | Nombre de la base de datos (por defecto: `trabajo`) |
| `MAIL_USERNAME` | Correo Gmail remitente |
| `MAIL_PASSWORD` | Contraseña de aplicación Gmail (entre comillas si tiene espacios) |
| `MAIL_FROM_ADDRESS` | Dirección del remitente |
| `APP_DEFAULT_ENTREGADO_POR` | Email predeterminado en el campo "Entregado por" |
| `APP_ADMIN_NAME` | Nombre del administrador que aparece en los correos |

---

## Columnas de la tabla `prestamos`

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | INT | Clave primaria |
| `colaborador` | VARCHAR | Nombre del colaborador |
| `correo_colaborador` | VARCHAR | Correo del colaborador |
| `serial_pc` | VARCHAR | Serial del equipo |
| `tipo_equipo` | VARCHAR | Portátil / Escritorio / Tablet |
| `modelo_equipo` | VARCHAR | Modelo del equipo |
| `fecha_entrega` | DATE | Fecha de entrega |
| `hora_entrega` | VARCHAR | Hora de entrega |
| `entregado_por` | VARCHAR | Correo de quien entrega |
| `observaciones_prestamo` | TEXT | Observaciones al momento de entregar |
| `fecha_devolucion` | DATE | Fecha de devolución (nullable) |
| `hora_devolucion` | VARCHAR | Hora de devolución (nullable) |
| `observaciones_devolucion` | TEXT | Observaciones al momento de devolver (nullable) |
| `created_at` / `updated_at` | TIMESTAMP | Gestionados por Laravel |

---

## Rutas disponibles

| Método | URL | Acción |
|---|---|---|
| GET | `/` | Formulario de nuevo préstamo |
| POST | `/prestamos` | Guardar préstamo |
| GET | `/ver` | Lista de todos los registros |
| PATCH | `/editar` | Actualizar devolución |
| DELETE | `/prestamos/{id}` | Eliminar registro |
