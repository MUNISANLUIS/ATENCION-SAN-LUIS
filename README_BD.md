# Documentación de Base de Datos - Sistema de Incidencias

## Configuración de Conexión

```
DB_CONNECTION=sqlsrv
DB_HOST=GDATASERVER\GDATA2014
DB_PORT=1433
DB_DATABASE=DB_Tickets
DB_USERNAME=sa
DB_PASSWORD=123456
```

## Tablas de la Base de Datos

### Tabla: area

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| nombre | varchar | No | NULL | 200 |
| estado | varchar | No | NULL | 1 |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_creacion | datetime | Sí | NULL | N/A |
| usuario_actualizacion | varchar | Sí | NULL | 100 |
| fecha_actualizacion | datetime | Sí | NULL | N/A |

**Clave Primaria:** id

---

### Tabla: categoria_incidencia

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | varchar | No | NULL | 36 |
| id_categoria_padre | varchar | No | NULL | 36 |
| nombre | varchar | No | NULL | 200 |
| estado | varchar | No | NULL | 1 |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_creacion | datetime | Sí | NULL | N/A |
| usuario_actualizacion | varchar | Sí | NULL | 100 |
| fecha_actualizacion | datetime | Sí | NULL | N/A |
| formato_path | varchar | Sí | NULL | -1 |
| tipo_incidencia | int | Sí | NULL | N/A |
| orden | int | Sí | NULL | N/A |

**Clave Primaria:** id

---

### Tabla: sysdiagrams

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| name | nvarchar | No | NULL | 128 |
| principal_id | int | No | NULL | N/A |
| diagram_id | int | No | NULL | N/A |
| version | int | Sí | NULL | N/A |
| definition | varbinary | Sí | NULL | -1 |

**Clave Primaria:** diagram_id

---

### Tabla: telegram_receptores

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| nombre | nvarchar | No | NULL | 150 |
| telegram_chat_id | nvarchar | No | NULL | 50 |
| usuario_creacion | nvarchar | No | NULL | 100 |
| usuario_actualizacion | nvarchar | Sí | NULL | 100 |
| estado | tinyint | No | ((1)) | N/A |
| created_at | datetime | Sí | NULL | N/A |
| updated_at | datetime | Sí | NULL | N/A |

**Clave Primaria:** id

---

### Tabla: ticket

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| correlativo | varchar | No | NULL | 6 |
| id_area | int | No | NULL | N/A |
| nombres | varchar | No | NULL | 200 |
| id_tipo_incidencia | int | No | NULL | N/A |
| id_sub_incidencia | int | Sí | NULL | N/A |
| descripcion | varchar | Sí | NULL | -1 |
| estado | varchar | No | NULL | 36 |
| id_usuario | int | Sí | NULL | N/A |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_creacion | datetime | Sí | NULL | N/A |
| usuario_actualizacion | varchar | Sí | NULL | 100 |
| fecha_actualizacion | datetime | Sí | NULL | N/A |
| estacion_creacion | varchar | Sí | NULL | 30 |
| respuesta | nvarchar | Sí | NULL | -1 |
| tiempo_respuesta | varchar | Sí | NULL | 50 |

**Clave Primaria:** id

---

### Tabla: ticket_auditoria

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| id_ticket | int | No | NULL | N/A |
| correlativo | varchar | No | NULL | 6 |
| id_area | int | No | NULL | N/A |
| area_nombre | varchar | Sí | NULL | 200 |
| nombres | varchar | No | NULL | 200 |
| id_tipo_incidencia | int | No | NULL | N/A |
| tipo_incidencia_nombre | varchar | Sí | NULL | 250 |
| id_sub_incidencia | int | Sí | NULL | N/A |
| sub_incidencia_nombre | varchar | Sí | NULL | 250 |
| descripcion | varchar | Sí | NULL | -1 |
| estado | varchar | No | NULL | 36 |
| id_usuario | int | Sí | NULL | N/A |
| usuario_nombre | varchar | Sí | NULL | 200 |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_creacion | datetime | Sí | NULL | N/A |
| usuario_actualizacion | varchar | Sí | NULL | 100 |
| fecha_actualizacion | datetime | Sí | NULL | N/A |
| estacion_creacion | varchar | Sí | NULL | 30 |
| respuesta | nvarchar | Sí | NULL | -1 |
| accion | varchar | No | ('UPDATE') | 50 |
| fecha_auditoria | datetime | No | (getdate()) | N/A |
| id_usuario_auditoria | int | Sí | NULL | N/A |
| estado_nombre | varchar | Sí | NULL | 150 |

**Clave Primaria:** id

---

### Tabla: tipo_estado

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| nombre | varchar | No | NULL | 200 |
| estado | varchar | No | NULL | 1 |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_creacion | datetime | Sí | NULL | N/A |
| usuario_actualizacion | varchar | Sí | NULL | 100 |
| fecha_actualizacion | datetime | Sí | NULL | N/A |
| orden | int | Sí | NULL | N/A |

**Clave Primaria:** id

---

