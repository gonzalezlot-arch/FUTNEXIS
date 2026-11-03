# FN-QA-RET-001 — Casos de prueba: retiro y descalificación de equipos

- **Versión:** 1.6
- **Estado:** EN_REVISION
- **Fecha:** 2026-11-02
- **Decisiones relacionadas:** DEC-PORT-048, DEC-PORT-049, DEC-PORT-050 y DEC-PORT-051
- **Documento funcional relacionado:** FN-MOD-ORG-002 v2.0 (referencia del portal; la lógica operativa debe trasladarse al módulo de torneos y partidos)

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
| CP-RET-002 | Un equipo se retira y tiene partidos pendientes contra equipos activos. | Cada partido pendiente contra un equipo activo se resuelve con un único marcador administrativo común configurado por el organizador y orientado a favor del equipo activo, incluso si figura como visitante. |
| CP-RET-003 | El organizador configura el marcador de un partido administrativo. | El marcador común configurado por el organizador se aplica a todos los partidos administrativos y siempre favorece al equipo activo, independientemente de la localía original. |
| CP-RET-004 | Un equipo es descalificado. | Cambia a estado descalificado y deja de integrar la clasificación vigente y el total de equipos activos. |
| CP-RET-005 | Se retiran/descalifican varios equipos. | Todos los excluidos salen de la clasificación vigente y la tabla se renumera consecutivamente entre equipos activos. |
| CP-RET-006 | Se consulta la clasificación después de una exclusión. | No se reservan posiciones para equipos excluidos; los activos ocupan posiciones consecutivas. |
| CP-RET-007 | Se confirma un resultado administrativo contra un equipo activo. | El equipo activo suma PJ, PG, GF, GC y puntos conforme al marcador; se recalculan DG, PTS, posición y demás métricas colectivas según el reglamento. |
| CP-RET-008 | Se consultan estadísticas individuales tras un resultado administrativo. | No se generan goles, partidos jugados, asistencias, tarjetas ni otras estadísticas individuales; no cambia ningún ranking/reconocimiento individual por ese resultado. |
| CP-RET-009 | Se consulta un partido disputado antes del retiro/descalificación. | Se conservan el resultado y las estadísticas originales, individuales y colectivas. |
| CP-RET-010 | Se consulta auditoría de retiro/descalificación. | Se identifica causa, fecha, responsable, marcador administrativo cuando aplique y partidos afectados. |
| CP-RET-011 | Ambos equipos de un partido pendiente están retirados/descalificados. | No se asignan puntos ni marcador administrativo; no se modifican estadísticas colectivas ni individuales; el encuentro queda registrado como no disputado por doble exclusión. |
| CP-RET-012 | Un equipo está excluido antes de un partido eliminatorio y su rival está activo. | El rival avanza automáticamente mediante victoria administrativa; el organizador establece el marcador; no se generan estadísticas individuales. |
| CP-RET-013 | Ambos equipos de un enfrentamiento eliminatorio están excluidos antes de disputarlo. | Ninguno avanza automáticamente; no se asigna ganador ni marcador; FUTNEXIS no elige sustituto; el organizador resuelve la plaza y su decisión queda auditada. |\n| CP-RET-014 | Un equipo es retirado/descalificado durante una fase eliminatoria y su rival continúa activo. | El rival avanza a la siguiente fase; el enfrentamiento queda registrado con el marcador administrativo común a favor del rival activo; se actualizan métricas colectivas sin estadísticas individuales; el cuadro se actualiza y el torneo continúa con normalidad. No se asigna un sustituto para esa plaza. |\n| CP-RET-015 | Durante la final, uno de los finalistas es retirado/descalificado y el otro permanece activo. | El finalista activo es declarado campeón; la final queda registrada con el marcador administrativo común a su favor; se actualizan métricas colectivas y no se generan estadísticas individuales. |\n| CP-RET-016 | Ambos finalistas están retirados/descalificados. | No se declara campeón automáticamente ni se asigna marcador administrativo; se activa el procedimiento de doble exclusión y la resolución queda a cargo del organizador autorizado. |\n| CP-RET-017 | Se registra un retiro/descalificación después del cierre oficial del torneo. | FUTNEXIS no modifica automáticamente resultados, clasificación, estadísticas ni reconocimientos; cualquier medida queda a decisión del organizador. |\n| CP-RET-018 | Un equipo que formalizó su retiro voluntario solicita reincorporarse mientras el torneo sigue en curso. | El sistema rechaza la reincorporación a esa misma edición; el equipo conserva el estado retirado y su historial. |

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
1. Cerrado por DEC-PORT-050: se utiliza un único marcador común para el torneo.
2. Cerrado por DEC-PORT-050: aparece en el historial con estado, PJ y estadísticas colectivas, pero no en la clasificación vigente.
3. Cerrado en lo esencial por DEC-PORT-050: el equipo activo suma PJ, PG, GF, GC y puntos; se recalculan DG, posición y demás métricas conforme al reglamento.
4. Cerrado en cuanto a la autoridad de decisión por DEC-PORT-051: el organizador selecciona equipos previamente eliminados. Quedan por especificar validaciones, elegibilidad y tratamiento de plazas múltiples.
5. Precisar estados y tratamiento de partidos no disputados por doble exclusión en fases de liga y eliminatorias.

## 6. Restricción de aprobación
Estos casos son una base de validación en revisión. No autorizan desarrollo ni sustituyen la aprobación integral del módulo. La ejecución documentará datos de entrada, resultado observado, resultado esperado, evidencia y estado (aprobado/rechazado/bloqueado).

## 7. Historial
- 1.6 — 2026-11-02: incorpora CP-RET-018 para DEC-TOR-010: se rechaza la reincorporación de un equipo retirado voluntariamente.\n- 1.5 — 2026-11-02: incorpora CP-RET-017 para DEC-TOR-009: ninguna modificación automática después del cierre oficial; decisión del organizador.\n- 1.4 — 2026-11-02: incorpora CP-RET-015/016 para DEC-TOR-008: campeón por retiro/descalificación de un finalista y tratamiento de doble exclusión en la final.\n- 1.3 — 2026-11-02: actualiza CP-RET-014 para DEC-TOR-007: el avance eliminatorio se registra con el marcador administrativo común y efectos colectivos, sin estadísticas individuales.\n- 1.2 — 2026-11-02: incorpora CP-RET-014 para DEC-TOR-006: avance del rival activo en fase eliminatoria y continuidad del torneo.\n- 1.1 — 2026-11-02: incorpora DEC-PORT-050/051: marcador común, orientación a favor del activo, cómputo colectivo, historial visible y selección de equipos previamente eliminados por el organizador.
- 1.0 — 2026-11-02: primera consolidación de CP-RET-001 a CP-RET-013 con base en DEC-PORT-048/049.
