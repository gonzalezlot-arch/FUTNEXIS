# FN-MOD-ORG-002 — Especificación funcional: portal público

- **Versión documental:** 1.5
- **Estado:** EN_REVISION
- **Fecha de consolidación:** 2026-11-02
- **Base conocida:** versión 1.4, WritingBlock de referencia 61482
- **Nota de integridad:** consolidación de decisiones y requisitos conocidos. No es transcripción literal verificada de la versión 1.4. Las referencias y numeraciones deberán compararse con el original antes de aprobación.

## 1. Objetivo

Definir el comportamiento funcional de los portales públicos de organizaciones, torneos, equipos y jugadores, con publicación controlada, navegación pública, estadísticas deportivas confirmadas y protección de datos personales.

## 2. Alcance

Incluye:
- Portal público de organización.
- Página pública de torneo.
- Página pública de equipo.
- Ficha pública de jugador.
- Publicación y despublicación.
- Contenido deportivo y estadísticas.
- Navegación, SEO, moderación y auditoría.

No incluye la administración interna completa de torneos, el registro de resultados ni la operación de actas, que pertenecen a módulos deportivos relacionados.

## 3. Decisiones generales

- Cada organización puede configurar nombre, logotipo, colores, descripción y datos de contacto institucionales.
- FUTNEXIS mantiene su identidad de plataforma; la identidad de organización no la sustituye.
- El portal público está desactivado por defecto.
- Solo el propietario o usuarios con permiso pueden publicar o despublicar.
- Publicar el portal no publica automáticamente los torneos. Cada torneo tiene publicación independiente.
- Datos personales no son públicos por defecto.
- Las funcionalidades esenciales del portal son iguales para todas las organizaciones durante el piloto.
- Se utiliza un dominio central FUTNEXIS, sin subdominios por organización.
- Identificadores públicos únicos, permanentes y no reutilizables.
- El contexto administrativo se determina por sesión y permisos, nunca por la URL por sí sola.

## 4. Rutas públicas

| Entidad | Ruta |
|---|---|
| Organización | `/o/{identificador}` |
| Equipo | `/e/{identificador}` |
| Jugador | `/jugador/{identificador}` |
| Torneo | `/torneo/{identificador}` |

Rutas generales conocidas: `/` para inicio, `/login` para autenticación, `/app` para la aplicación autenticada y `/app/organizacion/{identificador}` para el contexto administrativo de organización.

## 5. Requisitos funcionales consolidados

### 5.1 Portal de organización (PORT-01)

| ID | Requisito |
|---|---|
| RF-PORT-001 | Permitir que una organización configure la información institucional pública autorizada. |
| RF-PORT-002 | Mantener el portal desactivado por defecto. |
| RF-PORT-003 | Permitir publicar/despublicar únicamente a usuarios autorizados. |
| RF-PORT-004 | No publicar automáticamente torneos al publicar el portal de organización. |
| RF-PORT-005 | Mostrar únicamente torneos que estén publicados individualmente. |
| RF-PORT-006 | Presentar torneos mediante tarjetas en cuadrícula con paginación. |
| RF-PORT-007 | Mostrar en cada tarjeta nombre, imagen institucional/deportiva autorizada, modalidad, categoría, estado, temporada/año y acceso al torneo. |
| RF-PORT-008 | Clasificar visualmente torneos publicados como próximos, en curso o finalizados según DEC-PORT-036. |
| RF-PORT-009 | Ordenar los grupos en curso, próximos y finalizados; dentro de cada grupo, del más reciente al más antiguo. |
| RF-PORT-010 | No mostrar próximos partidos ni resultados en la portada de organización. |
| RF-PORT-011 | Permitir navegación desde la tarjeta al detalle público del torneo. |
| RF-PORT-012 | Mantener filtros por estado y modalidad como [PROPUESTA]/[PENDIENTE] hasta aprobación expresa. |

### 5.2 Portal de torneo (PORT-04)

