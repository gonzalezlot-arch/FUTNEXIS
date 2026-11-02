# FN-UX-ORG-002 — UX/UI: portal público

- **Versión documental:** 1.5
- **Estado:** EN_REVISION
- **Fecha de consolidación:** 2026-11-02
- **Base conocida:** versión 1.4, WritingBlock de referencia 61483
- **Nota de integridad:** consolidación de definiciones conocidas; no es copia literal verificada de la versión 1.4.

## 1. Objetivo

Definir la experiencia de navegación y presentación de los portales públicos de organización, torneo, equipo y jugador.

## 2. Principios UX

- Experiencia uniforme y reconocible en todos los portales.
- Identidad institucional configurable, sin sustituir la marca FUTNEXIS.
- Diseño responsive para escritorio, tablet, móvil y PWA.
- Navegación pública sin autenticación.
- No presentar datos personales prohibidos.
- Diferenciar claramente información deportiva oficial de estados pendientes.
- Estados de carga, vacío, error, despublicación y suspensión diseñados explícitamente.

## 3. PORT-01 — Portal de organización

### Estructura
1. Cabecera institucional: logotipo, nombre y navegación.
2. Presentación: descripción y datos institucionales autorizados.
3. Sección de torneos publicados.
4. Paginación.
5. Pie de página con identidad de FUTNEXIS y enlaces institucionales autorizados.

### Tarjetas de torneo
Cada tarjeta puede mostrar:
- Nombre del torneo.
- Escudo o imagen autorizada.
- Modalidad y categoría.
- Estado deportivo: próximo, en curso o finalizado.
- Temporada/año.
- Acción “Ver torneo”.

Presentación en cuadrícula adaptable. Orden: en curso, próximos y finalizados; dentro de cada grupo, del más reciente al más antiguo.

**No mostrar en PORT-01:** próximos partidos ni resultados.

**Pendiente:** filtros por estado y modalidad; se mantienen como propuesta hasta aprobación expresa.

## 4. PORT-04 — Portal de torneo

### Navegación
Seis pestañas horizontales:
1. Información general.
2. Equipos.
3. Calendario.
4. Resultados.
5. Clasificación.
6. Estadísticas individuales.

En móvil, las pestañas deben permitir desplazamiento horizontal y conservar indicadores visibles de sección activa.

### Información general
Presentar datos identificativos y descriptivos del torneo que hayan sido autorizados para publicación.

### Equipos
Listado de equipos participantes con escudos autorizados y navegación a sus fichas públicas.

### Calendario
- Vista por jornadas.
- Vista de lista cronológica.
- Filtros por equipo y estado.
- Cada partido muestra equipos, escudos autorizados, fecha/hora, jornada, escenario, estado y marcador confirmado si existe.
- Partidos aplazados, suspendidos o pendientes deben distinguirse de partidos confirmados.
- Partidos anulados no deben confundirse con resultados oficiales.

### Resultados
Presentar marcadores confirmados y su contexto de jornada/fecha. No mostrar como definitivo un resultado pendiente de confirmación o resolución.

### Clasificación
Tabla con posición, equipo, PJ, PG, PE, PP, GF, GC, DG y PTS. En móvil, usar una presentación legible, permitiendo desplazamiento horizontal si es necesario.

### Estadísticas individuales
- Ranking general de goleadores con criterios de desempate DEC-PORT-038.
- Rankings separados de tarjetas amarillas, rojas y azules.
- Mostrar nombre deportivo y datos estadísticos autorizados.
- No incluir asistencias en el alcance actual.
- Datos solo cuando estén confirmados oficialmente.

## 5. PORT-02 — Página pública de equipo

### Estructura
Cabecera con escudo, nombre, categoría y descripción autorizada. Navegación mediante:
- Resumen.
- Plantilla.
- Torneos.
- Resultados.

### Resumen
Mostrar indicadores colectivos por torneo, ranking de goleadores del equipo y resumen disciplinario.

### Plantilla
Tarjetas con número de camiseta, nombre deportivo y posición. Cada tarjeta enlaza a la ficha pública del jugador.

### Torneos y resultados
Presentar participación e historial deportivo por torneo, con resultados y clasificación cuando estén publicados y confirmados.

### Detalle disciplinario
Permitir consultar nombre deportivo, número y cantidad de tarjetas por jugador.

