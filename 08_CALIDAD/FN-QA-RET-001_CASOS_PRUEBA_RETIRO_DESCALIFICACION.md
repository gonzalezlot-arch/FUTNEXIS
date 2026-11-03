# FN-QA-RET-001 — Casos de prueba: retiro y descalificación de equipos

- **Versión:** 1.0
- **Estado:** EN_REVISION
- **Fecha:** 2026-11-02
- **Decisiones relacionadas:** DEC-PORT-048 y DEC-PORT-049
- **Documento funcional relacionado:** FN-MOD-ORG-002 v2.0

## 1. Precondiciones generales
- Existe un torneo con equipos inscritos y calendario oficial.
- Los partidos y resultados previos están registrados.
- El usuario tiene permisos para gestionar el torneo.
- Las acciones de retiro/descalificación y sus resoluciones quedan auditadas.
- Se distingue la clasificación vigente del historial oficial de resultados.

## 2. Casos de prueba

| ID | Escenario | Resultado esperado |
|---|---|---|
| CP-RET-001 | Un equipo se retira después de disputar cinco partidos. | Cambia a estado retirado; se conservan íntegros los cinco resultados y estadísticas originales. |
| CP-RET-002 | Un equipo se retira y tiene partidos pendientes contra equipos activos. | Cada partido pendiente se resuelve administrativamente a favor del respectivo rival activo. |
| CP-RET-003 | El organizador configura el marcador de un partido administrativo. | El marcador configurado queda registrado junto con puntos, causa, responsable y resolución. |
| CP-RET-004 | Un equipo es descalificado. | Cambia a estado descalificado y deja de integrar la clasificación vigente y el total de equipos activos. |
| CP-RET-005 | Se retiran/descalifican varios equipos. | Todos los excluidos salen de la clasificación vigente y la tabla se renumera consecutivamente entre equipos activos. |
| CP-RET-006 | Se consulta la clasificación después de una exclusión. | No se reservan posiciones para equipos excluidos; los activos ocupan posiciones consecutivas. |
| CP-RET-007 | Se confirma un resultado administrativo contra un equipo activo. | Se actualizan los datos colectivos conforme al marcador y las reglas de clasificación aplicables. El detalle de PJ/PG/PE/PP sigue pendiente de definición. |
| CP-RET-008 | Se consultan estadísticas individuales tras un resultado administrativo. | No se generan goles, partidos jugados, asistencias, tarjetas ni otras estadísticas individuales; no cambia ningún ranking/reconocimiento individual por ese resultado. |
| CP-RET-009 | Se consulta un partido disputado antes del retiro/descalificación. | Se conservan el resultado y las estadísticas originales, individuales y colectivas. |
| CP-RET-010 | Se consulta auditoría de retiro/descalificación. | Se identifica causa, fecha, responsable, marcador administrativo cuando aplique y partidos afectados. |
| CP-RET-011 | Ambos equipos de un partido pendiente están retirados/descalificados. | No se asignan puntos ni marcador administrativo; no se modifican estadísticas colectivas ni individuales; el encuentro queda registrado como no disputado por doble exclusión. |
| CP-RET-012 | Un equipo está excluido antes de un partido eliminatorio y su rival está activo. | El rival avanza automáticamente mediante victoria administrativa; el organizador establece el marcador; no se generan estadísticas individuales. |
| CP-RET-013 | Ambos equipos de un enfrentamiento eliminatorio están excluidos antes de disputarlo. | Ninguno avanza automáticamente; no se asigna ganador ni marcador; FUTNEXIS no elige sustituto; el organizador resuelve la plaza y su decisión queda auditada. |

## 3. Escenario de referencia
Torneo de liga con 20 equipos y 19 partidos previstos por equipo. Un equipo se retira después de disputar cinco partidos y mantiene 14 encuentros pendientes:
- Los cinco resultados disputados se conservan.
- Los encuentros pendientes contra equipos activos se resuelven administrativamente a favor de cada rival.
- Los encuentros pendientes contra otros equipos también excluidos no generan puntos ni marcador administrativo.
- El equipo retirado deja de integrar la clasificación vigente.
- La clasificación vigente muestra únicamente equipos activos, con posiciones consecutivas.
- Los resultados administrativos no generan estadísticas individuales.

Este escenario es ilustrativo; el formato y las reglas configuradas para cada torneo gobiernan los calendarios reales.

## 4. Casos de fase eliminatoria
### 4.1 Un equipo excluido y un rival activo
El rival activo avanza mediante victoria administrativa. El marcador lo define el organizador. No se crean goles ni partidos jugados individuales.

### 4.2 Doble exclusión
Ninguno de los dos equipos avanza automáticamente. La plaza queda pendiente de resolución del organizador. FUTNEXIS no selecciona automáticamente un sustituto. La decisión debe quedar auditada.

## 5. Pendientes que bloquean el cierre completo de pruebas
1. Definir si el marcador administrativo se configura por encuentro o mediante un marcador común.
2. Precisar cómo se representa el equipo excluido en el historial frente a la clasificación vigente.
3. Definir cómo los resultados administrativos afectan PJ, PG, PE, PP, GF, GC, DG y PTS.
4. Definir cómo el organizador resuelve la plaza vacante por doble exclusión y qué opciones reglamentarias se permiten.
5. Precisar estados y tratamiento de partidos no disputados por doble exclusión en fases de liga y eliminatorias.

## 6. Restricción de aprobación
Estos casos son una base de validación en revisión. No autorizan desarrollo ni sustituyen la aprobación integral del módulo. La ejecución documentará datos de entrada, resultado observado, resultado esperado, evidencia y estado (aprobado/rechazado/bloqueado).

## 7. Historial
- 1.0 — 2026-11-02: primera consolidación de CP-RET-001 a CP-RET-013 con base en DEC-PORT-048/049.
