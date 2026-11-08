# FN-MOD-CORE-001 — Especificación funcional: usuarios, autenticación y organizaciones

- Versión: 1.1
- Estado: EN_REVISION (la última versión aprobada es la 1.0; esta versión incorpora DEC-CORE-010 a DEC-CORE-014 y espera la revisión del propietario)
- Fecha: 2026-11-08
- Aprobación de la versión 1.0: aprobación expresa del responsable del producto en conversación.
- Alcance: reglas funcionales del núcleo de identidad, acceso, membresías, administración de cuentas y auditoría.

## 1. Objetivo

Definir el comportamiento funcional de las cuentas de usuario y su acceso seguro a FUTNEXIS, incluyendo la relación con múltiples organizaciones.

## 2. Registro e identidad

### RF-CORE-001 — Registro
El usuario podrá crear una cuenta mediante correo electrónico. La contraseña se establece después de verificar el correo (DEC-CORE-013).

### RN-CORE-001 — Correo único
- El correo será el identificador de acceso y será único globalmente en FUTNEXIS.
- Para comparar correos se eliminarán espacios al inicio y al final, se aplicará normalización Unicode NFC y comparación sin distinguir mayúsculas/minúsculas. Los correos que difieran por acentos son identidades diferentes (DEC-CORE-008 y DEC-CORE-010).
- Si ya existe una cuenta verificada, se rechazará el registro y se mostrará: «Este correo electrónico ya se encuentra registrado. Inicia sesión o recupera tu contraseña».
- Si la cuenta existente sigue pendiente de verificación, se enviará un nuevo enlace; un tercero no puede reservar un correo ajeno.
- La unicidad se garantizará también en backend y base de datos.

### RN-CORE-002 — Verificación de correo
- El registro quedará pendiente de verificación.
- FUTNEXIS enviará un enlace o código de verificación con vencimiento y uso único.
- Al usar el enlace, el propietario del correo establece su contraseña y la cuenta queda habilitada.
- No se permitirá iniciar sesión hasta verificar el correo.
- El usuario podrá solicitar un nuevo envío.

### RN-CORE-003 — Contraseña
- Longitud mínima: 8 caracteres.
- Debe contener al menos una mayúscula, una minúscula, un número y un carácter especial. Un espacio no cuenta como carácter especial.
- Longitud máxima efectiva: 72 bytes; las contraseñas más largas se rechazan.
- La política se validará en frontend y backend.
- La contraseña se almacenará mediante un algoritmo de hash seguro; nunca en texto plano.

## 3. Inicio de sesión y recuperación

### RF-CORE-002 — Inicio de sesión
El usuario iniciará sesión con correo y contraseña.

### RN-CORE-004 — Intentos fallidos
- Tras cinco intentos consecutivos fallidos se bloqueará temporalmente el inicio de sesión de la cuenta durante 15 minutos.
- El restablecimiento correcto de la contraseña desbloquea la cuenta (DEC-CORE-012).
- El control se realizará en backend.
- El mensaje no revelará si una cuenta existe ni información sensible.

### RF-CORE-003 — Recuperación de contraseña
- El usuario podrá solicitar un enlace de restablecimiento enviado al correo registrado.
- El enlace tendrá vencimiento y será de un solo uso.
- La nueva contraseña deberá cumplir la política vigente.
- Se notificará el cambio por correo.

### RN-CORE-005 — Invalidación de sesiones
Al cambiar o recuperar la contraseña se invalidarán todas las sesiones activas y se notificará al usuario.

## 4. Perfil y cambio de correo

### RF-CORE-004 — Cambio de correo
El usuario podrá solicitar el cambio de su correo desde el perfil. La solicitud no modificará inmediatamente el identificador vigente.

### RN-CORE-006 — Aprobación del cambio
- El cambio requiere aprobación de un usuario de nivel superior.
- Si el solicitante no es propietario principal, aprueba el propietario principal de su organización principal.
- Si el solicitante es propietario principal, deberá contactar al soporte de FUTNEXIS; el superadministrador realizará la aprobación.
- El nuevo correo deberá estar disponible globalmente y verificarse antes de aplicarse.
- Si se rechaza, el correo actual permanece sin cambios.
- Se notificará el resultado al solicitante y se auditará la solicitud y su resolución.

