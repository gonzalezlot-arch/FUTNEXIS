# FN-GOV-DEC-002 — Registro de decisiones del portal público

- Versión: 1.8
- Estado: EN_REVISION
- Fecha: 2026-11-02

## Propósito y trazabilidad
Consolida las decisiones conocidas del portal público de FUTNEXIS. DEC-PORT-001 a 008 existían en un registro anterior, pero su redacción literal no está disponible para verificación y no se reconstruye ni inventa. DEC-PORT-009 a 043 se registran como síntesis de las definiciones disponibles. DEC-PORT-036 a 049 fueron aprobadas expresamente el 2026-11-02.

## DEC-PORT-001 a 008
[PENDIENTE] Recuperar el texto literal desde la versión histórica original antes de declararlas consolidadas.

## DEC-PORT-009 a 035

| ID | Decisión consolidada |
|---|---|
| 009 | Mantener uniformidad visual y de navegación entre portales públicos. |
| 010 | Mostrar torneos publicados en cuadrícula con paginación. |
| 011 | Ordenar torneos: en curso, próximos y finalizados; dentro de cada grupo, del más reciente al más antiguo. |
| 012 | Tarjetas de torneo: nombre, escudo o imagen autorizada, modalidad, categoría, estado, temporada/año y acceso al torneo. |
| 013 | El torneo dispone de seis secciones: Información general, Equipos, Calendario, Resultados, Clasificación y Estadísticas individuales. |
| 014 | Secciones mediante pestañas horizontales; en móvil pueden desplazarse horizontalmente. |
| 015 | Calendario con vista por jornadas y lista cronológica. |
| 016 | Clasificación con posición, equipo, PJ, PG, PE, PP, GF, GC, DG y PTS. |
| 017 | Estadísticas individuales: goles, partidos jugados y tarjetas amarillas, rojas y azules. Asistencias excluidas. |
| 018 | Ranking general de goleadores: goles; avance deportivo del equipo; menor cantidad de partidos oficiales para alcanzar los goles. Empate residual comparte posición. El avance se amplía en DEC-PORT-038. |
| 019 | Rankings independientes de tarjetas amarillas, rojas y azules en orden descendente. |
| 020 | Cada jugador dispone de ficha pública individual. |
| 021 | La ficha conserva historial de participación por torneo y equipo. |
| 022 | Historial en tarjetas desplegables, orden cronológico inverso. |
| 023 | Ficha pública de equipo con identidad deportiva, torneos, plantilla, resultados, clasificación y estadísticas colectivas autorizadas. |
| 024 | Plantilla en tarjetas con número, nombre deportivo y posición. |
| 025 | Estadísticas colectivas por torneo: PJ, PG, PE, PP, GF, GC, DG, PTS y tarjetas amarillas, rojas y azules. |
| 026 | Ficha pública del equipo con secciones Resumen, Plantilla, Torneos y Resultados. |
| 027 | Resultados organizados y vinculados a sus partidos. |
| 028 | Detalle disciplinario con nombre deportivo, número y cantidad de tarjetas por jugador. |
| 029 | Ranking de goleadores de equipo por goles, mostrando nombre deportivo, número y goles. No hereda automáticamente los desempates del ranking general. |
| 030 | El ranking de goleadores del equipo se ubica en el resumen de su ficha pública. |
| 031 | La ficha del jugador presenta rendimiento desglosado por torneo. |
| 032 | Estadísticas del jugador presentadas de forma resumida y comprensible. |
| 033 | La ficha puede mostrar reconocimientos oficiales: goleador, campeón, subcampeón y distinciones individuales. |
| 034 | Solo se muestran reconocimientos confirmados oficialmente. |
| 035 | Navegación pública entre organización, torneo, equipo y jugador sin login, mediante identificadores estables. |