| ID | Requisito |
|---|---|
| RF-PORT-013 | Mostrar información general del torneo publicado. |
| RF-PORT-014 | Mostrar equipos participantes autorizados. |
| RF-PORT-015 | Mostrar calendario de partidos. |
| RF-PORT-016 | Mostrar resultados oficiales confirmados. |
| RF-PORT-017 | Mostrar clasificación oficial calculada según reglas del torneo. |
| RF-PORT-018 | Mostrar estadísticas individuales oficiales confirmadas. |
| RF-PORT-019 | Organizar el portal en seis secciones: Información general, Equipos, Calendario, Resultados, Clasificación y Estadísticas individuales. |
| RF-PORT-020 | Presentar las secciones mediante pestañas horizontales, con desplazamiento horizontal en móvil. |
| RF-PORT-021 | Ofrecer calendario por jornadas y lista cronológica. |
| RF-PORT-022 | Permitir filtros de calendario por equipo y estado del partido. |
| RF-PORT-023 | Mostrar en cada partido los equipos, escudos autorizados, fecha/hora, jornada, escenario, estado y marcador confirmado cuando exista. |
| RF-PORT-024 | Mostrar clasificación con posición, equipo, PJ, PG, PE, PP, GF, GC, DG y PTS. |
| RF-PORT-025 | Actualizar estadísticas y clasificación solo con datos deportivos oficialmente confirmados. |
| RF-PORT-026 | Mostrar estadísticas individuales de goles, partidos jugados y tarjetas amarillas, rojas y azules. |
| RF-PORT-027 | Mostrar ranking general de goleadores aplicando DEC-PORT-038. |
| RF-PORT-028 | Mostrar rankings disciplinarios independientes por tarjetas amarillas, rojas y azules, en orden descendente. |
| RF-PORT-029 | No publicar estadísticas derivadas de partidos pendientes de confirmación. |
| RF-PORT-030 | Excluir de cálculos oficiales los resultados y estadísticas de partidos anulados. |

### 5.3 Página pública de equipo (PORT-02)

| ID | Requisito |
|---|---|
| RF-PORT-031 | Mostrar nombre, escudo autorizado, categoría y descripción pública del equipo. |
| RF-PORT-032 | Mostrar torneos publicados en los que participa o participó el equipo, según permisos y disponibilidad. |
| RF-PORT-033 | Mostrar plantilla mediante tarjetas con número, nombre deportivo y posición. |
| RF-PORT-034 | Permitir navegar desde cada jugador a su ficha pública. |
| RF-PORT-035 | Mostrar resultados y clasificación del equipo por torneo. |
| RF-PORT-036 | Mostrar estadísticas colectivas por torneo: PJ, PG, PE, PP, GF, GC, DG, PTS y tarjetas amarillas, rojas y azules. |
| RF-PORT-037 | Presentar secciones Resumen, Plantilla, Torneos y Resultados. |
| RF-PORT-038 | Mostrar detalle disciplinario con nombre deportivo, número y cantidad de tarjetas por jugador. |
| RF-PORT-039 | Mostrar ranking de goleadores del equipo por cantidad de goles, con nombre deportivo, número y goles. |
| RF-PORT-040 | No aplicar automáticamente al ranking del equipo los criterios de desempate del ranking general. |

### 5.4 Ficha pública de jugador (PORT-03)

| ID | Requisito |
|---|---|
| RF-PORT-041 | Asignar a cada jugador un identificador público estable e independiente del nombre deportivo. |
| RF-PORT-042 | Mostrar únicamente nombre deportivo y datos deportivos autorizados. |
| RF-PORT-043 | Mostrar número de camiseta, posición y equipo/torneo actual cuando estén autorizados. |
| RF-PORT-044 | Mostrar historial de participación por torneo y equipo. |
| RF-PORT-045 | Mostrar estadísticas individuales desglosadas por torneo. |
| RF-PORT-046 | Mostrar reconocimientos deportivos solo cuando estén confirmados oficialmente. |
| RF-PORT-047 | Mantener el historial deportivo aunque el jugador cambie de equipo entre torneos. |

## 6. Reglas de negocio