### RN-CORE-007 — Organización principal
La organización principal del usuario será la primera organización a la que se asoció. Esta relación deberá quedar registrada de forma inequívoca y no se inferirá del orden variable de consultas.

## 5. Organizaciones y membresías

### RF-CORE-005 — Membresía múltiple
Un usuario podrá pertenecer a varias organizaciones y tener un rol distinto en cada una.

### RN-CORE-008 — Autorización por organización
- Los permisos se determinan por la membresía y el rol del usuario en la organización activa.
- El backend validará membresía, estado y permisos en cada consulta y operación protegida.
- Los datos de una organización estarán aislados de los de otras organizaciones.

### RF-CORE-006 — Creación de organización
Cualquier usuario registrado y habilitado podrá crear una organización sin aprobación previa. Al crearla:
- Se creará automáticamente su membresía.
- El creador quedará como propietario principal activo.
- La organización tendrá un identificador interno único; el nombre visible no necesita ser único.

## 6. Sesiones y acciones sensibles

### RN-CORE-009 — Sesiones
- Una sesión inactiva expirará después de 30 minutos.
- El cierre de sesión normal finalizará la sesión actual.
- Existirá una opción para cerrar todas las sesiones activas de la cuenta.
- El cierre global invalidará también la sesión desde la que se ejecuta la acción.

### RN-CORE-010 — Verificación reforzada
Las acciones sensibles requerirán un código enviado al correo registrado. El código tendrá vencimiento, será de un solo uso y la operación solo continuará tras validarlo. Incluye, como mínimo, transferencias de propiedad y cambios de seguridad.

## 7. Suspensión y desactivación de cuentas

### RF-CORE-007 — Administración global de cuentas
Solo el superadministrador FUTNEXIS podrá suspender, reactivar o desactivar definitivamente una cuenta.

### RN-CORE-011 — Suspensión temporal
- Impide iniciar sesión y acceder a cualquier funcionalidad de FUTNEXIS.
- Es global, no limitada a una organización.
- Puede revertirse únicamente por el superadministrador.
- Se conservarán las membresías y referencias históricas necesarias.

### RN-CORE-012 — Desactivación definitiva
- Impide permanentemente el acceso a FUTNEXIS.
- No podrá reactivarse.
- Exige motivo obligatorio.
- Se registrarán responsable, fecha, hora y causal.
- El correo quedará reservado y no podrá utilizarse para registrar otra cuenta.
- Se conservarán los registros históricos y relaciones necesarios para la trazabilidad, sujetos a las reglas de retención y protección de datos.

### RN-CORE-013 — Notificaciones
Se notificará por correo al usuario cuando su cuenta sea suspendida, reactivada o desactivada definitivamente. La comunicación incluirá el motivo cuando corresponda, sin divulgar información administrativa confidencial.

### RF-CORE-008 — Historial de administración
El superadministrador podrá consultar el historial de suspensiones, reactivaciones y desactivaciones, con filtros por usuario, fecha, estado y motivo.

## 8. Auditoría

### RN-CORE-014 — Registro de auditoría
Se registrarán las acciones relevantes, incluyendo:
- Usuario responsable.
- Fecha y hora.
- Acción realizada.
- Organización involucrada, cuando corresponda.
- Resultado.
- Dirección IP y dispositivo, cuando estén disponibles.

Los registros serán de solo lectura para usuarios de las organizaciones y no podrán ser alterados por ellos.

### RN-CORE-015 — Acceso a auditoría
- Propietario principal: auditoría de su organización.
- Administradores autorizados: según permisos asignados.
- Superadministrador FUTNEXIS: auditoría global.

## 9. Solicitud de eliminación de datos

### RF-CORE-009 — Solicitud del titular
El usuario podrá solicitar la eliminación de sus datos personales.

