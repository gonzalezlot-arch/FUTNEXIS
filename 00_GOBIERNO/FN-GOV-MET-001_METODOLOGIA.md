# FN-GOV-MET-001 — Metodología de gobierno documental

- Versión: 1.0
- Estado: BORRADOR
- Fecha: 2026-11-02

## Objetivo
Establecer una gestión documental única, trazable y verificable para FUTNEXIS.

## Fuente maestra
El repositorio GitHub gonzalezlot-arch/FUTNEXIS, rama main, es la fuente maestra documental. Una decisión se considera persistida cuando está registrada en GitHub y su commit es verificable. Las conversaciones no sustituyen el repositorio.

## Estados
- BORRADOR: contenido inicial, no aprobado.
- EN_REVISION: sometido a revisión.
- APROBADO: validado expresamente por el propietario del producto.
- OBSOLETO: sustituido; se conserva para trazabilidad.

## Etiquetas
[CONFIRMADO], [PROPUESTA], [PENDIENTE], [SUPUESTO], [FUERA DE ALCANCE], [NO DEFINIDO].

## Flujo de cambios
1. Identificar decisión o requisito.
2. Identificar documentos afectados.
3. Preparar cambios sin eliminar historial.
4. Revisar coherencia y referencias.
5. Solicitar aprobación cuando corresponda.
6. Registrar en GitHub.
7. Actualizar inventario y bitácora.
8. Informar rutas, versiones y commit.

## Identificadores
Formato: FN-[TIPO]-[ÁREA]-[NÚMERO]. Tipos: GOV, PROD, BRD, UX, ARC, MOD, API, SEC, QA, OPS y DEV. Requisitos: RF, RN, RNF, CA, ERR y PER.

## Versionado
Mayor: cambio estructural o incompatible. Menor: incorporación funcional compatible. Parche: correcciones editoriales o referencias. Nunca presentar una reconstrucción como transcripción literal si no se verificó el original.

## Responsabilidades
Propietario: decisiones y aprobaciones. ChatGPT: arquitectura funcional, documentación y revisión. Claude: implementación conforme a especificaciones aprobadas.

## Seguridad
El repositorio es público. No almacenar credenciales, tokens, claves, datos personales reales ni secretos. Usar datos ficticios.

## Cierre
Una decisión aprobada no se considera documentalmente cerrada hasta quedar registrada y vinculada a sus documentos afectados.
