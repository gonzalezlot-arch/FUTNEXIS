# FN-GOV-DOC-001 — Inventario documental

- Versión: 4.2
- Estado: EN_REVISION
- Fecha de corte: 2026-11-04

## Documentos creados en GitHub
| Código | Documento | Ruta | Versión | Estado |
|---|---|---|---|---|
| FN-GOV-MET-001 | Metodología de gobierno documental | 00_GOBIERNO/FN-GOV-MET-001_METODOLOGIA.md | 1.0 | BORRADOR |
| FN-GOV-DEC-001 | Registro maestro de decisiones | 00_GOBIERNO/FN-GOV-DEC-001_REGISTRO_DECISIONES.md | 2.7 | EN_REVISION |
| FN-GOV-GLO-001 | Glosario maestro | 00_GOBIERNO/FN-GOV-GLO-001_GLOSARIO.md | 1.0 | BORRADOR |
| FN-GOV-DOC-001 | Inventario documental | 00_GOBIERNO/FN-GOV-DOC-001_INVENTARIO_DOCUMENTAL.md | 4.1 | EN_REVISION |
| FN-MOD-ORG-001 | Especificación funcional organizaciones | 05_MODULOS/FN-MOD-ORG-001_ESPECIFICACION_ORGANIZACIONES.md | 1.2 | EN_REVISION; consolidación por verificar contra original |\n| FN-MOD-CORE-001 | Especificación funcional usuarios, autenticación y organizaciones | 05_MODULOS/FN-MOD-CORE-001_ESPECIFICACION_USUARIOS_AUTENTICACION_ORGANIZACIONES.md | 1.0 | APROBADO |
| FN-ARC-CORE-001 | Decisiones de arquitectura: identidad, autenticación y contexto de organización | 04_ARQUITECTURA/FN-ARC-CORE-001_DECISIONES_IDENTIDAD_AUTENTICACION_CONTEXTO_ORGANIZACION.md | 0.3 | APROBADO |
| FN-UX-ORG-001 | UX/UI organizaciones | 03_UX_UI/FN-UX-ORG-001_UX_ORGANIZACIONES.md | 1.1 | EN_REVISION; consolidación por verificar contra original |
| FN-MOD-ORG-002 | Especificación funcional portal público | 05_MODULOS/FN-MOD-ORG-002_ESPECIFICACION_PORTAL_PUBLICO.md | 2.0 | EN_REVISION; consolidación por verificar contra original |
| FN-UX-ORG-002 | UX/UI portal público | 03_UX_UI/FN-UX-ORG-002_UX_PORTAL_PUBLICO.md | 1.5 | EN_REVISION; consolidación por verificar contra original |
| FN-GOV-DEC-002 | Registro de decisiones del portal público | 00_GOBIERNO/FN-GOV-DEC-002_REGISTRO_DECISIONES_PORTAL.md | 1.9 | EN_REVISION | 
| FN-GOV-AUD-001 | Auditoría de consistencia documental | 00_GOBIERNO/FN-GOV-AUD-001_AUDITORIA_CONSISTENCIA.md | 1.5 | EN_REVISION |
| FN-GOV-TRZ-001 | Matriz de trazabilidad del portal público | 00_GOBIERNO/FN-GOV-TRZ-001_MATRIZ_TRAZABILIDAD_PORTAL.md | 1.6 | EN_REVISION |


| FN-QA-RET-001 | Casos de prueba: retiro y descalificación de equipos | 08_CALIDAD/FN-QA-RET-001_CASOS_PRUEBA_RETIRO_DESCALIFICACION.md | 2.1 | EN_REVISION |

| FN-MOD-TOR-001 | Especificación funcional: retiro, descalificación y resolución administrativa de equipos | 05_MODULOS/FN-MOD-TOR-001_ESPECIFICACION_RETIRO_DESCALIFICACION.md | 2.6 | EN_REVISION; especificación parcial |

## Versiones fuente conocidas pendientes de cotejo
| Código | Versión fuente conocida | Estado conocido | Acción pendiente |
|---|---:|---|---|
| FN-MOD-ORG-001 | 1.1 | EN_REVISION | Comparar con consolidación 1.2 |
| FN-UX-ORG-001 | 1.0 | EN_REVISION | Comparar con consolidación 1.1 |
| FN-MOD-ORG-002 | 1.4 | EN_REVISION | Comparar con consolidación 1.9 |
| FN-UX-ORG-002 | 1.4 | EN_REVISION | Comparar con consolidación 1.5 |
| FN-GOV-DEC-002 | 1.2 | EN_REVISION | Recuperar detalle histórico DEC-PORT-001 a 008 y comparar |

## Reglas
No marcar APROBADO sin aprobación expresa. No declarar verificada una versión conocida solo por resumen. Conservar versiones obsoletas. Actualizar el inventario junto con cada cambio documental.

