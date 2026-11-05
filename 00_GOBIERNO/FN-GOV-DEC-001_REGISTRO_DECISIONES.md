# FN-GOV-DEC-001 — Registro maestro de decisiones

- Versión: 2.6
- Estado: EN_REVISION
- Fecha de corte: 2026-11-04

## Decisiones generales

| ID | Decisión | Estado |
|---|---|---|
| PROD-001 | FUTNEXIS es una plataforma SaaS multiempresa para gestionar torneos de fútbol. | [CONFIRMADO] |
| PROD-002 | Modalidades: fútbol 5, 6, 7, 9 y 11. | [CONFIRMADO] |
| PROD-003 | Modelo comercial mixto: suscripciones y cobro por torneo. | [CONFIRMADO] |
| PROD-004 | Web adaptable y PWA desde el inicio. | [CONFIRMADO] |
| PROD-005 | Lanzamiento mediante piloto/MVP reducido y validación en torneos reales. | [CONFIRMADO] |
| GOV-001 | Desarrollo modular con aprobación individual. | [CONFIRMADO] |
| GOV-002 | No iniciar un módulo sin especificación y criterios de aceptación aprobados. | [CONFIRMADO] |
| GOV-003 | GitHub es la fuente maestra documental. | [CONFIRMADO] |

## Decisiones de arquitectura

### DEC-ARC-CORE-001 — Arquitectura de identidad, autenticación y contexto de organización
[APROBADO — 2026-11-04]
- Se adopta una arquitectura de monolito modular con Laravel 13, PHP y MariaDB, frontend Blade, Tailwind CSS y JavaScript, con aplicación web adaptable y PWA.
- La autenticación se implementará con mecanismos nativos de Laravel y flujos personalizados; no se utilizará Laravel Fortify.
- Se aplicarán las reglas CORE de verificación obligatoria del correo, enlaces y códigos de un solo uso, recuperación, sesiones con 30 minutos de inactividad, sin «Recordarme» y controles de intentos fallidos.
- La autorización será multiorganización, basada en membresías, rol y contexto activo, con validación en backend en cada petición protegida.
- El despliegue inicial será manual mediante cPanel, FTP/Administrador de archivos y phpMyAdmin. Las migraciones Laravel serán la fuente de verdad y se aplicarán mediante SQL revisado en producción.
- Se utilizará la base exclusiva de producción `gltechso_futnexis`, MariaDB 11.4.13 y cotejamiento `utf8mb4_unicode_ci`; el entorno local conservará su base independiente.
- El correo utilizará el SMTP del hosting; la configuración está definida, pero el envío y recepción siguen pendientes de prueba operativa.
- Sesiones en base de datos, caché en archivos y colas síncronas son configuraciones iniciales sujetas a validación de capacidades del hosting.
- La especificación completa y sus validaciones pendientes se documentan en `FN-ARC-CORE-001` versión 0.3.

## Decisiones del portal público
El detalle se mantiene en FN-GOV-DEC-002.

- DEC-PORT-001 a 008: redacción literal pendiente de recuperación/verificación. No inventar.
- DEC-PORT-009 a 035: decisiones conocidas en el contexto del proyecto; falta consolidación documental íntegra.
- DEC-PORT-036 a 043: aprobadas expresamente el 2026-11-02.

## Decisiones del módulo de torneos

### DEC-TOR-001 — Configuración y modificación del marcador administrativo
[CONFIRMADO — 2026-11-02]
- El organizador configura el marcador administrativo común al crear el torneo.
- Durante la competición puede modificarlo mediante una acción autorizada y auditada.
- El nuevo valor aplica a las resoluciones administrativas futuras.
- Los resultados administrativos ya aplicados no se modifican automáticamente.
- La corrección de un resultado anterior requiere un procedimiento explícito, autorizado y auditado.

### DEC-TOR-002 — Roles autorizados para retiro y descalificación
[CONFIRMADO — 2026-11-02]
- Pueden ejecutar el retiro/descalificación: propietario de la organización; administrador con permiso de gestión deportiva; organizador o director del torneo con permiso específico asignado para esa competición.
- El operador de resultados puede registrar incidencias o solicitudes, pero no ejecutar por sí mismo el retiro/descalificación.
- La matriz granular de permisos para resolver partidos y seleccionar equipos en plazas vacantes se definirá en la especificación operativa.