### 6.1 Publicación y estados
- Estado de publicación y estado deportivo son independientes.
- Próximos: publicado y la competición no ha comenzado.
- En curso: la competición comenzó y no terminó oficialmente.
- Finalizados: terminó oficialmente.
- Las estadísticas se publican solo si el torneo está publicado y los datos correspondientes están confirmados oficialmente.
- Despublicar no elimina datos ni configuración; impide su exposición pública y requiere invalidar caché.
- La publicación y despublicación deben auditarse.

### 6.2 Estadísticas y ranking de goleadores
Ranking general, en orden:
1. Mayor cantidad de goles.
2. Mayor avance deportivo del equipo con el que participa el jugador en el torneo.
3. Menor cantidad de partidos oficiales jugados para alcanzar esa cantidad de goles.
4. Si persiste el empate, comparten posición.

En formatos eliminatorios se consideran campeón, subcampeón, semifinales, cuartos, octavos y fases anteriores/grupos. En formatos de liga sin eliminatorias se considera la posición final del equipo. Los formatos mixtos/especiales requieren definición algorítmica adicional.

### 6.3 Estados de partidos y estadísticas
- Aplazado: no genera resultados ni estadísticas hasta disputarse y confirmarse.
- Suspendido: no genera estadísticas definitivas hasta resolución oficial.
- Anulado: resultado y estadísticas asociadas se excluyen de los registros oficiales; se conserva trazabilidad.
- Reprogramado: conserva identidad; no genera duplicado.
- Cambios de estado deben registrar usuario, fecha, motivo, estado anterior y nuevo.

### 6.4 Jugadores y nombres deportivos
- Nombre deportivo único dentro de cada torneo.
- Comparación sin distinguir mayúsculas/minúsculas y sin espacios sobrantes al inicio/final.
- Puede repetirse en torneos distintos.
- Estadísticas ligadas al ID interno del jugador y a su participación en el torneo, nunca al texto del nombre.
- Cambios de equipo entre torneos permitidos sin modificar historial.
- No se permite cambiar de equipo durante el mismo torneo. Excepciones de corrección por error administrativo requieren autorización, justificación y auditoría.

### 6.5 Privacidad y contenido
No publicar nombres reales, fotografías personales, documentos, teléfonos, correos, direcciones ni datos sensibles de jugadores, delegados, árbitros o participantes. Se permiten logotipos institucionales, escudos de equipo y banners institucionales sujetos a validación. Rechazar contenido ofensivo, ilegal, engañoso o que infrinja derechos de terceros. FUTNEXIS puede retirar contenido incumplidor. La versión vigente permanece visible mientras se revisa una nueva versión.

### 6.6 Organización suspendida
Cuando una organización sea suspendida por superadmin:
- Portal y rutas públicas asociadas dejan de mostrar contenido.
- Los datos se conservan.
- Los usuarios administrativos no pueden gestionar contenido.
- El superadmin puede consultar y gestionar la reactivación.
- Mensaje público: “Este portal no se encuentra disponible actualmente”.
- Al reactivar, se restablece la disponibilidad de los contenidos que sigan publicados.

### 6.7 SEO
Buscadores pueden indexar portales, torneos, equipos y jugadores publicados. Excluir páginas privadas, borradores y contenido no publicado. Utilizar títulos, descripciones y URLs amigables. Al despublicar o suspender, solicitar retirada de índices mediante mecanismos compatibles. Metadatos nunca deben revelar datos privados.

### 6.8 Auditoría
Registrar publicaciones, despublicaciones, modificaciones y cambios de estado con usuario, fecha, acción, entidad afectada y motivo cuando corresponda. Conservar historial aunque el contenido deje de ser público. Restringir acceso a usuarios autorizados. Plazos de conservación sujetos a política de privacidad pendiente.

## 7. Permisos

- Propietario o usuario autorizado: configurar y publicar portal de organización.
- Usuarios con permisos de torneo: administrar publicación individual del torneo según matriz de permisos.
- Superadmin FUTNEXIS: suspensión/reactivación de organización y moderación global conforme a políticas.
- Visitante: consulta solo de recursos publicados y disponibles.
- Toda autorización se valida en backend; la URL no concede permisos.

