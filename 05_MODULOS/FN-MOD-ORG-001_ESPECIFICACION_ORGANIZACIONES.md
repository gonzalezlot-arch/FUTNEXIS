# FN-MOD-ORG-001 — Especificación funcional: organizaciones

- **Versión documental:** 1.2
- **Estado:** EN_REVISION
- **Fecha de consolidación:** 2026-11-02
- **Base de trabajo conocida:** versión 1.1, WritingBlock de referencia 73184
- **Nota de integridad:** esta edición consolida las decisiones y reglas disponibles en el contexto del proyecto. No se presenta como copia literal del documento de trabajo anterior; debe compararse con el original antes de aprobarse.

## 1. Objetivo

Definir la gestión de organizaciones dentro de FUTNEXIS, incluyendo creación, propiedad, membresías, roles, invitaciones, configuración y suspensión.

## 2. Alcance

Incluye:
- Crear y consultar organizaciones.
- Administrar datos básicos y estado.
- Gestionar propietario, administradores y membresías.
- Invitar usuarios y controlar aceptación/vencimiento.
- Aplicar aislamiento entre organizaciones.
- Transferir la propiedad.
- Restringir operaciones según rol y estado.

No define todavía la matriz exhaustiva de permisos ni todos los campos de configuración institucional.

## 3. Actores y roles

- **Superadmin FUTNEXIS:** administración global; único rol autorizado para suspender organizaciones.
- **Propietario:** titular principal de la organización; administra la organización y puede transferir propiedad.
- **Administrador:** opera la organización según permisos asignados por el propietario.
- **Operador:** rol previsto en el modelo general; alcance específico pendiente.
- **Delegado y jugador:** roles del producto, no necesariamente administradores de organización.

Un usuario puede tener roles distintos en organizaciones diferentes.

## 4. Requisitos funcionales consolidados

| ID | Requisito |
|---|---|
| RF-ORG-001 | Permitir que cualquier usuario registrado cree una organización, incluso una persona independiente. |
| RF-ORG-002 | Al crear una organización, crear la membresía del usuario creador como propietario activo. |
| RF-ORG-003 | Permitir que un usuario pertenezca a varias organizaciones. |
| RF-ORG-004 | Permitir nombres de organización repetidos; cada organización debe tener identificador interno único. |
| RF-ORG-005 | Permitir configurar tipo de organización: organizador, club, escuela/academia, liga/asociación u otro. |
| RF-ORG-006 | Permitir al propietario asignar administradores y configurar sus permisos dentro de los límites de la plataforma. |
| RF-ORG-007 | Impedir que un administrador transfiera la propiedad o suspenda la organización. |
| RF-ORG-008 | Permitir transferir propiedad a otro usuario registrado, sujeto a aceptación del nuevo propietario. |
| RF-ORG-009 | Permitir que el propietario anterior permanezca como administrador si se le asigna ese rol. |
| RF-ORG-010 | Mantener un único propietario principal activo por organización. |
| RF-ORG-011 | Permitir invitaciones y registrar su estado. |
| RF-ORG-012 | Establecer vencimiento de invitaciones a los siete días. |
| RF-ORG-013 | Permitir al superadmin FUTNEXIS suspender una organización. |
| RF-ORG-014 | Mantener separados el estado de organización, el estado de membresía y el estado de invitación. |
| RF-ORG-015 | Validar en servidor la membresía, rol y permisos en cada consulta y operación protegida. |
| RF-ORG-016 | Aislar los datos de cada organización para impedir acceso cruzado no autorizado. |

## 5. Reglas de negocio

### 5.1 Organización
- Estados conocidos: activa, suspendida y pendiente de configuración.
- El nombre no es identificador único.
- El identificador interno sí debe ser único y estable.
- Tipos iniciales: organizador, club, escuela/academia, liga/asociación y otro.

### 5.2 Membresía
- Estados: pendiente, activa, suspendida y revocada.
- Una membresía identifica la relación de un usuario con una organización y su rol.
- Los permisos efectivos deben resolverse en el contexto de la organización seleccionada.
- La existencia de una membresía en una organización no concede acceso a otra.

### 5.3 Invitaciones
- Estados: pendiente, aceptada, rechazada, vencida y revocada.
- Vencen a los siete días.
- [PENDIENTE] Definir si el destinatario debe ser siempre un usuario registrado o si se permitirá invitar por correo a una persona que todavía no tenga cuenta.
- La aceptación debe comprobar que la invitación corresponde al usuario destinatario.

### 5.4 Propiedad
- Debe existir un único propietario principal activo.
- La transferencia requiere aceptación del nuevo propietario.
- La transferencia debe ser atómica: no dejar la organización sin propietario activo.
- El anterior propietario puede conservar acceso administrativo si se le asigna ese rol.
- La transferencia debe quedar auditada.

### 5.5 Suspensión
- Solo el superadmin FUTNEXIS puede suspender una organización.
- Un administrador o propietario no puede ejecutar la suspensión global definida para la plataforma.
- El comportamiento de acceso y reactivación se complementa con las reglas generales de plataforma y portal público.

## 6. Seguridad y auditoría

- Aplicar aislamiento multitenant en todas las operaciones.
- Validar permisos en backend; ocultar controles en frontend no constituye autorización.
- Evitar confiar en identificadores de organización enviados por el cliente sin comprobar membresía.
- Proteger operaciones de transferencia y administración mediante validaciones reforzadas.
- Registrar cambios de propietario, membresías, roles, invitaciones y suspensión.
- No exponer datos de una organización a usuarios de otra.

## 7. Criterios de aceptación iniciales

| ID | Criterio |
|---|---|
| CA-ORG-001 | Un usuario registrado puede crear una organización y queda como propietario activo. |
| CA-ORG-002 | Dos organizaciones pueden compartir nombre sin colisionar en su identificador interno. |
| CA-ORG-003 | Un usuario puede tener membresías independientes en varias organizaciones. |
| CA-ORG-004 | Un administrador no puede transferir propiedad ni suspender la organización. |
| CA-ORG-005 | Una transferencia no se completa sin aceptación del nuevo propietario. |
| CA-ORG-006 | La plataforma impide que una organización quede sin propietario principal activo. |
| CA-ORG-007 | Una invitación pendiente vence al cumplirse siete días. |
| CA-ORG-008 | Una solicitud sin membresía/permisos válidos es rechazada por el servidor. |
| CA-ORG-009 | Un usuario de una organización no puede consultar datos privados de otra sin autorización. |
| CA-ORG-010 | Solo el superadmin puede suspender una organización. |

## 8. Pendientes de definición

1. Matriz completa de permisos por rol y operación.
2. Invitaciones a usuarios existentes frente a invitaciones por correo a nuevos usuarios.
3. Límites de creación de organizaciones y controles antifraude.
4. Campos definitivos de contacto, ubicación, logotipo y datos institucionales.
5. Recuperación de propiedad ante pérdida de acceso o inactividad.
6. Cierre/eliminación de una organización y política de retención.
7. Procedimiento de reactivación y comunicación de suspensión.
8. Requisitos de autenticación reforzada para transferir propiedad.
9. Auditoría detallada y política de conservación.

## 9. Dependencias

- Núcleo de usuarios y autenticación.
- Modelo de roles y permisos.
- Arquitectura multitenant.
- Auditoría y seguridad.
- Portal público y módulo de torneos.

## 10. Historial

- 1.2 — 2026-11-02: consolidación en repositorio de las decisiones conocidas de la versión de trabajo 1.1; requiere comparación con el documento original.
