# Facturación Electrónica (SUNAT · Perú)

Módulo de emisión de comprobantes electrónicos ante SUNAT (UBL 2.1) con firma local vía **Greenter**.

## Puesta en marcha

1. **Instalar la librería Greenter**

   ```bash
   composer require greenter/lite
   ```

   Requiere las extensiones PHP: `ext-soap`, `ext-openssl`, `ext-dom`, `ext-zip`, `ext-mbstring`.

2. **Ejecutar las migraciones**

   ```bash
   php artisan migrate
   ```

   Crea la tabla `facturacion_electronica` y agrega las columnas SUNAT a `comprobantes`
   (`estado_sunat`, `hash_cpe`, `sunat_ticket`, `xml_path`, `cdr_path`, `sunat_observaciones`, `enviado_at`).

3. **Colocar el certificado digital** (`.pem`) en la ruta configurada, por defecto:

   ```
   storage/facturacion/pe/certificate.pem
   ```

   También puedes subirlo desde la pantalla de configuración (campo *Subir certificado*).

4. **Configurar** en el panel: `Facturación → Facturación Electrónica`
   - Marcar **Habilitar facturación electrónica**.
   - Driver de emisión: **Greenter**.
   - Entorno: **Beta** (homologación) o **Producción**.
   - Datos del emisor (RUC, razón social, ubigeo, etc.).
   - Credenciales SUNAT (usuario y clave SOL) y certificado.
   - Pulsar **Probar conexión con SUNAT**.

## Entorno de pruebas (Beta)

- RUC: `20000000001`
- Usuario SOL: `MODDATOS`
- Clave SOL: `MODDATOS`

## Cómo funciona

- Al emitir un comprobante (`FacturacionController@store`), si la facturación electrónica está
  **habilitada** y **emitir automáticamente** activo, se llama a `App\Services\Sunat\SunatService`.
- `SunatService` resuelve el **driver** según la configuración:
  - `none` → `NullDriver` (deja el comprobante pendiente, no envía).
  - `greenter` → `GreenterDriver` (firma el XML UBL 2.1 y lo envía a SUNAT).
- El resultado (aceptado / observado / rechazado / error) se guarda en el comprobante
  (`estado_sunat`, `hash_cpe`, rutas de XML y CDR).

## Arquitectura

```
app/Services/Sunat/
├── SunatService.php            Facade / factory de drivers
├── SunatResult.php             DTO de resultado normalizado
├── Contracts/SunatDriver.php   Interfaz del driver
└── Drivers/
    ├── NullDriver.php          No emite (deja pendiente)
    └── GreenterDriver.php      Firma UBL 2.1 + envío a SUNAT
```

Las claves SOL y contraseñas del certificado se guardan **cifradas** en la base de datos
(cast `encrypted` del modelo `FacturacionElectronica`).

## Validar en homologación (beta)

Prueba el circuito completo (certificado + credenciales + envío + CDR) sin registrar nada:

```bash
php artisan sunat:test boleta
php artisan sunat:test factura
```

Muestra si SUNAT **acepta**, **observa** o **rechaza** el comprobante de prueba, con el hash y las notas.
En producción pide confirmación antes de enviar.

## Reenviar comprobantes

En la lista de **Facturación**, cada comprobante muestra su **estado SUNAT**
(pendiente / aceptado / observado / rechazado / error). Los que estén *pendiente*, *rechazado*
o *error* muestran un botón **Reenviar a SUNAT** (avión de papel) para reintentar el envío.

## Anulación electrónica

Al anular un comprobante ya aceptado por SUNAT, además de la anulación local (estado + caja),
se comunica la baja a SUNAT según el tipo:

- **Factura** → *Comunicación de Baja* (Voided).
- **Boleta** → *Resumen Diario* con estado "anular".

Ambas son **asíncronas**: SUNAT devuelve un **ticket** y la respuesta (CDR) se obtiene consultándolo.
El sistema intenta confirmar de inmediato; si SUNAT sigue procesando, el comprobante queda en estado
`anulando` y aparece un botón **Consultar baja** (icono de refresco) en la lista para reintentar la
consulta. Al confirmarse, pasa a `anulado`.