## DEC-PORT-036 — Clasificación pública de estados de torneos
[CONFIRMADO]
- Próximos: torneo publicado cuya competición no ha comenzado.
- En curso: la competición comenzó y no ha finalizado oficialmente.
- Finalizados: la competición terminó oficialmente.
- Estado de publicación y estado deportivo son conceptos independientes.

## DEC-PORT-037 — Publicación de estadísticas deportivas
[CONFIRMADO]
- Estadísticas públicas solo cuando el torneo está publicado y los datos correspondientes están confirmados oficialmente por la organización.
- Partidos pendientes de confirmación no alteran estadísticas públicas.
- Correcciones autorizadas actualizan los datos publicados.
- Datos personales privados nunca se publican.
- Aplica a estadísticas individuales y colectivas, goleadores y rankings disciplinarios.

## DEC-PORT-038 — Avance deportivo como segundo criterio de desempate
[CONFIRMADO]
Ranking general de goleadores, en orden: (1) mayor cantidad de goles; (2) mayor avance deportivo del equipo con el que participa el jugador en ese torneo; (3) menor cantidad de partidos oficiales jugados para alcanzar esa cantidad de goles. Si persiste el empate, comparten posición.
- En formatos eliminatorios se consideran campeón, subcampeón, eliminado en semifinales, cuartos, octavos y fases anteriores/grupos.
- En formatos de liga sin eliminatorias se considera la posición final del equipo.
- En formatos mixtos se determina el avance por la fase más avanzada alcanzada oficialmente; la fase eliminatoria prevalece sobre la eliminación en grupos.
- Entre equipos eliminados en la misma fase, se utiliza la posición final oficial establecida por el reglamento. Si no diferencia posiciones, comparten nivel de avance.
- [PENDIENTE] Precisar estructuras especiales no clasificables con estas reglas generales.

## DEC-PORT-039 — Partidos aplazados, suspendidos, anulados y reprogramados
[CONFIRMADO]
- Aplazado: no genera resultados ni estadísticas hasta disputarse y confirmarse.
- Suspendido: no genera estadísticas definitivas hasta resolución oficial.
- Anulado: resultado y estadísticas asociadas se excluyen de registros oficiales.
- Reprogramado: conserva identidad del partido, no crea duplicado.
- Cambios de estado auditan usuario, fecha, motivo, estado anterior y nuevo.
- Partidos anulados se conservan para trazabilidad, aunque excluidos de cálculos oficiales.

## DEC-PORT-040 — Moderación y control de contenido institucional
[CONFIRMADO]
- Permitidos logotipos de organizaciones, escudos de equipos y banners institucionales.
- No se permiten fotografías personales ni datos privados.
- Validar formato, tamaño, dimensiones y tipo de archivo.
- Rechazar contenido ofensivo, ilegal, engañoso o que infrinja derechos de terceros.
- FUTNEXIS puede retirar contenido incumplidor.
- Auditar cargas y modificaciones.
- La versión vigente permanece visible mientras se revisa una nueva versión.

## DEC-PORT-041 — Disponibilidad de portales de organizaciones suspendidas
[CONFIRMADO]
- Al suspender una organización por superadmin, el portal público y las rutas públicas asociadas a torneos, equipos y jugadores dejan de mostrar contenido.
- Los datos se conservan.
- Usuarios administrativos pierden capacidad de gestionar contenidos durante la suspensión.
- Superadmin puede consultar y gestionar reactivación.
- Mensaje neutral: “Este portal no se encuentra disponible actualmente”.
- Reactivar restablece la disponibilidad de los contenidos que sigan publicados.

## DEC-PORT-042 — SEO e indexación
[CONFIRMADO]
- Buscadores pueden indexar portales, torneos, equipos y jugadores publicados.
- Páginas privadas, borradores y contenidos no publicados quedan excluidos.
- Definir títulos, descripciones y URLs amigables.
- Al despublicar o suspender una organización se solicita retirada de índices mediante mecanismos compatibles.
- Metadatos y resultados no exponen información privada.