### Ranking de goleadores del equipo
Orden descendente por goles; mostrar nombre deportivo, número y cantidad de goles. No aplicar los criterios de desempate del ranking general.

## 6. PORT-03 — Ficha pública de jugador

### Cabecera
Mostrar nombre deportivo y datos deportivos autorizados. No mostrar nombre real ni fotografía personal.

### Resumen estadístico
Mostrar estadísticas individuales disponibles y confirmadas.

### Rendimiento por torneo
Desglosar estadísticas por torneo y equipo con el que participó.

### Historial
Mostrar participaciones históricas por torneo, con tarjetas desplegables en orden cronológico inverso. El equipo actual debe distinguirse del historial.

### Reconocimientos
Mostrar goleador, campeón, subcampeón y distinciones individuales únicamente cuando estén confirmados oficialmente.

## 7. Navegación pública

Flujo principal:
- Organización → torneo.
- Torneo → equipo.
- Equipo → jugador.
- Ranking → ficha individual del jugador.
- Jugador → historial por torneo y equipo.

Rutas:
- Organización: `/o/{identificador}`
- Equipo: `/e/{identificador}`
- Jugador: `/jugador/{identificador}`
- Torneo: `/torneo/{identificador}`

No exigir login para contenido público publicado. Los enlaces a contenido privado, despublicado o no disponible deben mostrar un estado neutral sin revelar información privada.

## 8. Estados visuales y mensajes

Diseñar componentes para:
- Cargando.
- Sin torneos publicados.
- Sin equipos o jugadores disponibles públicamente.
- Partido pendiente, aplazado, suspendido, reprogramado o anulado.
- Estadísticas aún no disponibles/confirmadas.
- Portal despublicado.
- Organización suspendida: “Este portal no se encuentra disponible actualmente”.
- Recurso inexistente o identificador no válido.
- Error temporal de carga.

## 9. Privacidad y contenido visual

- No usar fotografías personales de jugadores, delegados, árbitros o participantes.
- Permitidos logotipos institucionales, escudos y banners institucionales validados.
- No exponer documentos, teléfonos, correos, direcciones ni nombres reales.
- Evitar que metadatos, texto alternativo, datos estructurados o vistas previas filtren información privada.

## 10. Responsive y accesibilidad

- Cuadrículas adaptables a anchura disponible.
- Tablas deportivas legibles en móvil.
- Pestañas con navegación accesible por teclado.
- Contraste suficiente y foco visible.
- Estados no comunicados exclusivamente por color.
- Textos y controles comprensibles, con jerarquía visual consistente.

## 11. Criterios de aceptación UX iniciales

1. PORT-01 presenta cabecera, información institucional, torneos y pie de página.
2. PORT-01 solo presenta torneos publicados.
3. PORT-01 no presenta próximos partidos ni resultados.
4. Las tarjetas se adaptan a escritorio y móvil.
5. Los grupos y el orden de torneos coinciden con las reglas funcionales.
6. PORT-04 presenta las seis secciones definidas.
7. Las pestañas funcionan en escritorio y móvil.
8. El calendario permite vistas por jornada y lista.
9. Clasificación presenta las columnas acordadas y es legible en móvil.
10. Los rankings distinguen goleadores y tipos de tarjetas.
11. PORT-02 presenta Resumen, Plantilla, Torneos y Resultados.
12. Las tarjetas de plantilla enlazan al jugador correcto.
13. PORT-03 muestra rendimiento e historial por torneo.
14. Reconocimientos no confirmados no aparecen.
15. Las páginas públicas no exponen datos personales prohibidos.
16. Un portal suspendido muestra el mensaje neutral definido.
17. Páginas privadas o despublicadas no se presentan como contenido público disponible.

## 12. Pendientes

- Recuperar y comparar la versión original 1.4.
- Crear wireframes de alta fidelidad para PORT-01, PORT-02, PORT-03 y PORT-04.
- Definir estados visuales y componentes reutilizables.
- Definir cantidad de tarjetas por página y comportamiento exacto de paginación.
- Definir placeholders para logotipos/escudos ausentes.
- Confirmar filtros de PORT-01.
- Validar rutas definitivas y navegación.
- Definir tokens de accesibilidad y pruebas responsive.

## 13. Historial

- 1.5 — 2026-11-02: consolidación de experiencia conocida y armonización con decisiones DEC-PORT-036 a 043; pendiente comparar con versión original 1.4.
