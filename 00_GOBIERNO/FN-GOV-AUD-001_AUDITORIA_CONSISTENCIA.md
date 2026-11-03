# FN-GOV-AUD-001 — Auditoría de consistencia documental

- **Versión:** 1.3
- **Estado:** EN_REVISION
- **Fecha:** 2026-11-02
- **Alcance:** documentos de organizaciones y portal público disponibles en la rama main.
- **Tipo de revisión:** consistencia interna, trazabilidad, referencias y alineación con decisiones registradas. No sustituye la comparación literal con los WritingBlocks históricos.

## 1. Resultado ejecutivo

Los documentos ofrecen una base funcional coherente para continuar la definición del producto, pero todavía no deben entregarse a desarrollo como especificaciones aprobadas. Se detectaron inconsistencias de inventario, numeración, referencias cruzadas y algunos requisitos que requieren mayor precisión.

No se modifican decisiones de negocio mediante esta auditoría. Las correcciones documentales propuestas deben incorporarse en las siguientes versiones de cada documento.

## 2. Hallazgos

| ID | Severidad | Documento/área | Hallazgo | Acción recomendada |
|---|---|---|---|---|
| AUD-FN-001 | Alta | Inventario | El encabezado declara versión 1.0 mientras la fila del propio inventario declara 1.2. Además, los documentos ya incorporados aparecen en una sección llamada “aún no incorporados íntegramente”. | Corregir versión del encabezado, separar documentos vigentes en GitHub de las versiones históricas/base pendientes de comparación e incluir FN-GOV-DEC-002. |
| AUD-FN-002 | Alta | FN-MOD-ORG-002 | La consolidación usa RF-PORT-001 a 047 y CA-PORT-001 a 024, mientras la versión de trabajo conocida se describía con 42 RF y 33 CA. No se ha verificado equivalencia uno a uno. | No tratar la numeración consolidada como continuidad literal. Preparar matriz de trazabilidad contra v1.4 original y recuperar requisitos/criterios faltantes. |
| AUD-FN-003 | Alta | FN-GOV-DEC-002 / FN-MOD-ORG-002 | DEC-PORT-009 se usa en CA-PORT-006 para justificar el orden de grupos, pero el orden también está expresado en DEC-PORT-011; DEC-PORT-036 define los estados. | Corregir la referencia para apuntar a DEC-PORT-011 y DEC-PORT-036, según el aspecto verificado. |
| AUD-FN-004 | Media | FN-MOD-ORG-002 | Se presenta RF-PORT-012 como propuesta pendiente, lo cual es correcto, pero la UX también debe mantener explícitamente el mismo estado en criterios de aceptación y futuras maquetas. | Mantener filtros fuera de los criterios aprobados hasta decisión expresa. |
| AUD-FN-005 | Resuelto en definición funcional | FN-MOD-ORG-002 / DEC-PORT-044 | La definición del tercer desempate fue precisada y aprobada: participación efectiva en partidos oficiales, titular o suplente; convocatoria sin ingreso excluida; varios goles en un partido cuentan como un encuentro; partidos anulados excluidos. | Incorporado en RN-PORT-009 y CA-PORT-041. Mantener pruebas de aceptación y validar consistencia con el módulo operativo de partidos. |
| AUD-FN-006 | Parcial | DEC-PORT-038 y DEC-PORT-045 a DEC-PORT-048 | El algoritmo base contempla formatos mixtos, grupos, rondas previas y cuadros escalonados: se compara la instancia alcanzada y, a igual instancia, la menor cantidad de partidos oficiales disputados en rondas eliminatorias. | Validar casos límite: estructuras especiales; reclasificaciones que formen parte del camino al título; comparación de mejores terceros entre grupos; retiro/descalificación con partidos pendientes; reajuste de tabla; y exclusión de estadísticas individuales en resultados administrativos. |
| AUD-FN-007 | Alta | FN-MOD-ORG-001 | Se indica un propietario principal activo único, pero falta especificar concurrencia y transacción para transferencias simultáneas, revocación del propietario y recuperación de cuenta. | Definir invariantes y escenarios de concurrencia en arquitectura y reglas de negocio. |
| AUD-FN-008 | Media | FN-MOD-ORG-001 | Los estados de organización, membresía e invitación están enumerados, pero no existe matriz de transiciones ni reglas completas de quién puede ejecutarlas. | Incorporar diagramas/tablas de transición y permisos por estado. |
| AUD-FN-009 | Media | FN-UX-ORG-001 | La codificación de pantallas es parcial y se advierte una posible colisión ORG-06/ORG-16 sin resolver. | Congelar inventario de pantallas y rutas antes de wireframes definitivos. |
| AUD-FN-010 | Alta | FN-MOD-ORG-002 / DEC-PORT-039 | “Suspendido” depende de resolución oficial, pero no se define el estado final posible ni el efecto retroactivo sobre actas, goles, tarjetas y clasificación. | Definir resoluciones posibles y reglas de recálculo/auditoría en módulo de partidos. |
| AUD-FN-011 | Media | FN-MOD-ORG-002 | El criterio “torneo más reciente” dentro de cada grupo no identifica el campo de orden (fecha de inicio, creación, temporada u otro). | Definir campo y desempate estable para el orden. |
| AUD-FN-012 | Parcial | FN-MOD-ORG-002 | El conteo de partidos del tercer desempate ya define participación efectiva dentro del torneo. La integración con cambios de equipo, participaciones parciales y la atribución del avance deportivo sigue dependiendo de las reglas de inscripción y del algoritmo de formatos mixtos. | Mantener cálculo ligado a la participación del jugador en el torneo; completar casos especiales y coordinación con módulo operativo/de inscripciones. |
| AUD-FN-013 | Media | FN-MOD-ORG-002 / FN-UX-ORG-002 | Se habla de páginas privadas, despublicadas, suspendidas y no disponibles, pero no hay catálogo único de respuestas HTTP, redirecciones, robots/noindex y caché. | Definir contrato técnico SEO/HTTP sin revelar existencia de datos privados. |
| AUD-FN-014 | Media | FN-MOD-ORG-002 | Se permite mostrar temporada/año, categoría y escenario, pero no hay catálogo de campos públicos por entidad ni reglas de obligatoriedad/visibilidad. | Definir matriz de campos públicos y permisos por entidad. |
| AUD-FN-015 | Media | FN-MOD-ORG-001 | La invitación vence a los siete días, pero no se define el instante exacto de vencimiento, zona horaria, reenvío, duplicados ni revocación después de aceptación. | Precisar política temporal y ciclo de vida de invitaciones. |
| AUD-FN-016 | Media | FN-MOD-ORG-002 | DEC-PORT-040 establece moderación y retirada, pero no define flujo de revisión, responsable, apelación, plazos ni tratamiento de contenido reportado. | Definir flujo operativo y permisos de moderación. |
| AUD-FN-017 | Alta | Todos | Los documentos son consolidaciones basadas en contexto y no transcripciones verificadas de las versiones históricas. | Mantener EN_REVISION y realizar comparación literal cuando se disponga de los documentos fuente. |

