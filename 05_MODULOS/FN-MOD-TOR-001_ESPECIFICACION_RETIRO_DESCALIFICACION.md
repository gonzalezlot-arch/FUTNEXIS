# FN-MOD-TOR-001 — Especificación funcional: retiro, descalificación y resolución administrativa de equipos

- **Versión:** 1.0
- **Estado:** EN_REVISION
- **Fecha:** 2026-11-02
- **Tipo:** especificación funcional parcial
- **Decisiones relacionadas:** DEC-PORT-039, DEC-PORT-048, DEC-PORT-049, DEC-PORT-050 y DEC-PORT-051
- **Casos de prueba relacionados:** FN-QA-RET-001 v1.1
- **Nota de alcance:** este documento formaliza exclusivamente las reglas conocidas sobre retiro/descalificación y sus efectos. No constituye todavía la especificación integral del módulo de torneos, calendario, partidos, clasificación ni inscripciones.

## 1. Objetivo

Definir el comportamiento funcional cuando un equipo abandona voluntariamente una competición, es descalificado o queda excluido de un encuentro, incluyendo la resolución de partidos pendientes, los efectos colectivos, la conservación del historial y los escenarios de doble exclusión.

## 2. Alcance

Incluye:
- Cambio de estado competitivo del equipo a retirado o descalificado.
- Exclusión de la clasificación vigente y del total de equipos activos.
- Conservación de resultados y estadísticas históricas.
- Resolución administrativa de partidos pendientes contra equipos activos.
- Marcador administrativo común del torneo.
- Efectos colectivos e individuales de los resultados administrativos.
- Tratamiento de encuentros entre dos equipos excluidos.
- Doble exclusión en una instancia eliminatoria.
- Auditoría de las decisiones administrativas.

No define todavía:
- Flujo completo de creación y configuración de torneos.
- Formatos de competición, grupos, llaves, fases y calendarios.
- Reglas completas de inscripción, sustitución o transferencia de equipos.
- Catálogo completo de estados y transiciones de partidos.
- Fórmulas completas de clasificación para todos los reglamentos.
- Reglas de elegibilidad y validación de equipos sustitutos en cuadros eliminatorios.

## 3. Términos

- **Equipo activo:** equipo que conserva el derecho a competir en el torneo y puede disputar encuentros.
- **Equipo excluido:** equipo cuyo estado competitivo es retirado o descalificado.
- **Resultado administrativo:** resolución de un partido pendiente sin disputa efectiva, aplicada a favor de un equipo activo conforme al marcador administrativo común.
- **Doble exclusión:** situación en la que ambos equipos asignados a un encuentro eliminatorio están excluidos antes de disputarlo.

## 4. Requisitos funcionales

### RF-TOR-001 — Registrar retiro o descalificación
El sistema permitirá registrar que un equipo abandona el torneo o es descalificado. El registro deberá identificar el estado correspondiente y conservar la información histórica del equipo.

### RF-TOR-002 — Excluir de la clasificación vigente
Al registrar el retiro o descalificación, el equipo dejará de integrar la clasificación competitiva vigente y el total de equipos activos. La clasificación de los equipos activos se recalculará y sus posiciones se mostrarán consecutivamente.

### RF-TOR-003 — Conservar historial previo
Los resultados, puntos y estadísticas colectivas de los partidos disputados antes de la exclusión se conservarán sin modificación retroactiva.

### RF-TOR-004 — Configurar marcador administrativo común
El organizador podrá configurar un único marcador administrativo común para el torneo. Este valor será utilizado en todos los partidos pendientes que deban resolverse administrativamente a favor de equipos activos.

### RF-TOR-005 — Aplicar marcador a favor del equipo activo
El resultado administrativo siempre se interpretará a favor del equipo activo, incluso cuando este figure originalmente como visitante. La localía original no podrá invertir el beneficiario del resultado.

### RF-TOR-006 — Actualizar métricas colectivas del equipo activo
Cada resultado administrativo a favor de un equipo activo incrementará sus partidos jugados y partidos ganados, sus goles a favor y puntos conforme al marcador y al reglamento del torneo. Se recalcularán goles en contra, diferencia de goles, puntos, posición y demás métricas colectivas aplicables.

### RF-TOR-007 — Mantener historial colectivo del equipo excluido
El equipo excluido permanecerá consultable en el historial del torneo con su estado, total de partidos jugados y estadísticas colectivas de goles y resultados, incluidos los efectos administrativos que le correspondan. No aparecerá en la clasificación vigente ni en el total de equipos activos.

### RF-TOR-008 — No generar estadísticas individuales administrativas
Los resultados administrativos no asignarán goles, asistencias, partidos jugados, tarjetas ni otras estadísticas individuales a jugadores. Tampoco alterarán rankings o reconocimientos individuales.

### RF-TOR-009 — Resolver partido entre dos equipos excluidos
Cuando ambos equipos asignados a un partido pendiente estén excluidos, el encuentro quedará registrado como no disputado por exclusión de ambos equipos. No se asignará marcador administrativo, puntos ni efectos estadísticos colectivos o individuales.

### RF-TOR-010 — Resolver doble exclusión eliminatoria
Si ambos equipos de un enfrentamiento eliminatorio están excluidos antes de disputarlo, ninguno avanzará automáticamente. FUTNEXIS no seleccionará sustitutos por sí mismo. El organizador indicará qué equipo o equipos previamente eliminados ocuparán la instancia o plaza vacante.

### RF-TOR-011 — Auditar decisiones administrativas
El sistema conservará auditoría de las acciones de retiro/descalificación, aplicación de resultados administrativos y selección de equipos para plazas vacantes. Como mínimo deberá registrarse responsable, fecha, acción, entidad afectada y motivo cuando corresponda; también el marcador aplicado y los equipos seleccionados cuando aplique.