## DEC-PORT-043 — Auditoría y conservación de registros
[CONFIRMADO]
- Registrar publicaciones, despublicaciones, modificaciones y cambios de estado con usuario, fecha, acción, entidad afectada y motivo cuando corresponda.
- Conservar historial aunque el contenido deje de ser público.
- Acceso a auditoría restringido a autorizados.
- Plazos de conservación conforme a política de privacidad/protección de datos, aún por definir.

## DEC-PORT-044 — Definición de partidos jugados para el tercer desempate de goleadores
[CONFIRMADO — aprobación expresa del propietario del producto, 2026-11-02]

Para el tercer criterio del ranking general de goleadores —menor cantidad de partidos oficiales jugados para alcanzar la misma cantidad de goles— se aplican estas reglas:
- Se cuentan los partidos oficiales del torneo en los que el jugador haya ingresado efectivamente al campo, desde el inicio del torneo hasta el partido en el que alcance la cantidad de goles que se está comparando.
- Una convocatoria sin ingreso al campo no cuenta como partido jugado.
- La participación como titular o suplente cuenta como un partido jugado.
- Varios goles anotados por el jugador en un mismo partido cuentan dentro de su total de goles, pero ese encuentro cuenta como un solo partido jugado.
- Solo se consideran partidos oficiales del torneo.
- Los partidos anulados quedan excluidos del conteo.

## DEC-PORT-045 — Algoritmo base de avance deportivo para formatos mixtos
[CONFIRMADO — aprobación expresa del propietario del producto, 2026-11-02]

El segundo criterio de desempate del ranking general de goleadores se determina por el avance deportivo del equipo del jugador en el torneo:
- Se considera la fase más avanzada alcanzada oficialmente por el equipo, incluso si el torneo combina grupos y eliminación directa.
- La jerarquía base es: campeón, subcampeón, eliminado en semifinales, cuartos de final, octavos de final, rondas eliminatorias anteriores y fase de grupos.
- Alcanzar una fase eliminatoria prevalece sobre quedar eliminado en fase de grupos.
- Entre equipos eliminados en la misma fase, se utiliza la posición final oficial que establezca el reglamento. Si no hay posiciones diferenciadas, comparten nivel de avance.
- En torneos de liga sin eliminación directa, se utiliza la posición final oficial de la clasificación.
- Las estructuras especiales no clasificables con estas reglas requieren definición específica antes de calcular el ranking.

## DEC-PORT-046 — Avance deportivo en cuadros escalonados y comparación por partidos disputados
[CONFIRMADO — aprobación expresa del propietario del producto, 2026-11-02]

Esta decisión complementa DEC-PORT-045 para torneos donde los equipos ingresan a distintas rondas de eliminación según su posición en una fase inicial de liga o grupos. El organizador configura clasificados, ingresos directos, rondas previas, cruces y condiciones de avance.

- La estructura puede combinar liga o grupos con rondas previas y rondas eliminatorias escalonadas; no se fija cantidad de grupos, equipos clasificados ni rondas.
- El avance se determina por la instancia oficial más avanzada alcanzada dentro del camino hacia el campeonato. La clasificación directa no constituye por sí sola una instancia superior.
- Cuando dos equipos alcanzan la misma instancia, tiene prioridad el que haya disputado menor cantidad de partidos oficiales en las rondas eliminatorias necesarias para alcanzarla.
- Se cuentan todos los partidos oficiales disputados en esas rondas, ganados, empatados o perdidos. Se incluyen rondas previas y eliminatorias del recorrido.
- No se incluyen partidos de la fase inicial de liga o grupos.
- Los partidos anulados se excluyen; los suspendidos se consideran según su resolución oficial.
- Si persiste el empate, se utiliza la posición final oficial del reglamento. Si no hay diferencia oficial, comparten nivel de avance.
- Las rondas de reclasificación que formen parte del recorrido oficial hacia el título sí se consideran para determinar el avance deportivo y la cantidad de partidos eliminatorios disputados. Las rondas que no formen parte de ese recorrido no aumentan el avance deportivo, aunque pueden determinar posiciones finales si el reglamento así lo establece.
- La estructura y las reglas configuradas para cada torneo gobiernan el cálculo; FUTNEXIS no impone formatos ni criterios universales de clasificación.