Al anular se pide un **motivo** (obligatorio para SUNAT), que por defecto es "Anulación de la operación".

> Nota: el resumen diario de boletas requiere que la boleta haya sido informada previamente a SUNAT.
> El correlativo diario de bajas/resúmenes se genera de forma secuencial por día.

## Representación impresa con QR

Al imprimir un comprobante (`facturacion.imprimir`), si la facturación electrónica está habilitada
se muestra la **representación impresa electrónica** con el **código QR** exigido por SUNAT.

La cadena del QR sigue el formato oficial (separado por `|`):

```
RUC | TipoDoc | Serie | Número | IGV | Total | Fecha(Y-m-d) | TipoDocCliente | NúmDocCliente | Hash
```

El QR se genera en el navegador con `qrcodejs` (CDN). También se muestran el hash del CPE y el
estado SUNAT. El hash aparece una vez que el comprobante fue aceptado (se guarda en `hash_cpe`).

## Tablero de estado SUNAT

En **Facturación Electrónica → Estado SUNAT** (`facturacion.electronica.monitor`) hay un tablero con
contadores por estado (aceptados, observados, pendientes, rechazados, anulados) y la cola de
comprobantes por enviar.

- **Reintentar pendientes**: reenvía en lote los comprobantes en estado `pendiente`, `rechazado` o
  `error` (tope de 50 por ejecución para evitar tiempos de espera largos).
- Cada fila también tiene su botón individual de reenvío.

## Notas de crédito y débito

Desde la lista de **Facturación**, cada comprobante vigente tiene un botón para emitir una
**nota de crédito** (07) o **nota de débito** (08) sobre él.

- **Nota de crédito**: devoluciones, anulaciones, descuentos, correcciones (catálogo 09 SUNAT).
  Registra un **egreso** en caja.
- **Nota de débito**: intereses por mora, aumentos de valor, penalidades (catálogo 10 SUNAT).
  Registra un **ingreso** en caja.

La nota referencia al comprobante afectado (`doc_afectado_id`), toma su serie (`FC01`/`BC01` para
crédito, `FD01`/`BD01` para débito según sea factura o boleta) y se emite a SUNAT como cualquier
comprobante (misma vía `See` / drivers, con su QR y estado). El monto puede ser total o parcial
(hasta el total del documento afectado).

> Alcance: las notas se emiten por monto (total o parcial). Las notas por ítem específico
> (devolución de un repuesto concreto) pueden añadirse extendiendo `buildNote`.

## Guía de Remisión Electrónica (GRE)

Módulo **Guías de Remisión** (menú Finanzas). La GRE usa el **API REST** de SUNAT (no SOAP), que
requiere, además de la Clave SOL y el certificado, un **client_id** y **client_secret** (se
configuran en Facturación Electrónica → Credenciales). Estas credenciales se obtienen en SUNAT SOL
(Menú → Empresas → Guías → credenciales del API).

Flujo: `GuiaRemisionController@store` crea la guía y, si la facturación electrónica está habilitada,
la envía con `App\Services\Sunat\GreService` (clase `Greenter\Api`). La GRE es **asíncrona**: SUNAT
devuelve un ticket y el CDR se obtiene consultándolo (botón *Consultar estado* en la lista).

Soporta las dos modalidades (transporte **privado**: placa + conductor; **público**: transportista
con RUC), motivo de traslado (catálogo 20), puntos de partida/llegada, destinatario y los bienes
trasladados. Incluye representación impresa con QR.

> Requisito: la clase `Greenter\Api` debe estar disponible. Si `greenter/lite` no la incluye en tu
> versión, instala el paquete completo `composer require greenter/greenter`. El módulo detecta su
> ausencia y no rompe la app (deja la guía como `pendiente`).

## Notas técnicas

- Los secretos (clave SOL, contraseña del certificado) solo se actualizan si escribes un valor nuevo;
  dejar el campo vacío conserva el valor anterior.
- Los XML firmados se guardan en `storage/app/facturacion/pe/xml/` y los CDR en `.../cdr/`.
- Para notas de crédito/débito y guías de remisión se puede extender `GreenterDriver` reutilizando
  la misma infraestructura de `See` y credenciales.