## Historial
- 4.2 — 2026-11-05: registra FN-ARC-CORE-001 v0.4 y FN-GOV-DEC-001 v2.8 por DEC-CORE-009; el cotejamiento específico de `users.email` queda aprobado documentalmente, pendiente de validación en MariaDB 11.4.13 antes de cualquier implementación.
- 3.7 — 2026-11-02: actualiza FN-GOV-DEC-001 a v2.5, FN-MOD-TOR-001 a v2.6 y FN-QA-RET-001 a v2.1 por DEC-TOR-013; los informes y reportes descargables identifican individualmente el estado de baja de cada equipo.
- 3.6 — 2026-11-02: actualiza FN-GOV-DEC-001 a v2.4, FN-MOD-TOR-001 a v2.5 y FN-QA-RET-001 a v2.0 por DEC-TOR-012; define presentación del partido no disputado por doble baja en calendario e historial, registro administrativo sin acta deportiva y sin efectos estadísticos.
- 3.5 — 2026-11-02: sincroniza FN-MOD-TOR-001 v2.4 con DEC-TOR-011 y registra el estado «No disputado por doble baja»; el registro maestro está en v2.3 y los casos de prueba en v1.9.
- 3.3 — 2026-11-02: actualiza el registro de decisiones a v2.2, la especificación a v2.3 y los casos de prueba a v1.8; ningún equipo dado de baja puede reintegrarse, cualquiera que sea el motivo.
- 3.2 — 2026-11-02: actualiza el registro de decisiones a v2.1, la especificación de retiro/descalificación a v2.2 y los casos de prueba a v1.7; aclara que ningún equipo retirado o que abandone puede reincorporarse.
- 3.1 — 2026-11-02: incorpora DEC-TOR-010 y actualiza el registro de decisiones a v2.0, la especificación a v2.1 y los casos de prueba a v1.6.
- 3.0 — 2026-11-02: actualiza FN-MOD-TOR-001 a v2.0, FN-GOV-DEC-001 a v1.9 y FN-QA-RET-001 a v1.5 por DEC-TOR-009; define que los casos posteriores al cierre oficial quedan a decisión del organizador, sin cambios automáticos.\n- 2.9 — 2026-11-02: actualiza FN-MOD-TOR-001 a v1.9, FN-GOV-DEC-001 a v1.8 y FN-QA-RET-001 a v1.4 por DEC-TOR-008; incorpora declaración de campeón por retiro/descalificación en la final.\n- 2.8 — 2026-11-02: actualiza FN-MOD-TOR-001 a v1.8, FN-GOV-DEC-001 a v1.7 y FN-QA-RET-001 a v1.3 por DEC-TOR-007; incorpora marcador administrativo en avance eliminatorio.\n- 2.7 — 2026-11-02: actualiza FN-MOD-TOR-001 a v1.7 y FN-GOV-DEC-001 a v1.6 por DEC-TOR-006; incorpora avance del rival en fase eliminatoria.\n- 2.6 — 2026-11-02: actualiza FN-MOD-TOR-001 a v1.6 y FN-GOV-DEC-001 a v1.5 por DEC-TOR-005; incorpora resolución de partidos futuros pendientes.
- 2.2 — 2026-11-02: incorpora FN-MOD-TOR-001 v1.0 como especificación operativa parcial para retiro/descalificación y resolución administrativa.
- 2.1 — 2026-11-02: registra FN-GOV-AUD-001 v1.5 y consolida el seguimiento de DEC-PORT-050/051.
- 2.0 — 2026-11-02: registra FN-GOV-DEC-002 v1.9, FN-GOV-TRZ-001 v1.6 y FN-QA-RET-001 v1.1; incorpora DEC-PORT-050/051 al seguimiento documental.
- 1.8 — 2026-11-02: registra DEC-PORT-047/048 y las versiones FN-MOD-ORG-002 v1.9, FN-GOV-DEC-002 v1.7, FN-GOV-TRZ-001 v1.4 y FN-GOV-AUD-001 v1.3.
- 1.7 — 2026-11-02: registra DEC-PORT-046 y las versiones FN-MOD-ORG-002 v1.8, FN-GOV-DEC-002 v1.6, FN-GOV-TRZ-001 v1.3 y FN-GOV-AUD-001 v1.2.
- 1.6 — 2026-11-02: registra DEC-PORT-044 y las versiones FN-MOD-ORG-002 v1.7, FN-GOV-DEC-002 v1.4, FN-GOV-TRZ-001 v1.2 y FN-GOV-AUD-001 v1.1.
- 1.5 — 2026-11-02: actualiza FN-MOD-ORG-002 a v1.6 y FN-GOV-TRZ-001 a v1.1 tras normalizar reglas y ampliar criterios de aceptación.
- 1.3 — 2026-11-02: corrige versión del encabezado, incorpora el registro de decisiones y la auditoría, y distingue archivos actuales de versiones fuente pendientes de cotejo.
- 1.0 — 2026-11-02: inventario inicial.