## DEC-PORT-047 — Posición oficial de equipos eliminados en fase de grupos
[CONFIRMADO — aprobación expresa del propietario del producto, 2026-11-02]

- Cuando equipos queden eliminados en fase de grupos, se compara su posición oficial dentro del grupo si el reglamento establece esa diferencia.
- Entre equipos de grupos distintos, solo se comparan posiciones mediante la tabla general, tabla de mejores terceros u otro mecanismo comparativo definido por el reglamento y configurado por el organizador.
- FUTNEXIS no impone criterios universales para ordenar mejores terceros.
- Si no existe un criterio reglamentario válido para diferenciar a los equipos comparados, comparten nivel de avance.
- Una vez que un mejor tercero clasifica a una fase eliminatoria, su posición original de tercero deja de utilizarse para comparar su avance deportivo. Desde ese momento se aplican DEC-PORT-045 y DEC-PORT-046, incluida la comparación de partidos eliminatorios disputados cuando corresponda.

## DEC-PORT-048 — Retiro o descalificación de equipos y resultados administrativos
[CONFIRMADO — aprobación expresa del propietario del producto, 2026-11-02]

- Un equipo que abandone el torneo o sea descalificado cambia a un estado que identifique la causa: retirado o descalificado. Deja de integrar la clasificación competitiva vigente y se excluye del total de equipos activos.
- La clasificación se recalcula únicamente entre equipos activos y sus posiciones se reajustan consecutivamente, sin reservar puestos a equipos retirados o descalificados.
- Los resultados y puntos obtenidos por el equipo antes de su retiro o descalificación se conservan en el historial oficial; no se eliminan ni recalculan retroactivamente.
- Todos los partidos pendientes de ese equipo se resuelven administrativamente a favor de cada rival al que faltaba enfrentar. Se otorgan a cada rival los puntos correspondientes a una victoria administrativa.
- El marcador administrativo aplicable a esos encuentros lo establece el organizador del torneo. El sistema registra el marcador, los puntos asignados, el estado/causa del equipo y la resolución administrativa.
- Los resultados administrativos tienen efectos exclusivamente colectivos: resultado del encuentro, puntos, goles a favor/en contra, diferencia de goles y clasificación/estadísticas colectivas.
- Los resultados administrativos no generan goles, partidos jugados, asistencias, tarjetas ni ninguna otra estadística individual. Tampoco afectan rankings o reconocimientos individuales.
- Los partidos disputados antes del retiro o descalificación mantienen sus resultados y estadísticas originales, incluidas las individuales.
- La decisión, causa, fecha, responsable, marcador administrativo configurado y partidos afectados deben conservar trazabilidad/auditoría.

## DEC-PORT-049 — Doble exclusión en enfrentamiento eliminatorio
[CONFIRMADO — aprobación expresa del propietario del producto, 2026-11-02]

Cuando los dos equipos de un enfrentamiento eliminatorio estén retirados o descalificados antes de disputar el partido:
- Ninguno avanza automáticamente.
- El encuentro se registra como no disputado por exclusión de ambos equipos.
- No se asignan puntos ni marcador administrativo.
- FUTNEXIS no selecciona automáticamente un equipo sustituto.
- El organizador decide cómo completar la plaza o resolver el cuadro.
- La decisión registra responsable, fecha, motivo y resolución.
- No se generan estadísticas individuales ni efectos colectivos de resultado para ese encuentro.