### Tabla: tipo_incidencia

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| nombres | varchar | No | NULL | 250 |
| fecha_creacion | datetime | Sí | NULL | N/A |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_actualizacion | datetime | Sí | NULL | N/A |
| usuario_actualizacion | varchar | Sí | NULL | 100 |
| estado | int | Sí | NULL | N/A |

**Clave Primaria:** id

---

### Tabla: tipo_usuario_rol

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| nombre | varchar | No | NULL | 200 |
| estado | varchar | No | NULL | 1 |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_creacion | datetime | Sí | NULL | N/A |
| usuario_actualizacion | varchar | Sí | NULL | 100 |
| fecha_actualizacion | datetime | Sí | NULL | N/A |

**Clave Primaria:** id

---

### Tabla: users

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | bigint | No | NULL | N/A |
| name | nvarchar | No | NULL | 255 |
| email | nvarchar | No | NULL | 255 |
| email_verified_at | datetime | Sí | NULL | N/A |
| password | nvarchar | No | NULL | 255 |
| remember_token | nvarchar | Sí | NULL | 100 |
| created_at | datetime | Sí | NULL | N/A |
| updated_at | datetime | Sí | NULL | N/A |
| estado | varchar | No | NULL | 1 |
| usuario_creacion | int | Sí | NULL | N/A |
| fecha_creacion | datetime | Sí | NULL | N/A |
| id_tipo_usuario_rol | int | Sí | NULL | N/A |
| usuario_actualizacion | int | Sí | NULL | N/A |
| fecha_actualizacion | datetime | Sí | NULL | N/A |
| username | varchar | Sí | NULL | 255 |
| id_tipo_incidente | int | Sí | NULL | N/A |

**Clave Primaria:** id

---

### Tabla: usuarios

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| nombres | varchar | No | NULL | 200 |
| contraseña | varchar | No | NULL | 200 |
| id_tipo_usuario | varchar | No | NULL | 36 |
| estado | varchar | Sí | NULL | 1 |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_creacion | datetime | Sí | NULL | N/A |
| usuario_actualizacion | varchar | Sí | NULL | 100 |
| fecha_actualizacion | datetime | Sí | NULL | N/A |

**Clave Primaria:** id

---

### Tabla: equipo

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| tipo | varchar | No | NULL | 50 |
| codigo | varchar | No | NULL | 50 |
| marca | varchar | Sí | NULL | 100 |
| modelo | varchar | Sí | NULL | 100 |
| estado | varchar | No | '1' | 1 |
| id_area | int | Sí | NULL | N/A |
| id_usuario | int | Sí | NULL | N/A |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_creacion | datetime | Sí | NULL | N/A |

**Clave Primaria:** id

---

---

### Tabla: reserva_equipo

| Columna | Tipo de Dato | Nulo | Por Defecto | Longitud Máxima |
|--------|-------------|------|-------------|----------------|
| id | int | No | NULL | N/A |
| id_equipo | int | No | NULL | N/A |
| id_usuario | int | No | NULL | N/A |
| fecha_inicio | datetime | No | NULL | N/A |
| fecha_fin | datetime | No | NULL | N/A |
| motivo | varchar | Sí | NULL | 500 |
| estado | varchar | No | 'PENDIENTE' | 50 |
| usuario_creacion | varchar | Sí | NULL | 100 |
| fecha_creacion | datetime | Sí | NULL | N/A |
| usuario_aprobacion | varchar | Sí | NULL | 100 |
| fecha_aprobacion | datetime | Sí | NULL | N/A |

**Clave Primaria:** id

---

## Resumen de Tablas

El sistema de incidencias contiene las siguientes tablas principales:

1. **area** - Gestión de áreas de trabajo
2. **categoria_incidencia** - Categorización de incidencias con estructura jerárquica
3. **sysdiagrams** - Diagramas de base de datos (sistema)
4. **telegram_receptores** - Configuración de notificaciones por Telegram
5. **ticket** - Tickets de incidencias principales
6. **ticket_auditoria** - Registro de auditoría de cambios en tickets
7. **tipo_estado** - Estados posibles para los tickets
8. **tipo_incidencia** - Tipos de incidencias disponibles
9. **tipo_usuario_rol** - Roles de usuario del sistema
10. **users** - Usuarios del sistema (Laravel)
11. **usuarios** - Usuarios del sistema (legado)
12. **equipo** - Inventario básico de equipos (computadora, laptop, proyector, etc.)
13. **ticket_equipo** - Relación simple entre tickets y equipos afectados
14. **reserva_equipo** - Sistema de reservas de equipos por tiempo y fecha

## Relaciones Principales

- Los **tickets** están relacionados con **áreas**, **tipos de incidencia**, y **usuarios**
- La tabla **ticket_auditoria** mantiene un historial de cambios en los tickets
- **categoria_incidencia** tiene una estructura jerárquica mediante `id_categoria_padre`
- Los **usuarios** tienen roles definidos en **tipo_usuario_rol**
- Los **equipos** tienen un tipo simple y se asignan a **áreas** y **usuarios**
- **ticket_equipo** vincula tickets con equipos de forma simple para seguimiento básico
- **reserva_equipo** permite a usuarios reservar equipos en fechas específicas con aprobación

---
*Documentación generada automáticamente el 20 de abril de 2026*