### DEC-TOR-003 — Elegibilidad de equipos sustitutos ante doble exclusión
[CONFIRMADO — 2026-11-02]
Un equipo previamente eliminado podrá ser seleccionado para ocupar una instancia o plaza vacante si:
- Participó en el mismo torneo.
- Fue eliminado en una instancia anterior.
- Cumple las condiciones deportivas y reglamentarias de la ronda.
- No tiene una exclusión o sanción que le impida continuar.
La selección corresponde al organizador autorizado y debe quedar auditada.

### DEC-TOR-004 — Resultado administrativo del partido que motiva el retiro
[CONFIRMADO — 2026-11-02]
Cuando la decisión de retiro/descalificación se presenta respecto de un partido ya disputado, únicamente ese encuentro se sustituye por el marcador administrativo común a favor del rival. Se recalculan las métricas colectivas de ese encuentro. Los demás partidos anteriores conservan sus resultados y estadísticas originales. El marcador original del encuentro afectado se conserva en auditoría y no genera estadísticas individuales como resultado administrativo.

### DEC-TOR-005 — Partidos futuros pendientes tras retiro/descalificación
[CONFIRMADO — 2026-11-02]
Todos los partidos futuros pendientes del equipo retirado/descalificado contra equipos activos se resuelven administrativamente con el marcador común del torneo, siempre a favor del equipo activo, sin importar la localía. Se aplican los efectos colectivos correspondientes conforme a DEC-PORT-050, sin estadísticas individuales. Los partidos pendientes entre dos equipos excluidos no reciben marcador ni efectos estadísticos.

### DEC-TOR-006 — Avance del rival en fase eliminatoria tras retiro/descalificación
[CONFIRMADO — 2026-11-02]
Cuando un equipo sea retirado o descalificado durante una fase eliminatoria, avanzará a la siguiente fase el rival contra el que se encontraba enfrentado en esa instancia. El torneo continuará con normalidad conforme al calendario y cuadro resultantes. Esta regla no aplica a la doble exclusión del enfrentamiento: en ese caso se mantiene el procedimiento de DEC-PORT-049/051, sin avance automático y con resolución de la plaza por el organizador.

### DEC-TOR-007 — Marcador administrativo en avance eliminatorio
[CONFIRMADO — 2026-11-02]
Cuando un equipo sea retirado o descalificado en una fase eliminatoria y su rival activo avance a la siguiente fase, el enfrentamiento quedará registrado oficialmente con el marcador administrativo común configurado para el torneo, aplicado a favor del rival activo. Se actualizarán las métricas colectivas que correspondan y no se generarán estadísticas individuales. La doble exclusión conserva el tratamiento especial de DEC-PORT-049/051 y no recibe marcador ni avance automático.

### DEC-TOR-008 — Declaración de campeón por retiro/descalificación en la final
[CONFIRMADO — 2026-11-02]
Si un equipo es retirado o descalificado durante la final y su rival permanece activo, el rival será declarado campeón del torneo. El partido final quedará registrado con el marcador administrativo común vigente, aplicado a favor del equipo que obtiene el título. Se actualizarán las métricas colectivas correspondientes y no se generarán estadísticas individuales. Si ambos finalistas están excluidos, se mantiene el procedimiento de doble exclusión: no se declara campeón automáticamente ni se asigna marcador; el organizador resuelve la situación conforme a las decisiones aplicables.

### DEC-TOR-009 — Retiro/descalificación después del cierre oficial del torneo
[CONFIRMADO — 2026-11-02]
Una vez que el torneo haya sido declarado oficialmente finalizado, FUTNEXIS no aplicará automáticamente cambios deportivos, administrativos ni estadísticos por un retiro o descalificación posterior. Cualquier decisión o medida sobre esa situación corresponderá exclusivamente al organizador del torneo.

### DEC-TOR-010 — No reincorporación de equipos retirados
[CONFIRMADO — 2026-11-02]
Ningún equipo que sea dado de baja del torneo, cualquiera que sea el motivo (retiro voluntario, abandono, descalificación u otra causa), podrá reintegrarse a esa misma edición, aunque la competición continúe en curso. La baja será definitiva para esa edición.

### DEC-TOR-011 — Partido pendiente entre dos equipos dados de baja
[CONFIRMADO — 2026-11-02]
Cuando ambos equipos de un partido pendiente estén dados de baja, el encuentro se registrará con el estado «No disputado por doble baja». No se asignará ganador, marcador, puntos ni efectos estadísticos colectivos o individuales. En fases eliminatorias, la plaza o continuidad del cuadro se resolverá por separado conforme al procedimiento de doble exclusión y a la decisión auditada del organizador; el estado del partido no otorga avance automático.

