# FN-UX-ORG-001 — UX/UI: gestión de organizaciones

- **Versión documental:** 1.1
- **Estado:** EN_REVISION
- **Fecha de consolidación:** 2026-11-02
- **Base de trabajo conocida:** versión 1.0, WritingBlock de referencia 94621
- **Nota de integridad:** documento consolidado desde decisiones disponibles; no es transcripción literal del documento de trabajo anterior.

## 1. Objetivo

Definir la experiencia de usuario para crear organizaciones, seleccionar contexto, consultar su información y administrar usuarios y permisos.

## 2. Principios UX

- Interfaz corporativa premium coherente con FUTNEXIS.
- Navegación clara y consistente.
- Diseño responsive para escritorio, tablet, móvil y PWA.
- Menú lateral expandible y contraíble.
- En estado contraído, mostrar ayudas/tooltip en los iconos.
- En móvil, el menú lateral se presenta como navegación desplegable.
- Persistir la preferencia de expansión/contracción.
- Soportar tema claro, oscuro y automático, con preferencia persistente.

## 3. Contexto de organizaciones

### Comportamiento después del login
- Usuario con una organización: entrar al dashboard de esa organización.
- Usuario con varias organizaciones: entrar a vista consolidada.
- Usuario sin organizaciones: mostrar estado inicial acorde con su rol y ofrecer las acciones autorizadas para comenzar.

### Selector y vistas
- Permitir cambiar entre vista consolidada y contexto individual.
- La vista consolidada reúne únicamente organizaciones a las que el usuario tiene acceso.
- La vista individual muestra información de la organización seleccionada.
- Cambiar el contexto debe actualizar indicadores, consultas, acciones, estadísticas y listados.
- El backend debe validar la membresía y permisos; el selector visual no otorga acceso.

## 4. Flujo de creación

Wizard de tres pasos:

1. **Información básica:** nombre y tipo de organización, con los campos obligatorios que defina el modelo.
2. **Contacto y ubicación:** datos complementarios opcionales según definición funcional.
3. **Confirmación:** resumen de datos, confirmación y creación.

Al completar la creación, el usuario creador queda como propietario activo y debe acceder al contexto de la nueva organización.

## 5. Pantallas y áreas funcionales conocidas

| Código | Pantalla/área | Descripción |
|---|---|---|
| ORG-01 | Vista consolidada | Resumen de las organizaciones autorizadas para el usuario. |
| ORG-02 | Dashboard de organización | Panel principal de la organización seleccionada. |
| ORG-03 | Selector de organización | Cambio entre vista consolidada y organización individual. |
| ORG-04 | Crear organización | Wizard de tres pasos. |
| ORG-05 | Detalle de organización | Vista de información y configuración de la organización. |
| ORG-06 | Usuarios y membresías | Gestión de usuarios vinculados, roles y estados. |
| ORG-07 | Invitaciones | Consulta y administración de invitaciones. |
| ORG-08 | Transferencia de propiedad | Flujo de transferencia sujeto a aceptación. |

**Nota:** el inventario de pantallas histórico también contiene referencias ORG-16 y posibles colisiones de numeración. La codificación completa debe revisarse antes de congelar el mapa de navegación.

## 6. Detalle de organización

La vista de detalle utiliza pestañas:
- Resumen.
- Torneos.
- Configuración.

La administración de usuarios/membresías se mantiene en una pantalla independiente, identificada provisionalmente como ORG-06.

## 7. Gestión de usuarios

La pantalla debe permitir consultar miembros, rol y estado de membresía, así como ejecutar las acciones autorizadas. Las opciones visibles deben corresponder a los permisos efectivos del usuario.

Las acciones de alto impacto —como transferir propiedad— deben tener confirmación explícita y explicar sus consecuencias.

## 8. Estados y mensajes

Diseñar estados visuales diferenciados para:
- Organización activa.
- Organización pendiente de configuración.
- Organización suspendida.
- Sin organizaciones disponibles.
- Carga, error y ausencia de resultados.
- Invitación pendiente, aceptada, rechazada, vencida o revocada.
- Membresía pendiente, activa, suspendida o revocada.

No mostrar acciones que el usuario no esté autorizado a ejecutar.

## 9. Responsive y accesibilidad

- Priorizar navegación y lectura en pantallas pequeñas.
- Tablas extensas deben adaptarse mediante desplazamiento o presentación alternativa legible.
- Controles con etiquetas claras y foco visible.
- Contraste suficiente en temas claro y oscuro.
- No depender exclusivamente del color para comunicar estados.

## 10. Criterios de aceptación UX iniciales

1. El usuario con una organización entra directamente a su dashboard.
2. El usuario con varias organizaciones ve la vista consolidada.
3. El usuario puede cambiar de contexto y observa actualización de datos y acciones.
4. El wizard permite completar creación en tres pasos.
5. La creación deja al usuario en la organización recién creada.
6. El detalle presenta Resumen, Torneos y Configuración.
7. La gestión de miembros se accede desde una pantalla independiente.
8. El menú lateral funciona expandido, contraído y en móvil.
9. La preferencia del menú y del tema persiste.
10. Las acciones visibles respetan el rol y los permisos.

## 11. Pendientes

- Validar el mapa definitivo de pantallas y resolver la colisión ORG-06/ORG-16.
- Definir wireframes de alta fidelidad.
- Definir estados visuales, mensajes, placeholders y componentes.
- Confirmar campos obligatorios del wizard.
- Definir comportamiento de tablas y paginación en móvil.
- Revisar accesibilidad y tokens de diseño.

## 12. Historial

- 1.1 — 2026-11-02: consolidación en repositorio de las definiciones conocidas de la versión de trabajo 1.0; requiere comparación con el documento original.
