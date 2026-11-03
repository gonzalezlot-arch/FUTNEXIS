# FN-MOD-TOR-001 — Especificación funcional: retiro, descalificación y resolución administrativa de equipos

- **Versión:** 2.2
- **Estado:** EN_REVISION
- **Fecha:** 2026-11-02
- **Tipo:** especificación funcional parcial
- **Decisiones relacionadas:** DEC-PORT-039, DEC-PORT-048, DEC-PORT-049, DEC-PORT-050, DEC-PORT-051 y DEC-TOR-001
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

### RF-TOR-013 — Reemplazar resultado del enfrentamiento objeto de retiro/descalificación
Cuando la decisión de retiro o descalificación se presente respecto de un enfrentamiento ya disputado entre el equipo excluido y su rival, únicamente ese encuentro reemplazará su resultado deportivo original por el resultado administrativo común del torneo, aplicado a favor del rival. Los demás encuentros anteriores conservarán sus resultados originales. Actualizará las métricas colectivas de ambos equipos conforme a ese marcador y conservará el resultado original en la auditoría histórica, sin generar ni conservar estadísticas individuales derivadas del encuentro como resultado oficial.

### RF-TOR-012 — Validar equipos sustitutos en plazas vacantes
El sistema permitirá al organizador seleccionar equipos previamente eliminados para ocupar una instancia o plaza vacante por doble exclusión, validando que hayan participado en el mismo torneo, que hayan sido eliminados en una instancia anterior, que cumplan las condiciones deportivas y reglamentarias de la ronda y que no tengan una exclusión o sanción que les impida continuar.

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

### RN-TOR-011 — Autorización para retiro y descalificación
Podrán ejecutar el retiro o descalificación del equipo: el propietario de la organización, un administrador de la organización que tenga permiso de gestión deportiva, y el organizador o director del torneo cuando tenga asignado el permiso específico para esa competición. Un operador de resultados podrá registrar una incidencia o reportar una solicitud, pero no ejecutar por sí mismo el retiro o la descalificación.

### RN-TOR-013 — Efecto de la decisión sobre el enfrentamiento disputado
Si el retiro o descalificación se presenta respecto de un enfrentamiento ya disputado contra el equipo que solicita o motiva la decisión, únicamente ese encuentro se sustituye por el marcador administrativo común del torneo, siempre a favor del rival. Los demás partidos anteriores del equipo excluido conservan sus resultados y estadísticas originales. Se recalculan las métricas colectivas de los dos equipos del encuentro afectado. El resultado deportivo original se conserva únicamente como antecedente de auditoría y no como resultado oficial vigente. No se generan estadísticas individuales a partir del resultado administrativo.

### RN-TOR-012 — Elegibilidad de equipos sustitutos
Para ocupar una plaza vacante por doble exclusión, el equipo debe haber participado en el mismo torneo, haber sido eliminado en una instancia anterior, cumplir las condiciones deportivas y reglamentarias de la ronda y no tener una exclusión o sanción que le impida continuar. La selección corresponde al organizador autorizado y debe quedar auditada.

### RN-TOR-015 — Avance del rival en fase eliminatoria\nCuando un equipo sea retirado o descalificado durante una fase eliminatoria, el rival contra el que se encontraba enfrentado en esa instancia avanzará a la siguiente fase. El torneo continuará con normalidad conforme al calendario y cuadro resultantes. Si ambos equipos del enfrentamiento están excluidos, no habrá avance automático y se aplicará el procedimiento de doble exclusión definido en DEC-PORT-049/051.\n\n### RN-TOR-019 — No reincorporación tras retiro voluntario
Todo equipo que sea registrado como retirado o que abandone el torneo no podrá reincorporarse a esa misma edición, aunque la competición continúe en curso. La salida será definitiva para esa edición y el equipo conservará su registro histórico con el estado correspondiente.

### RN-TOR-018 — Decisión posterior al cierre oficial del torneo
Una vez que el torneo haya sido declarado oficialmente finalizado, FUTNEXIS no ejecutará automáticamente cambios deportivos, administrativos ni estadísticos por un retiro o descalificación posterior. Cualquier decisión o medida sobre esa situación queda exclusivamente a criterio del organizador del torneo; la plataforma no modificará automáticamente resultados, clasificación, estadísticas ni reconocimientos ya cerrados.

### RN-TOR-017 — Declaración de campeón por retiro/descalificación en la final
Si uno de los finalistas es retirado o descalificado y el rival permanece activo, el rival será declarado campeón del torneo. La final quedará registrada con el marcador administrativo común vigente, aplicado a favor del equipo campeón. Se actualizarán las métricas colectivas correspondientes y no se generarán estadísticas individuales. Si ambos finalistas están excluidos, no se declarará campeón automáticamente ni se asignará marcador; se aplicará el procedimiento de doble exclusión y la resolución corresponderá al organizador autorizado.