## 8. Criterios de aceptación iniciales

| ID | Criterio |
|---|---|
| CA-PORT-001 | Un portal desactivado no expone contenido público. |
| CA-PORT-002 | Publicar portal no publica automáticamente los torneos. |
| CA-PORT-003 | La portada solo muestra torneos publicados. |
| CA-PORT-004 | La portada no muestra próximos partidos ni resultados. |
| CA-PORT-005 | Los torneos se muestran en tarjetas y con paginación. |
| CA-PORT-006 | El orden de grupos corresponde a DEC-PORT-036 y DEC-PORT-009. |
| CA-PORT-007 | Los estados deportivos se calculan independientemente del estado de publicación. |
| CA-PORT-008 | El portal de torneo presenta sus seis secciones. |
| CA-PORT-009 | El calendario permite vista por jornada y lista cronológica. |
| CA-PORT-010 | Los partidos sin confirmación no alteran estadísticas públicas. |
| CA-PORT-011 | Clasificación muestra PJ, PG, PE, PP, GF, GC, DG y PTS. |
| CA-PORT-012 | Los rankings disciplinarios se presentan por tipo de tarjeta. |
| CA-PORT-013 | El ranking general aplica los criterios aprobados de DEC-PORT-038. |
| CA-PORT-014 | Los partidos anulados no intervienen en cálculos oficiales. |
| CA-PORT-015 | La ficha del equipo muestra plantilla, resultados y estadísticas autorizadas. |
| CA-PORT-016 | El ranking de goleadores del equipo no aplica desempates del ranking general. |
| CA-PORT-017 | El nombre deportivo se valida como único dentro del torneo sin distinguir mayúsculas/minúsculas ni espacios externos. |
| CA-PORT-018 | La ficha del jugador conserva historial por torneo y equipo. |
| CA-PORT-019 | Ningún dato personal prohibido aparece en páginas, metadatos o resultados públicos. |
| CA-PORT-020 | La suspensión de organización oculta las rutas públicas asociadas. |
| CA-PORT-021 | Despublicar invalida la exposición pública y conserva los datos. |
| CA-PORT-022 | Las operaciones de publicación y cambios de estado quedan auditadas. |
| CA-PORT-023 | Solo páginas publicadas y disponibles son elegibles para indexación. |
| CA-PORT-024 | Un visitante no puede acceder a contenido privado cambiando identificadores de URL. |

## 9. Dependencias

- Módulo de organizaciones y membresías.
- Módulo de torneos y configuración deportiva.
- Equipos, jugadores e inscripciones.
- Calendario, partidos, actas y resultados.
- Clasificación, estadísticas y disciplina.
- Permisos, auditoría, almacenamiento multimedia y caché.
- Política de privacidad y protección de datos.

## 10. Pendientes

1. Recuperar y comparar el documento original v1.4, incluida la numeración completa RF/CA.
2. Recuperar el detalle literal de DEC-PORT-001 a 008.
3. Aprobar o descartar filtros por estado/modalidad en PORT-01.
4. Definir algoritmo de avance deportivo para formatos mixtos y especiales.
5. Definir autorización de publicación de estadísticas por torneo/categoría.
6. Definir catálogo completo de estados de publicación y transiciones.
7. Definir correcciones estadísticas después del cierre de partido/torneo.
8. Definir plazos de conservación de auditoría.
9. Definir política de privacidad, moderación, reportes y apelaciones.
10. Definir disponibilidad pública ante restricciones distintas de suspensión.
11. Definir tiempos y mecanismos SEO de retirada de índices.
12. Revisar CA-PORT-009 y referencias cruzadas con RN-PORT-015 en el documento histórico.

## 11. Historial

- 1.5 — 2026-11-02: consolidación de requisitos y reglas conocidas; integra DEC-PORT-036 a 043 y marca explícitamente pendientes. Requiere comparación con versión original 1.4.
