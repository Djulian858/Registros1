# Sistema de Registro de Préstamo de Equipos - Instrucciones de Actualización

## Cambios Realizados

Se han implementado todos los cambios solicitados:

### ✅ Cambios en la Interfaz

1. **Registro Editable**: Los registros ahora pueden editarse después de ser guardados
2. **Campos Editables**: 
   - Hora de devolución
   - Fecha de devolución  
   - Observaciones de devolución
   - Entregado por

3. **Cambios de Etiquetas**:
   - "Docente" → "Colaborador"
   - "Recibido por" → "Entregado por"

4. **Correo Estándar**: El campo de correo viene pre-llenado con "colaborador@institucion.edu.co"

5. **Reorden de Campos** (Primera Parte - Préstamo):
   - Colaborador (Prestado por)
   - Correo
   - Serial
   - Tipo de equipo
   - **Modelo de equipo** (NUEVO)
   - Fecha entrega
   - Hora entrega
   - Entregado por
   - Observaciones (Préstamo)

6. **Segunda Parte** (Devolución - Editable):
   - Fecha de devolución
   - Hora de devolución
   - Observaciones (Devolución)

7. **Observaciones en Ambos Lados**: 
   - Observaciones de préstamo
   - Observaciones de devolución

## Archivos Modificados/Creados

- `index.php` - Formulario actualizado con nueva estructura
- `guardar.php` - Script actualizado para guardar nuevos campos
- `ver.php` - Vista mejorada con funcionalidad de edición
- `editar.php` - NUEVO: Permite editar datos de devolución
- `eliminar.php` - NUEVO: Permite eliminar registros
- `migrar_bd.php` - NUEVO: Script de migración automática

## Paso 1: Ejecutar la Migración de Base de Datos

Accede a este archivo desde tu navegador:

```
http://localhost:8080/migrar_bd.php
```

Este script automáticamente:
- Detectará los cambios necesarios en la base de datos
- Renombrará las columnas antiguas
- Agregará nuevas columnas
- Eliminará columnas innecesarias
- Migrará datos antiguos si existen

## Paso 2: Usar el Sistema

### Crear un nuevo registro:
1. Ve a `http://localhost:8080/`
2. Llena los datos del PRÉSTAMO (obligatorios)
3. Opcionalmente, completa datos de la DEVOLUCIÓN
4. Haz clic en "Guardar Registro"

### Ver registros:
1. Haz clic en "Ver registros"
2. Se mostrarán todos los registros con dos secciones: PRÉSTAMO y DEVOLUCIÓN

### Editar devolución:
1. En la vista de registros, haz clic en "✏️ Editar Devolución"
2. Completa fecha, hora y observaciones de devolución
3. Haz clic en "✅ Guardar Cambios"

### Eliminar registro:
1. En la vista de registros, haz clic en "🗑️ Eliminar"
2. Confirma la eliminación

## Estructura de la Base de Datos (Columnas Finales)

```
- id (INT, primary key)
- colaborador (VARCHAR)
- correo_colaborador (VARCHAR)
- serial_pc (VARCHAR)
- tipo_equipo (VARCHAR)
- modelo_equipo (VARCHAR) - NUEVO
- fecha_entrega (DATE)
- hora_entrega (TIME)
- fecha_devolucion (DATE) - NUEVO
- hora_devolucion (TIME)
- entregado_por (VARCHAR) - RENOMBRADO
- observaciones_prestamo (TEXT) - NUEVO
- observaciones_devolucion (TEXT) - NUEVO
```

## Solución de Problemas

### Si la migración falla:
1. Asegúrate de que la base de datos "trabajo" existe
2. Verifica que el usuario "root" tiene permisos
3. Intenta acceder nuevamente a `migrar_bd.php`

### Si hay errores al guardar:
1. Verifica la conexión a la base de datos en `conexion.php`
2. Asegúrate de que la tabla prestamos existe
3. Ejecuta nuevamente `migrar_bd.php`

## Apoyo

Si tienes problemas, verifica en la consola del navegador (F12) o en los logs de PHP.