### RN-TOR-016 — Registro del marcador administrativo en avance eliminatorio\nEl enfrentamiento que habilita el avance del rival activo quedará registrado oficialmente con el marcador administrativo común vigente del torneo, aplicado a favor del equipo que avanza, independientemente de la localía. Se actualizarán las métricas colectivas correspondientes y no se generarán estadísticas individuales. Esta regla no aplica a enfrentamientos con doble exclusión.\n\n### RN-TOR-014 — Resolución de partidos futuros pendientes
Al registrar el retiro o descalificación, todos los partidos futuros que permanezcan pendientes contra equipos activos se resolverán administrativamente con el marcador común vigente del torneo, siempre a favor del equipo activo y sin importar la localía. Los partidos pendientes contra equipos también excluidos no recibirán marcador ni efectos estadísticos. No se generarán estadísticas individuales por ningún resultado administrativo.

### RN-TOR-010 — Configuración y modificación del marcador administrativo
El organizador configura el marcador administrativo común al crear el torneo. Durante la competición podrá modificarlo mediante una acción autorizada y auditada. La modificación tendrá efecto únicamente sobre las resoluciones administrativas que se apliquen después del cambio; no alterará automáticamente resultados administrativos ya aplicados. Cualquier corrección de un resultado anterior requerirá un procedimiento explícito, autorizado y auditado.

## 6. Permisos y controles

- Podrán ejecutar el retiro/descalificación el propietario de la organización, un administrador con permiso de gestión deportiva o el organizador/director del torneo con permiso específico asignado.
- El operador de resultados podrá registrar incidencias o solicitudes, pero no ejecutar por sí mismo el retiro/descalificación.
- La capacidad de resolver encuentros y aplicar resultados administrativos debe depender de permisos administrativos del torneo.
- La selección de equipos para cubrir una plaza vacante corresponde al organizador autorizado.
- Las operaciones deben validarse en servidor; no basta con ocultar acciones en la interfaz.
- Cada operación debe conservar trazabilidad para revisión posterior.

**Pendiente:** definir la matriz granular de permisos, incluyendo quién autoriza y ejecuta la resolución administrativa de partidos y quién puede seleccionar equipos para plazas vacantes.

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
- **CA-TOR-011:** el sistema valida participación en el mismo torneo, eliminación en instancia anterior, compatibilidad reglamentaria/deportiva y ausencia de exclusión o sanción impeditiva antes de aceptar al sustituto.
- **CA-TOR-012:** cuando la decisión de retiro/descalificación afecta un enfrentamiento ya disputado contra el rival correspondiente, únicamente ese partido sustituye su marcador oficial por el administrativo común a favor de ese rival; los demás partidos anteriores conservan sus resultados y el original afectado queda en auditoría.
- **CA-TOR-013:** todos los partidos futuros pendientes contra equipos activos se resuelven con el marcador administrativo común vigente, a favor del equipo activo y con efectos colectivos; los pendientes contra equipos excluidos no reciben marcador ni efectos estadísticos.\n- **CA-TOR-014:** en fase eliminatoria, al excluirse uno de los dos equipos enfrentados, el rival activo avanza a la siguiente fase y el torneo continúa conforme al cuadro actualizado; la doble exclusión sigue el procedimiento especial sin avance automático.\n- **CA-TOR-015:** el enfrentamiento eliminatorio queda registrado con el marcador administrativo común a favor del rival que avanza; se actualizan métricas colectivas y no se generan estadísticas individuales. La doble exclusión no recibe marcador.\n- **CA-TOR-016:** si uno de los finalistas es retirado/descalificado y el otro permanece activo, el activo es declarado campeón; la final se registra con el marcador administrativo común a su favor, con efectos colectivos y sin estadísticas individuales. Si ambos están excluidos, no se declara campeón automáticamente ni se asigna marcador.
- **CA-TOR-017:** después del cierre oficial del torneo, un retiro/descalificación posterior no provoca cambios automáticos en resultados, clasificación, estadísticas ni reconocimientos; cualquier medida queda a decisión del organizador.
- **CA-TOR-018:** todo equipo retirado o que haya abandonado el torneo no puede reincorporarse a la misma edición, incluso si sigue en curso; el intento debe rechazarse y conservarse su estado e historial.

## 9. Dependencias y riesgos