### DEC-TOR-012 — Visualización y registro del partido no disputado por doble baja
[CONFIRMADO — 2026-11-02]
- En el calendario, el encuentro permanece visible dentro de su jornada con los nombres y escudos de ambos equipos, sin marcador y con el estado «No disputado por doble baja».
- En el historial de partidos, se conserva el encuentro y el motivo de no disputa para consulta posterior.
- No se genera acta deportiva. Se conserva un registro administrativo de la situación.
- El encuentro no suma partidos jugados, puntos ni goles a ninguno de los equipos y no genera estadísticas colectivas ni individuales.

### DEC-TOR-013 — Detalle de la causa de no disputa en informes y reportes
[CONFIRMADO — 2026-11-02]
- Los informes administrativos y reportes descargables mostrarán el partido no disputado e identificarán el estado de baja de cada equipo por separado.
- Si ambos equipos están retirados, la indicación será «Partido no disputado por retiro de ambos equipos».
- Si ambos están descalificados, será «Partido no disputado por descalificación de ambos equipos».
- Si uno está retirado y el otro descalificado, será «Partido no disputado por baja de ambos equipos», detallando individualmente cuál equipo está retirado y cuál descalificado.
- Se conservarán los nombres de ambos equipos y el motivo/estado de baja correspondiente a cada uno. No se asignará marcador, ganador ni efectos estadísticos.

## Pendientes
1. Verificar el texto original de DEC-PORT-001 a 008 si aparece una fuente histórica.
2. Armonizar especificaciones funcionales y UX con DEC-PORT-036 a 043.
3. Actualizar este registro ante cada nueva decisión.

## Historial
- 2.6 — 2026-11-04: incorpora DEC-ARC-CORE-001, aprobada junto con FN-ARC-CORE-001 v0.3, sobre arquitectura técnica del módulo CORE.
- 2.5 — 2026-11-02: incorpora DEC-TOR-013 sobre identificación individual del estado de baja de cada equipo en informes administrativos y reportes descargables.
- 2.4 — 2026-11-02: incorpora DEC-TOR-012 sobre visualización en calendario e historial y registro administrativo del partido no disputado por doble baja.
- 2.3 — 2026-11-02: incorpora DEC-TOR-011: partidos pendientes entre dos equipos dados de baja quedan «No disputado por doble baja», sin marcador ni efectos estadísticos.
- 2.2 — 2026-11-02: amplía DEC-TOR-010: ningún equipo dado de baja puede reintegrarse, cualquiera que sea el motivo.
- 2.1 — 2026-11-02: aclara DEC-TOR-010: la prohibición de reincorporación aplica a todo equipo retirado o que abandone el torneo.
- 2.0 — 2026-11-02: incorpora DEC-TOR-010: todo equipo retirado o que abandone el torneo no puede reincorporarse a la misma edición.
- 1.9 — 2026-11-02: incorpora DEC-TOR-009: después del cierre oficial del torneo, cualquier decisión por retiro/descalificación corresponde al organizador; FUTNEXIS no aplica cambios automáticos.
- 1.8 — 2026-11-02: incorpora DEC-TOR-008: si el retiro/descalificación ocurre en la final, el rival activo es declarado campeón con el marcador administrativo común.\n- 1.7 — 2026-11-02: incorpora DEC-TOR-007: el avance del rival en fase eliminatoria queda registrado con el marcador administrativo común.\n- 1.6 — 2026-11-02: incorpora DEC-TOR-006 sobre avance del rival en fase eliminatoria y continuidad normal del torneo.\n- 1.5 — 2026-11-02: incorpora DEC-TOR-005 sobre partidos futuros pendientes tras retiro/descalificación.
- 1.4 — 2026-11-02: incorpora DEC-TOR-004 y limita su efecto al partido que motivó la decisión.
- 1.3 — 2026-11-02: incorpora DEC-TOR-003 sobre elegibilidad de equipos sustitutos ante doble exclusión.
- 1.2 — 2026-11-02: incorpora DEC-TOR-002 sobre roles autorizados para retiro/descalificación.
- 1.1 — 2026-11-02: incorpora DEC-TOR-001 sobre configuración y modificación del marcador administrativo.
- 1.0 — 2026-11-02: registro inicial basado en decisiones disponibles; no es transcripción literal de documentos anteriores.
