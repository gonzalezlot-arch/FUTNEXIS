# FN-GOV-TRZ-001 — Matriz de trazabilidad del portal público

- **Versión:** 1.0
- **Estado:** EN_REVISION
- **Fecha:** 2026-11-02
- **Documentos relacionados:** FN-GOV-DEC-002 v1.3; FN-MOD-ORG-002 v1.6.
- **Propósito:** relacionar decisiones confirmadas con requisitos funcionales, reglas de negocio y criterios de aceptación, identificando vacíos sin inventar aprobaciones.

## 1. Convenciones

- **Directa:** el requisito/criterio implementa la decisión de forma explícita.
- **Parcial:** existe cobertura, pero falta precisión o un caso de aceptación.
- **Pendiente:** no existe cobertura verificable suficiente.
- FN-MOD-ORG-002 v1.6 incorpora RN-PORT-001 a RN-PORT-020 como normalización documental de decisiones ya registradas. Los aspectos expresamente pendientes continúan marcados como tales; la incorporación no implica aprobación global del módulo.
- DEC-PORT-001 a 008 no se trazan porque su contenido literal no está disponible.

## 2. Matriz de trazabilidad

| Decisión | Tema | Requisitos relacionados | Regla de negocio / referencia | Criterio(s) de aceptación | Cobertura |
|---|---|---|---|---|---|
| DEC-PORT-009 | Uniformidad visual y navegación | RF-PORT-019, RF-PORT-020, RF-PORT-037, RF-PORT-041 a 047 | UX transversal; rutas públicas | CA-PORT-008, CA-PORT-015, CA-PORT-018 | Parcial: aceptación visual general pendiente en UX |
| DEC-PORT-010 | Cuadrícula y paginación | RF-PORT-006 | RN-PORT-PROP-01: listado en tarjetas y paginación | CA-PORT-005 | Directa |
| DEC-PORT-011 | Orden de torneos | RF-PORT-009 | RN-PORT-PROP-02: grupos en curso, próximos, finalizados; orden interno por fecha a precisar | CA-PORT-006 | Parcial: campo temporal exacto pendiente |
| DEC-PORT-012 | Datos de tarjeta de torneo | RF-PORT-007 | RN-PORT-PROP-03: campos de tarjeta autorizados | CA-PORT-005 | Parcial: CA no verifica cada campo |
| DEC-PORT-013 | Seis secciones del torneo | RF-PORT-019 | RN-PORT-PROP-04: navegación por seis secciones | CA-PORT-008 | Directa |
| DEC-PORT-014 | Tabs horizontales | RF-PORT-020 | RN-PORT-PROP-05: tabs con desplazamiento móvil | CA-PORT-008 | Parcial: falta CA responsive explícito |
| DEC-PORT-015 | Calendario por jornada/lista | RF-PORT-021, RF-PORT-022, RF-PORT-023 | RN-PORT-PROP-06: dos presentaciones y filtros | CA-PORT-009 | Parcial: filtros y campos del partido no están cubiertos en CA |
| DEC-PORT-016 | Columnas de clasificación | RF-PORT-024 | RN-PORT-PROP-07: columnas oficiales de tabla | CA-PORT-011 | Directa |
| DEC-PORT-017 | Estadísticas individuales | RF-PORT-026 | RN-PORT-PROP-08: goles, partidos y tarjetas; sin asistencias | CA-PORT-012 | Parcial: CA-PORT-012 solo cubre rankings disciplinarios |
| DEC-PORT-018 | Desempates ranking goleadores | RF-PORT-027 | RN-PORT-PROP-09: goles, avance deportivo, menor cantidad de partidos; empate comparte posición | CA-PORT-013 | Parcial: algoritmo mixto y definición de partidos pendiente |
| DEC-PORT-019 | Rankings disciplinarios separados | RF-PORT-028 | RN-PORT-PROP-10: ranking independiente por color de tarjeta | CA-PORT-012 | Directa |
| DEC-PORT-020 | Ficha pública jugador | RF-PORT-041 a 046 | RN-PORT-PROP-11: ficha por identificador estable | CA-PORT-018 | Parcial: aceptación no cubre todos los datos y secciones |
| DEC-PORT-021 | Historial por torneo/equipo | RF-PORT-044, RF-PORT-047 | RN-PORT-PROP-12: historial inmutable por participación | CA-PORT-018 | Parcial: falta verificar orden y separación equipo actual |
| DEC-PORT-022 | Historial desplegable cronológico inverso | RF-PORT-044 | RN-PORT-PROP-13: tarjetas desplegables en orden inverso | Sin CA específico | Pendiente |
| DEC-PORT-023 | Ficha pública equipo | RF-PORT-031 a 040 | RN-PORT-PROP-14: resumen, plantilla, torneos, resultados y estadísticas | CA-PORT-015, CA-PORT-016 | Parcial |
| DEC-PORT-024 | Plantilla en tarjetas | RF-PORT-033, RF-PORT-034 | RN-PORT-PROP-15: tarjeta con número, nombre deportivo y posición | CA-PORT-015 | Parcial: falta validar navegación a jugador |
| DEC-PORT-025 | Estadísticas colectivas | RF-PORT-036 | RN-PORT-PROP-16: métricas colectivas por torneo | CA-PORT-015 | Parcial: enumerar columnas en CA |
| DEC-PORT-026 | Secciones de ficha equipo | RF-PORT-037 | RN-PORT-PROP-17: Resumen, Plantilla, Torneos, Resultados | CA-PORT-015 | Parcial: CA no valida las cuatro secciones |
| DEC-PORT-027 | Presentación de resultados | RF-PORT-016, RF-PORT-035 | RN-PORT-PROP-18: resultados oficiales vinculados al partido | CA-PORT-010, CA-PORT-015 | Parcial |
| DEC-PORT-028 | Detalle disciplinario | RF-PORT-038 | RN-PORT-PROP-19: jugador, número y conteo de tarjetas | Sin CA específico | Pendiente |
| DEC-PORT-029 | Goleadores del equipo | RF-PORT-039, RF-PORT-040 | RN-PORT-PROP-20: orden por goles sin desempates generales | CA-PORT-016 | Directa |
| DEC-PORT-030 | Ubicación ranking equipo | RF-PORT-039 | RN-PORT-PROP-21: ranking en resumen de ficha equipo | CA-PORT-016 | Parcial: ubicación visual no explícita |
| DEC-PORT-031 | Rendimiento por torneo | RF-PORT-045 | RN-PORT-PROP-22: estadísticas desglosadas por torneo | CA-PORT-018 | Parcial |
| DEC-PORT-032 | Presentación resumida de estadísticas | RF-PORT-045 | RN-PORT-PROP-23: resumen estadístico legible | Sin CA específico | Pendiente |
| DEC-PORT-033 | Reconocimientos deportivos | RF-PORT-046 | RN-PORT-PROP-24: reconocimientos permitidos | CA-PORT-018 | Parcial |
| DEC-PORT-034 | Solo reconocimientos confirmados | RF-PORT-046 | RN-PORT-PROP-25: no mostrar reconocimientos no confirmados | CA-PORT-018 | Parcial: agregar caso negativo |
| DEC-PORT-035 | Navegación pública e IDs estables | RF-PORT-041 y rutas públicas | RN-PORT-PROP-26: navegación sin login por identificadores estables | CA-PORT-024 | Parcial: CA-PORT-024 solo valida acceso privado |
| DEC-PORT-036 | Estados públicos de torneo | RF-PORT-008, RF-PORT-009 | RN-PORT-PROP-27: próximo/en curso/finalizado según estado deportivo | CA-PORT-006, CA-PORT-007 | Parcial: falta probar límites de transición |
| DEC-PORT-037 | Publicación de estadísticas confirmadas | RF-PORT-016 a 018, RF-PORT-025 a 030, RF-PORT-035 a 039, RF-PORT-045 | RN-PORT-PROP-28: solo torneo publicado y dato confirmado | CA-PORT-010, CA-PORT-013, CA-PORT-014 | Parcial: faltan casos por tipo de estadística |
| DEC-PORT-038 | Desempate por avance deportivo | RF-PORT-027 | RN-PORT-PROP-09 | CA-PORT-013 | Parcial: formatos mixtos/especiales |
| DEC-PORT-039 | Estados especiales de partido | RF-PORT-015, RF-PORT-016, RF-PORT-023, RF-PORT-025, RF-PORT-029, RF-PORT-030 | RN-PORT-PROP-29: aplazado, suspendido, anulado y reprogramado | CA-PORT-010, CA-PORT-014 | Parcial: suspendido y reprogramado sin CA propio |
| DEC-PORT-040 | Moderación de contenido | RF-PORT-001, RF-PORT-007, RF-PORT-031 | RN-PORT-PROP-30: validar archivos y contenido institucional | Sin CA específico | Pendiente |
| DEC-PORT-041 | Organización suspendida | Reglas generales de publicación y disponibilidad | RN-PORT-PROP-31: ocultar rutas públicas, conservar datos y bloquear gestión | CA-PORT-020 | Parcial: mensaje y reactivación no validados |
| DEC-PORT-042 | SEO e indexación | Reglas de publicación y metadatos | RN-PORT-PROP-32: indexar solo recursos publicados y disponibles | CA-PORT-023 | Parcial: retirada de índices no probada |
| DEC-PORT-043 | Auditoría y conservación | RF-PORT-003 y reglas de publicación/estado | RN-PORT-PROP-33: auditoría con actor, fecha, acción, entidad y motivo | CA-PORT-022 | Parcial: campos auditados y acceso no validados |