## 3. Alineaciones confirmadas

1. La publicación del portal de organización es independiente de la publicación de cada torneo.
2. La portada de organización muestra torneos publicados y no incorpora próximos partidos ni resultados.
3. Los estados deportivos del torneo son independientes de la publicación.
4. Las estadísticas públicas requieren confirmación oficial.
5. Los datos personales prohibidos no se publican.
6. El nombre deportivo se distingue del nombre real y es único dentro del torneo según la normalización definida.
7. La ficha pública del jugador conserva el historial por torneo/equipo.
8. La suspensión de organización oculta las rutas públicas asociadas sin borrar datos.
9. Las páginas públicas usan identificadores estables; la autorización administrativa depende de sesión y permisos.
10. Las reglas de organizaciones y portal público mantienen separación entre estado de organización, membresía, invitación y publicación.

## 4. Acciones prioritarias

### Prioridad 1 — Corrección documental
- Corregir inventario y registrar todos los archivos persistidos.
- Corregir referencias cruzadas y numeración.
- Crear matriz de trazabilidad entre decisiones, RF, RN, PER y CA.
- Recuperar texto histórico de DEC-PORT-001 a 008 y versiones fuente de los cuatro documentos de módulo/UX.

### Prioridad 2 — Cierre funcional
- Completar pruebas de aceptación de avance deportivo: reclasificaciones que integren el camino al título, comparación de mejores terceros y estructuras especiales.
- Validar retiro/descalificación, adjudicación de partidos pendientes, reajuste de posiciones y exclusión de estadísticas individuales en resultados administrativos.
- Definir estados y transiciones de partido.
- Definir estados y transiciones de organización, membresía e invitación.
- Definir campos y visibilidad pública por entidad.
- Resolver identificadores y mapa de pantallas ORG.

### Prioridad 3 — Preparación UX y arquitectura
- Cerrar wireframes y estados vacíos/errores.
- Definir comportamiento de SEO, caché y despublicación.
- Llevar las invariantes multitenant, autorización y auditoría a arquitectura.

## 5. Restricción de aprobación

Ninguno de los documentos revisados debe cambiar a APROBADO ni utilizarse como instrucción final de desarrollo hasta:
1. resolver los hallazgos bloqueantes;
2. completar trazabilidad;
3. validar criterios de aceptación;
4. obtener aprobación expresa del propietario del producto.

## 6. Historial
- 1.3 — 2026-11-02: incorpora DEC-PORT-047/048; AUD-FN-006 sigue parcialmente resuelto y amplía pruebas a comparación entre grupos, reclasificación y retiro/descalificación.
- 1.2 — 2026-11-02: actualiza AUD-FN-006 a parcialmente resuelto tras DEC-PORT-045/046; quedan pruebas de casos límite y estructuras especiales.
- 1.1 — 2026-11-02: actualiza AUD-FN-005 tras DEC-PORT-044; AUD-FN-012 permanece parcialmente abierto por dependencias deportivas y de inscripción.
- 1.0 — 2026-11-02: primera auditoría transversal de consistencia documental.