- Depende del modelo de datos de torneo, equipo participante, partido, resultado, clasificación y auditoría.
- La aplicación del marcador debe ser transaccional para evitar resultados parciales o duplicados.
- El recálculo debe respetar el reglamento específico del torneo.
- La distinción entre historial y clasificación vigente debe reflejarse tanto en consultas como en interfaces.
- Las reglas de sustitución en cuadros deben validarse contra la estructura real de la competición.

## 10. Pendientes de definición

1. [RESUELTO — DEC-TOR-001] El marcador se configura al crear el torneo; puede modificarse durante la competición mediante acción autorizada y auditada. El cambio aplica a resoluciones futuras y no modifica resultados ya aplicados.
2. ¿Qué roles pueden registrar un retiro/descalificación y confirmar la resolución administrativa?
3. [RESUELTO — DEC-TOR-003] Para ser elegible, el equipo debe haber participado en el mismo torneo, haber sido eliminado en una instancia anterior, cumplir las condiciones deportivas y reglamentarias de la ronda y no tener exclusión o sanción que le impida continuar.
4. [RESUELTO — DEC-TOR-004] La sustitución por marcador administrativo se limita exclusivamente al partido que motivó la decisión. Los demás partidos disputados anteriormente conservan sus resultados y estadísticas originales.
5. [RESUELTO — DEC-TOR-005] Todos los partidos futuros pendientes contra equipos activos se resuelven administrativamente con el marcador común; los partidos contra equipos excluidos no reciben marcador ni efectos estadísticos.\n6. [RESUELTO — DEC-TOR-006] En fase eliminatoria, el rival activo del equipo retirado/descalificado avanza a la siguiente fase y el torneo continúa normalmente. La doble exclusión conserva su tratamiento especial.\n7. [RESUELTO — DEC-TOR-007] El partido que habilita ese avance se registra con el marcador administrativo común a favor del rival activo, con efectos colectivos y sin estadísticas individuales.\n8. [RESUELTO — DEC-TOR-008] Si el retiro/descalificación ocurre en la final y el rival está activo, este es declarado campeón con el marcador administrativo común; se aplican efectos colectivos y no individuales.
9. [RESUELTO — DEC-TOR-009] Después del cierre oficial, FUTNEXIS no realiza cambios automáticos; cualquier decisión corresponde al organizador.
10. [RESUELTO — DEC-TOR-010] Todo equipo retirado o que abandone el torneo no puede reincorporarse a esa misma edición.
6. ¿Qué estados exactos tendrá un partido no disputado por doble exclusión y cómo se mostrará en calendario, actas e informes?
7. ¿Cómo se recalculan o congelan las clasificaciones históricas al cierre del torneo?
8. ¿Qué procedimiento autorizado permite corregir un retiro/descalificación registrado por error?

## 11. Historial de cambios

- **2.2 — 2026-11-02:** precisa que la no reincorporación aplica a cualquier equipo retirado o que abandone el torneo.
- **2.1 — 2026-11-02:** incorpora DEC-TOR-010: se prohíbe la reincorporación de cualquier equipo retirado o que haya abandonado la misma edición del torneo.
- **2.0 — 2026-11-02:** incorpora DEC-TOR-009: después del cierre oficial, cualquier medida por retiro/descalificación queda a decisión del organizador y no genera cambios automáticos.
- **1.9 — 2026-11-02:** incorpora DEC-TOR-008 sobre declaración de campeón cuando el rival de la final es retirado/descalificado.\n- **1.8 — 2026-11-02:** incorpora DEC-TOR-007 sobre registro del marcador administrativo en el avance eliminatorio.\n- **1.7 — 2026-11-02:** incorpora DEC-TOR-006 sobre avance del rival en fase eliminatoria y continuidad del torneo.\n- **1.6 — 2026-11-02:** incorpora DEC-TOR-005 sobre resolución de todos los partidos futuros pendientes contra equipos activos.
- **1.5 — 2026-11-02:** precisa que DEC-TOR-004 afecta exclusivamente al partido que motivó la decisión; los demás encuentros anteriores conservan sus resultados y estadísticas.
- **1.4 — 2026-11-02:** incorpora DEC-TOR-004 sobre el efecto de retiro/descalificación en el enfrentamiento ya disputado contra el rival correspondiente.
- **1.3 — 2026-11-02:** incorpora DEC-TOR-003 sobre condiciones de elegibilidad de equipos sustitutos ante doble exclusión.
- **1.2 — 2026-11-02:** incorpora DEC-TOR-002 sobre autorización de retiro/descalificación y alcance del operador de resultados.
- **1.1 — 2026-11-02:** incorpora DEC-TOR-001 sobre configuración inicial, modificación auditada y no retroactividad del marcador administrativo.
- **1.0 — 2026-11-02:** primera formalización parcial de las reglas aprobadas en DEC-PORT-048 a DEC-PORT-051.
