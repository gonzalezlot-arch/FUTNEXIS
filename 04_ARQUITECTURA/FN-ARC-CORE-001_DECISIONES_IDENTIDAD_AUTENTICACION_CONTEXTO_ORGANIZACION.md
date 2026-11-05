# FN-ARC-CORE-001 — Decisiones de arquitectura: identidad, autenticación y contexto de organización

- **Proyecto:** FUTNEXIS
- **Código:** FN-ARC-CORE-001
- **Versión:** 0.3
- **Estado:** APROBADO
- **Módulo:** CORE
- **Fecha:** 2026-11-04

## 1. Objetivo

Establecer las decisiones técnicas que orientarán la implementación de identidad, autenticación, sesiones, organizaciones y contexto de acceso de FUTNEXIS. Estas decisiones deben respetar los requisitos funcionales aprobados en `FN-MOD-CORE-001`.

## 2. Arquitectura general

Se adopta una arquitectura de **monolito modular con Laravel**, manteniendo separación lógica entre módulos y responsabilidades.

| Componente | Decisión |
|---|---|
| Backend | Laravel 13 |
| Lenguaje backend | PHP |
| Versión objetivo en hosting | PHP 8.5 |
| Base de datos | MariaDB |
| Frontend | Blade, Tailwind CSS y JavaScript |
| Aplicación | Web adaptable y PWA |
| Autenticación | Laravel nativo con flujos personalizados |
| Despliegue | Manual mediante cPanel |
| Administración de base de datos | phpMyAdmin |
| Repositorio | GitHub |

La arquitectura debe permitir ampliar funcionalidades sin convertir el sistema en una aplicación monolítica desorganizada.

## 3. Autenticación

Se aprueba implementar los flujos de autenticación mediante funcionalidades nativas de Laravel y lógica propia.

**No se utilizará Laravel Fortify como solución de autenticación del proyecto.**

Motivos:
- El registro no debe iniciar sesión automáticamente.
- La verificación del correo debe ser obligatoria antes del acceso.
- Los enlaces de verificación deben ser de un solo uso.
- El control de intentos debe ajustarse a las reglas de FUTNEXIS.
- No se permitirá la opción «Recordarme».
- Los cambios de correo requieren aprobación.
- Las acciones sensibles utilizan códigos enviados por correo.

Se utilizarán los mecanismos de seguridad y hashing de Laravel, evitando desarrollar mecanismos criptográficos propios.

## 4. Registro, verificación y recuperación

Se implementarán los siguientes flujos:

- Registro con correo electrónico único y normalizado.
- Verificación obligatoria del correo antes del primer acceso.
- Enlaces de verificación con vencimiento de 24 horas y un solo uso.
- Recuperación de contraseña mediante enlace con vencimiento de 60 minutos y un solo uso.
- Códigos de autorización para acciones sensibles con vencimiento de 10 minutos.
- La generación de un nuevo enlace o código del mismo tipo invalida el anterior.
- Las validaciones de vencimiento y uso se realizarán en el servidor.

Las contraseñas cumplirán los requisitos funcionales aprobados: mínimo ocho caracteres, mayúscula, minúscula, número y carácter especial.

## 5. Sesiones y protección de acceso

Se establece:

- Duración máxima de inactividad: 30 minutos.
- Sin funcionalidad «Recordarme».
- La actividad de una sesión no extenderá las demás sesiones del usuario.
- Los cambios o recuperaciones de contraseña invalidarán todas las sesiones.
- Las cuentas suspendidas no podrán continuar utilizando la aplicación.
- El estado de la cuenta se verificará también durante las peticiones autenticadas, no únicamente durante el inicio de sesión.

Se utilizará inicialmente almacenamiento de sesiones en base de datos, sujeto a validación operativa en el hosting.

El bloqueo por intentos fallidos seguirá la regla funcional de cinco intentos consecutivos y quince minutos de bloqueo, con controles adicionales por IP y dispositivo.

## 6. Organizaciones y contexto de acceso

FUTNEXIS será multiorganización. La relación entre usuarios y organizaciones se administrará mediante membresías independientes.

Se establece:

- Un usuario puede pertenecer a varias organizaciones.
- Puede tener un rol diferente en cada organización.
- Los permisos dependen de la membresía y del contexto de organización activo.
- El backend debe comprobar membresía, estado y permisos en cada petición protegida.
- No se permitirá acceder a información de otra organización modificando una URL o identificador.
- La primera organización a la que se formalice la vinculación será registrada como organización principal histórica.
- Las invitaciones pendientes no constituyen membresías activas.
- Un usuario podrá registrarse e ingresar sin pertenecer todavía a una organización.
- La organización activa se resolverá según las reglas funcionales aprobadas para el contexto de acceso.

La autorización detallada por roles y permisos se desarrollará en el módulo correspondiente.