## 3. Hallazgos de trazabilidad

1. **No hay sección formal de reglas de negocio numeradas** en FN-MOD-ORG-002 v1.5. Las reglas aparecen en prosa. La especificación v1.6 incorpora RN-PORT-001 a RN-PORT-020 como catálogo numerado en revisión.
2. **Los criterios de aceptación son insuficientes** para verificar varias decisiones de presentación, moderación, historial, accesibilidad, SEO y estados de partidos.
3. **CA-PORT-006:** la referencia fue corregida en FN-MOD-ORG-002 v1.6; ahora cita DEC-PORT-011 para el orden y DEC-PORT-036 para la clasificación de estados.
4. **RF-PORT-012 no está aprobado:** filtros por estado/modalidad en portada siguen pendientes. No deben convertirse en criterio de aceptación obligatorio ni implementarse como alcance confirmado.
5. **DEC-PORT-001 a 008 quedan fuera de la matriz** hasta recuperar su contenido literal.
6. **La numeración RF/CA no se declara equivalente a la versión fuente 1.4.** Se necesita una matriz de correspondencia histórica antes de reemplazar o eliminar IDs anteriores.
7. **El ranking de goleadores requiere casos de prueba** para avance deportivo, partidos jugados y empates residuales.
8. **Las reglas de partidos suspendidos y anulados necesitan enlace** con el módulo operativo de partidos y con el procedimiento de recálculo estadístico.