### RN-CORE-016 — Tratamiento de la solicitud
La solicitud se evaluará conforme a las obligaciones legales y reglas de retención aplicables. Se eliminarán los datos que corresponda y, cuando sea necesario conservar trazabilidad deportiva, administrativa o legal, se conservarán únicamente los registros requeridos y se anonimizarán los datos personales cuando proceda. La solicitud, decisión y ejecución deberán quedar registradas. La eliminación no se ejecutará de forma que vulnere obligaciones de conservación.

## 10. Criterios de aceptación

| ID | Prueba | Resultado esperado |
|---|---|---|
| CA-CORE-001 | Registrar un correo nuevo | Cuenta pendiente de verificación. |
| CA-CORE-002 | Registrar un correo ya existente con otra capitalización o espacios externos | Registro rechazado por duplicidad. |
| CA-CORE-003 | Establecer, tras verificar el correo, una contraseña que incumple la política | Rechazo en frontend y backend. |
| CA-CORE-004 | Iniciar sesión sin verificar correo | Acceso denegado. |
| CA-CORE-005 | Verificar correo válido y establecer una contraseña | Cuenta habilitada para iniciar sesión. |
| CA-CORE-006 | Usar enlace de recuperación vencido o ya utilizado | Restablecimiento rechazado. |
| CA-CORE-007 | Cambiar o recuperar contraseña | Todas las sesiones anteriores quedan invalidadas. |
| CA-CORE-008 | Fallar cinco veces consecutivas | Bloqueo de inicio de sesión por 15 minutos. |
| CA-CORE-009 | Mantener sesión inactiva 30 minutos | Sesión expirada. |
| CA-CORE-010 | Cerrar sesión global | Todas las sesiones quedan invalidadas. |
| CA-CORE-011 | Asociar usuario a varias organizaciones | Roles y permisos independientes por organización. |
| CA-CORE-012 | Crear organización | Creador queda como propietario principal activo. |
| CA-CORE-013 | Solicitar cambio de correo como miembro | Solicitud pendiente de aprobación del propietario principal. |
| CA-CORE-014 | Propietario solicita cambio de correo | Requiere gestión de soporte y aprobación del superadministrador. |
| CA-CORE-015 | Aprobar cambio a correo ya utilizado | Operación rechazada; correo vigente no cambia. |
| CA-CORE-016 | Suspender cuenta | Acceso global bloqueado; datos históricos preservados. |
| CA-CORE-017 | Desactivar definitivamente | Acceso bloqueado sin reactivación y correo reservado. |
| CA-CORE-018 | Consultar auditoría con rol no autorizado | Acceso denegado. |
| CA-CORE-019 | Ejecutar acción sensible sin validar código | Operación no ejecutada. |
| CA-CORE-020 | Solicitar eliminación de datos | Solicitud evaluada y tratada según retención y obligaciones legales. |

## 11. Dependencias y asuntos de implementación

- La matriz detallada de permisos por rol y organización se especificará en el módulo de autorización.
- Los mecanismos concretos de correo, almacenamiento de sesiones, algoritmo de hash, proveedor de identidad y tecnología se definirán en arquitectura; esta especificación no impone un stack.
- Los tiempos de retención, anonimización y respuesta a solicitudes de eliminación deberán alinearse con la normativa aplicable y la política de privacidad.
- Los mensajes, plantillas y diseño de pantallas se detallarán en UX/UI.
- Esta especificación no sustituye las reglas específicas del módulo de organizaciones; las complementa para el núcleo de identidad y acceso.

## 12. Historial de cambios

- 1.1 — 2026-11-08: EN_REVISION. Incorpora DEC-CORE-010 a DEC-CORE-014: la contraseña se establece después de verificar el correo, normalización NFC e identidades distintas por acentos, un espacio no es carácter especial, máximo de 72 bytes y desbloqueo por restablecimiento.
- 1.0 — 2026-11-03: primera versión aprobada; consolida las decisiones funcionales confirmadas para usuarios, autenticación, organizaciones, sesiones, administración de cuentas, auditoría y solicitudes de eliminación de datos.