## 5. Reglas de negocio

### RN-TOR-001 — Estado e inclusión competitiva
Un equipo retirado o descalificado no forma parte de la clasificación vigente ni del total de equipos activos. Su historial no se elimina.

### RN-TOR-002 — No retroactividad
Los partidos disputados y confirmados antes de la exclusión mantienen sus resultados y estadísticas originales.

### RN-TOR-003 — Marcador común por torneo
Existe un solo marcador administrativo configurado para el torneo; no se define un marcador distinto por cada partido.

### RN-TOR-004 — Beneficiario del resultado administrativo
El marcador administrativo se asigna siempre a favor del equipo activo, con independencia de su condición original de local o visitante.

### RN-TOR-005 — Efecto colectivo para el equipo activo
Los resultados administrativos cuentan para las métricas colectivas del equipo activo conforme al marcador y las reglas del torneo.

### RN-TOR-006 — Historial del equipo excluido
El equipo excluido conserva una vista histórica de sus partidos jugados y estadísticas colectivas, incluidos los efectos administrativos. Esta vista histórica no equivale a su inclusión en la clasificación vigente.

### RN-TOR-007 — Sin efecto individual
Un partido resuelto administrativamente no genera participación ni estadísticas individuales.

### RN-TOR-008 — Doble exclusión en fase eliminatoria
Ante la doble exclusión, ningún equipo avanza automáticamente. La selección de los equipos previamente eliminados que ocuparán la instancia corresponde al organizador y debe quedar auditada.

### RN-TOR-009 — Encuentro entre dos equipos excluidos
No se asigna marcador ni efecto colectivo o individual a un partido pendiente entre dos equipos excluidos.

## 6. Permisos y controles

- La capacidad de registrar retiros/descalificaciones y resolver encuentros debe depender de permisos administrativos del torneo.
- La selección de equipos para cubrir una plaza vacante corresponde al organizador autorizado.
- Las operaciones deben validarse en servidor; no basta con ocultar acciones en la interfaz.
- Cada operación debe conservar trazabilidad para revisión posterior.

**Pendiente:** definir matriz exacta de roles y permisos para estas operaciones en el módulo de torneos.

## 7. Validaciones y errores

El sistema deberá impedir:
- Aplicar un resultado administrativo a favor de un equipo que también esté excluido.
- Aplicar el marcador común cuando ambos equipos estén excluidos.
- Avanzar automáticamente a uno de los dos equipos excluidos en una llave eliminatoria.
- Crear estadísticas individuales a partir de un resultado administrativo.
- Modificar retroactivamente resultados confirmados anteriores al retiro/descalificación sin un procedimiento de corrección autorizado y auditado.

**Pendiente:** definir mensajes de error, restricciones de edición posterior y reglas de idempotencia ante reintentos de una misma operación.

## 8. Criterios de aceptación

- **CA-TOR-001:** al retirar o descalificar un equipo, este deja de figurar en la clasificación vigente y el total de equipos activos se actualiza.
- **CA-TOR-002:** los resultados previos del equipo excluido permanecen sin cambios.
- **CA-TOR-003:** todos los partidos pendientes contra equipos activos usan el mismo marcador configurado para el torneo.
- **CA-TOR-004:** el marcador se aplica a favor del equipo activo incluso si originalmente era visitante.
- **CA-TOR-005:** el equipo activo recibe los efectos colectivos correspondientes al marcador y al reglamento.
- **CA-TOR-006:** el equipo excluido conserva su historial colectivo y no reaparece en la clasificación vigente.
- **CA-TOR-007:** los resultados administrativos no generan estadísticas individuales.
- **CA-TOR-008:** un partido pendiente entre dos equipos excluidos queda no disputado y sin efectos estadísticos.
- **CA-TOR-009:** ante doble exclusión eliminatoria, ninguno avanza automáticamente.
- **CA-TOR-010:** el organizador puede seleccionar equipos previamente eliminados para ocupar la instancia vacante y la decisión queda auditada.

## 9. Dependencias y riesgos

- Depende del modelo de datos de torneo, equipo participante, partido, resultado, clasificación y auditoría.
- La aplicación del marcador debe ser transaccional para evitar resultados parciales o duplicados.
- El recálculo debe respetar el reglamento específico del torneo.
- La distinción entre historial y clasificación vigente debe reflejarse tanto en consultas como en interfaces.
- Las reglas de sustitución en cuadros deben validarse contra la estructura real de la competición.

## 10. Pendientes de definición

1. ¿El organizador configura el marcador administrativo al crear el torneo o puede modificarlo durante la competición? ¿Qué ocurre con resultados ya aplicados si lo modifica?
2. ¿Qué roles pueden registrar un retiro/descalificación y confirmar la resolución administrativa?
3. ¿Qué condiciones hacen elegible a un equipo previamente eliminado para ocupar una plaza vacante y cómo se valida su compatibilidad con la ronda?
4. ¿Cómo se trata una exclusión después de que ya se haya generado o confirmado el resultado de un partido, pero antes de cerrar la jornada/fase?
5. ¿Qué estados exactos tendrá un partido no disputado por doble exclusión y cómo se mostrará en calendario, actas e informes?
6. ¿Cómo se recalculan o congelan las clasificaciones históricas al cierre del torneo?
7. ¿Qué procedimiento autorizado permite corregir un retiro/descalificación registrado por error?

## 11. Historial de cambios

- **1.0 — 2026-11-02:** primera formalización parcial de las reglas aprobadas en DEC-PORT-048 a DEC-PORT-051. Pendiente completar reglas operativas y validar criterios de aceptación.