## 4. Acciones recomendadas

| Acción | Prioridad | Resultado esperado |
|---|---|---|
| Corregir referencia de CA-PORT-006 | Alta | Criterio ligado a DEC-PORT-011 y DEC-PORT-036 |
| Incorporar sección RN numerada | Alta | Reglas identificables y trazables |
| Ampliar criterios de aceptación faltantes | Alta | Cobertura verificable para cada decisión confirmada |
| Recuperar DEC-PORT-001 a 008 | Alta | Registro histórico completo |
| Comparar RF/CA con versión 1.4 | Alta | Correspondencia de IDs y control de cambios |
| Definir casos de ranking y desempates | Alta | Algoritmo verificable y pruebas |
| Mantener filtros PORT-01 como pendientes | Media | Evitar alcance no aprobado |
| Vincular estados de partido con módulo operativo | Alta | Coherencia de resultados y estadísticas |

## 5. Restricción de uso

Esta matriz es una herramienta de análisis y control documental. No constituye aprobación funcional ni autorización de desarrollo. Los códigos RN-PORT-PROP son propuestas y deben ratificarse antes de incorporarse como reglas oficiales.

## 6. Historial

- 1.1 — 2026-11-02: actualiza la trazabilidad tras FN-MOD-ORG-002 v1.6; incorpora catálogo RN numerado y ampliación de criterios CA-PORT-025 a 040.