## Reglas transversales de privacidad y navegación
- Cada organización configura nombre, logotipo, colores, descripción y contacto; FUTNEXIS conserva su identidad de plataforma.
- Portal público desactivado por defecto y requiere publicación autorizada.
- Publicar el portal no publica automáticamente los torneos; cada torneo tiene publicación independiente.
- No se publican nombres reales, fotografías personales, documentos, teléfonos, correos, direcciones ni datos sensibles de jugadores, delegados o árbitros.
- El jugador se identifica mediante nombre deportivo; el nombre real permanece privado.
- Nombre deportivo único dentro del torneo, ignorando mayúsculas/minúsculas y espacios sobrantes al inicio/final. Puede repetirse en torneos distintos.
- Cada jugador tiene identificador público estable, independiente del nombre deportivo.
- La ficha conserva historial por torneo y equipo; el equipo actual se mantiene separado.
- Cambios de equipo entre torneos permitidos sin alterar estadísticas ni inscripciones históricas.
- No se permite cambiar de equipo durante el mismo torneo, independientemente de fase, jornada o estado del partido. Correcciones excepcionales por error administrativo requieren autorización, justificación y auditoría.
- Estadísticas vinculadas al ID interno del jugador y su participación en el torneo, no al texto del nombre deportivo.
- Rutas públicas: /o/{identificador}, /e/{identificador}, /jugador/{identificador} y /torneo/{identificador}. Contexto administrativo determinado por sesión/permisos, nunca por URL solamente.
- Identificadores públicos únicos, permanentes y no reutilizables.
- Dominio central FUTNEXIS; no se definieron subdominios por organización.

## Pendientes
1. Recuperar DEC-PORT-001 a 008 literalmente.
2. Confirmar filtros por estado y modalidad en portada; por ahora [PROPUESTA]/[PENDIENTE].
3. Precisar únicamente estructuras especiales no cubiertas por DEC-PORT-045 y DEC-PORT-046.
4. Definir plazos de conservación de auditoría y política de privacidad.
5. Definir responsables, tiempos y apelaciones de moderación.
6. Definir estados completos de publicación y transiciones.
7. Definir detalles técnicos SEO y tiempos de retirada de índices.
8. Precisar autorización para publicar estadísticas por torneo/categoría.
9. Definir correcciones estadísticas posteriores al cierre de partido/torneo.
10. Definir disponibilidad pública ante estados distintos de suspensión de organización.

## Observaciones de consistencia
- En FN-MOD-ORG-002 v1.4, los filtros de estado/modalidad aparecen como requisitos pese a no tener aprobación explícita; deben quedar como propuesta pendiente.
- CA-PORT-009 contiene una referencia incorrecta a RN-PORT-015; corregir en próxima revisión.
- DEC-PORT-038 aprueba el principio de avance deportivo, no el algoritmo de formatos mixtos.

## Historial
- 1.8 — 2026-11-02: incorpora DEC-PORT-049 sobre doble exclusión en enfrentamiento eliminatorio; se complementa DEC-PORT-048 con la resolución de encuentros entre dos equipos excluidos.
- 1.7 — 2026-11-02: incorpora DEC-PORT-047 y DEC-PORT-048, y precisa que las reclasificaciones que formen parte del camino al título cuentan para el avance deportivo.
- 1.6 — 2026-11-02: incorpora DEC-PORT-046 para cuadros escalonados y comparación por partidos oficiales disputados; elimina duplicación de DEC-PORT-045.
- 1.5 — 2026-11-02: incorpora DEC-PORT-045, que define el algoritmo base de avance deportivo para formatos mixtos.
- 1.4 — 2026-11-02: incorpora DEC-PORT-044, que precisa el conteo de partidos para el tercer desempate del ranking general de goleadores.
- 1.3 — 2026-11-02: consolidación de DEC-PORT-009 a 043, reglas transversales y pendientes. DEC-PORT-001 a 008 quedan expresamente pendientes de recuperación literal.
- 1.2 — versión de trabajo conocida; pendiente comparar contenido histórico íntegro.
