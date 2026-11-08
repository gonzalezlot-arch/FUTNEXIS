# FN-OPS-CORE-001 — Procedimiento SQL, retención y condiciones de hosting de CORE

- Versión: 0.1
- Estado: BORRADOR (pendiente de revisión del propietario)
- Fecha: 2026-11-08
- Relacionado con: `FN-ARC-CORE-001`, `FN-MOD-CORE-001`, DEC-CORE-010 a DEC-CORE-016.

## 1. Generar el SQL para phpMyAdmin

El hosting no dispone de SSH, terminal ni Composer. Las migraciones de Laravel siguen siendo la fuente de verdad: el script se genera en un equipo de desarrollo y se revisa antes de importarlo. No se usa `migrate --pretend`, porque no crea la tabla `migrations` ni registra lo aplicado.

**Primer despliegue (base vacía):**

```bash
php artisan migrations:export-sql --output=despliegue.sql
```

**Despliegues posteriores:** consultar en phpMyAdmin la última migración aplicada y el lote más alto, y exportar solo lo nuevo.

```sql
SELECT migration, batch FROM migrations ORDER BY id DESC LIMIT 1;
```

```bash
php artisan migrations:export-sql --after=NOMBRE_DE_LA_ULTIMA --batch=SIGUIENTE_LOTE --output=despliegue.sql
```

El comando necesita una conexión MariaDB local, pero **no ejecuta nada en ella**: compila cada migración y escribe el SQL. El script incluye:

- `SET NAMES utf8mb4` y zona horaria UTC.
- Una comprobación previa del cotejamiento `utf8mb4_uca1400_as_ci`.
- La tabla `migrations`, si no existe.
- Tablas, índices, claves foráneas y cotejamientos.
- Un `INSERT` en `migrations` por cada migración, con su lote.

## 2. Aplicarlo

1. Hacer un respaldo completo de la base con phpMyAdmin y comprobar que se descargó.
2. Revisar el script.
3. Importarlo en `gltechso_futnexis`.
4. Si una sentencia falla, **detenerse**. MariaDB aplica cada cambio de esquema fuera de una transacción, así que el script puede quedar a medias: hay que restaurar el respaldo.
5. Comprobar que `SELECT COUNT(*) FROM migrations` coincide con el número de migraciones publicadas.

**Comprobaciones del servidor:**

```sql
SELECT @@explicit_defaults_for_timestamp, @@time_zone, @@sql_mode, VERSION();
```

La aplicación fija su sesión en UTC, y las columnas `expires_at` y `attempted_at` declaran un valor predeterminado explícito para no depender de `explicit_defaults_for_timestamp`.

## 3. Limpieza programada

Se ejecuta de dos formas complementarias:

1. **Cron de cPanel, si está disponible (recomendado).** Una vez al día:

   ```
   /usr/local/bin/php /home/USUARIO/RUTA_APP/artisan core:prune-auth-data
   ```

   La ruta del binario de PHP debe confirmarse en el hosting. La disponibilidad de cron no está verificada.

2. **Sin cron.** Aproximadamente 1 de cada 100 inicios de sesión ejecuta la limpieza después de responder. Cada ejecución borra como máximo 1000 filas por tipo de dato.

## 4. Retención

**Plazos aprobados por el propietario (DEC-CORE-015).**

| Dato | Plazo | Justificación |
|---|---|---|
| Intentos de acceso, con IP y navegador | 90 días | Permite investigar incidentes recientes sin conservar datos personales indefinidamente |
| Contadores de intentos inactivos | 30 días | Sin actividad ya no aportan protección. Consecuencia: fallos con más de 30 días dejan de contar como consecutivos |
| Bloqueos expirados | 24 horas | Ya no tienen efecto |
| Cuentas sin verificar | 7 días | El enlace vence en 24 horas; deja margen para pedir otro y libera el correo |
| Tokens de verificación y de recuperación | Al vencer | No tienen ningún uso una vez vencidos |
| Registros de la aplicación | 14 días (`LOG_DAILY_DAYS`) | Suficiente para diagnóstico |

Al borrar una cuenta sin verificar, sus intentos se conservan sin el vínculo con el usuario hasta cumplir su plazo.

## 5. Límite secundario por IP

**Valores aprobados por el propietario (DEC-CORE-011):** 100 contraseñas fallidas por IP en una ventana fija de 15 minutos. IPv6 se agrupa por red /64.

| Situación | Comportamiento |
|---|---|
| Por debajo del umbral | Sin efecto |
| Superado, contraseña correcta | Se acepta (D3) |
| Superado, contraseña incorrecta | Mensaje específico; el fallo sigue contando para la cuenta |
| Superado, correo sin cuenta | Se rechaza sin escribir en la base |
| Al alcanzar el umbral | Se escribe un aviso `core.login.ip_threshold_reached` |

**Consecuencias:**

- Un club o una red móvil puede acumular fallos de muchas personas sin perjudicar a quien escribe bien su contraseña.
- **Riesgo residual aceptado por D3:** como una contraseña correcta nunca se rechaza por IP, este límite no detiene por sí solo a un atacante que pruebe pocas contraseñas contra muchas cuentas. El freno real es el bloqueo por cuenta. Ante un ataque sostenido, el aviso del registro permite bloquear la IP desde cPanel.

**Bloqueos provocados por terceros:** cualquiera que conozca un correo puede provocar el bloqueo de esa cuenta durante 15 minutos, y repetirlo. Es inherente a la regla aprobada de bloquear por cuenta y no puede eliminarse sin debilitarla. Lo mitiga D4: el propietario puede desbloquear la cuenta en cualquier momento restableciendo su contraseña. Riesgo residual aceptado para el piloto (DEC-CORE-011).

## 6. Proxies

Por defecto no se confía en ninguna cabecera reenviada. Si el hosting antepone un proxy inverso, indicar sus direcciones en `TRUSTED_PROXIES`, en el `.env` del servidor. El comodín `*` se descarta. Si hay un proxy y no se configura, todos los visitantes aparentarán tener la misma IP.

## 7. Correo y direcciones internacionales

Enviar a una dirección con caracteres no ASCII antes de la `@` (por ejemplo `josé@dominio.com`) exige que el servidor SMTP anuncie la extensión `SMTPUTF8`. **No hay evidencia de que `mail.gltechsolutions.com` la admita.** Si no la admite, el registro se acepta, el correo no llega y queda el aviso `core.mail.delivery_failed` en el registro. La respuesta al usuario es neutra y no afirma el envío.

Debe probarse con el SMTP real antes de ofrecer estas direcciones.

## 8. Comprobaciones antes de producción

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` en HTTPS y **nunca** `MAIL_MAILER=log`.
- Raíz web apuntando a `public/` y redirección a HTTPS.
- `vendor` generado localmente con `composer install --no-dev`; no subir `bootstrap/cache` local.
- Subir `public/build` tras `npm run build`.
- Permisos de escritura en `storage/` y `bootstrap/cache/`.
- Prueba real de SMTP, incluida una dirección con acento.
- Los servidores web registran las URL completas: los enlaces con token pueden aparecer en los registros de acceso. Conviene limitar quién los consulta.