## 7. Superadministración y auditoría

El primer superadministrador se creará mediante un procedimiento de instalación controlado, no mediante registro público.

La administración global de cuentas estará restringida al superadministrador autorizado.

Se implementarán:

- Suspensión y reactivación de cuentas.
- Desactivación definitiva con motivo obligatorio.
- Restricción de acceso en cada petición.
- Registro de acciones sensibles.
- Historial de cambios de estado.
- Auditoría de usuario, fecha, acción, organización, resultado e información técnica disponible.

Los registros de auditoría no podrán ser modificados por usuarios de las organizaciones.

## 8. Base de datos

Se utilizará una base de datos exclusiva para FUTNEXIS.

**Producción:**
- Base de datos: `gltechso_futnexis`
- Motor: MariaDB 11.4.13
- Cotejamiento: `utf8mb4_unicode_ci`

**Desarrollo local:**
- Base de datos independiente: `futnexis`
- Motor local: MariaDB 10.11.16

Los entornos mantendrán bases independientes.

Las migraciones de Laravel serán la fuente de verdad del esquema. Debido a que el hosting no dispone de terminal ni Composer, el procedimiento de producción utilizará scripts SQL generados y revisados a partir de las migraciones, aplicados mediante phpMyAdmin.

No se ejecutarán migraciones destructivas ni se modificará manualmente el esquema de producción sin respaldo y revisión.

## 9. Correo electrónico

Se utilizará el servicio SMTP del hosting.

| Parámetro | Configuración |
|---|---|
| Servidor | `mail.gltechsolutions.com` |
| Puerto | 465 |
| Seguridad | SSL/TLS |
| Remitente | `notificaciones@gltechsolutions.com` |
| Usuario SMTP | Dirección de correo completa |

La contraseña SMTP se almacenará exclusivamente en variables de entorno.

**Estado:** configuración definida, pendiente de prueba operativa de envío y recepción.

## 10. Hosting y despliegue

El despliegue inicial será manual mediante cPanel, Administrador de archivos, FTP y phpMyAdmin.

Condiciones conocidas del hosting:

- PHP 8.5.
- MariaDB 11.4.13.
- 10 GB de almacenamiento.
- 500 GB de transferencia reportada.
- Certificado SSL activo.
- Sin acceso SSH ni terminal.
- Sin Composer disponible en el servidor.

El proceso de despliegue deberá contemplar:

1. Respaldo previo de archivos y base de datos.
2. Preparación local de dependencias y recursos compilados.
3. Transferencia de archivos mediante FTP o Administrador de archivos.
4. Configuración de variables de entorno fuera del repositorio.
5. Aplicación controlada de SQL revisado.
6. Verificación de permisos y almacenamiento.
7. Pruebas de funcionamiento y procedimiento de reversión.

La disponibilidad de tareas programadas, enlaces simbólicos y procesos en segundo plano queda pendiente de validación.

## 11. Configuración inicial de servicios

Para el primer despliegue se utilizarán configuraciones conservadoras:

- Caché basada inicialmente en archivos.
- Colas inicialmente síncronas.
- Sesiones inicialmente almacenadas en base de datos.

Estas configuraciones podrán evolucionar cuando se validen las capacidades reales del hosting y las necesidades operativas de FUTNEXIS.

## 12. Seguridad y gestión de secretos

Se establece como obligatorio:

- No almacenar credenciales en GitHub.
- No incluir archivos `.env` en el repositorio.
- Mantener configuraciones independientes por entorno.
- Validar permisos y acceso desde el backend.
- Proteger las operaciones sensibles.
- Evitar exponer información privada en respuestas públicas.
- Mantener trazabilidad de operaciones administrativas.

## 13. Validaciones pendientes antes de producción

La aprobación de esta arquitectura no significa que los servicios hayan superado pruebas operativas.

Antes del despliegue se deberá validar:

1. Compatibilidad real de Laravel 13 con el entorno PHP 8.5 del hosting.
2. Envío y recepción mediante SMTP.
3. Importación de SQL desde phpMyAdmin.
4. Permisos de almacenamiento y archivos.
5. Configuración de sesiones y caché.
6. Disponibilidad de tareas programadas y procesos en segundo plano.
7. Acceso seguro a la aplicación mediante HTTPS.
8. Procedimiento de respaldo y recuperación.

## 14. Criterio de aprobación

Esta arquitectura queda aprobada como referencia técnica para desarrollar el módulo CORE de FUTNEXIS.

Las decisiones funcionales aprobadas en `FN-MOD-CORE-001` prevalecen sobre cualquier comportamiento predeterminado de Laravel o de sus dependencias.

Cualquier cambio de arquitectura deberá documentarse, justificarse y aprobarse antes de implementarse.

---

**Fin del documento.**
