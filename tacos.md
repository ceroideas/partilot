en el laravel necesito imlementar una funcionalidad que genere un QR con un codigo que al leerlo desde la app permita vender un TACO completo, si el taco va desde la participacion 00001 hasta la 00050, al leer el QR de la portada del taco deberá permitirme vender todo el taco.

Que necesito hacer?

- Generar un QR por cada taco generado al crear el formato de diseño, eso quiere decir que si tengo 200 participaciones y creo un diseño de tacos de 50 participaciones, esto me estaria generando 4 tacos, a cada taco debo asignarle un QR unico que al leerlo desde la app me permita vender TODO el taco o las participaciones disponibles, es decir, si leo el QR y tengo las XX participaciones disponibles, me aparezca en la app una ventana de confirmación donde me diga las participaciones que voy a vender con dicha lectura.

- Si hay participaciones ya vendidas de ese taco, me debe mostrar las participaciones que puedo vender, por ejemplo, de 50 participaciones del taco, he vendido de la 11 a la 30, al leer el QR me dirá que tengo disponibles de la 1 a la 10 y de la 31 a la 50.

- Como se generará 1 QR por cada taco, quiere decir que la portada se deberá imprimir 1 vez por cada taco, mostrando todas las portadas para cada taco en el PDF, si son 10 tacos, mostrarme en el PDF las 10 portadas cada 1 con su QR

- Habría que separar entonces portada de la imagen trasera en los PDF

En la app Ionic

- Cuando regalo una participación me está diciendo que solo puedo regalarla a usuarios con rol cliente, debo poder regalarla a cualquier tipo de usuario

En la aplicación Ionic debo activar la venta de las participaciones Digitales

- Al entrar en VENDER como vendedor y seleccionar un sorteo, tengo 2 opciones, participaciones físicas y participaciones digitales, en cada tipo de venta deben salir los sets de su tipo, en la tabla sets, se diferencian porque cuando son fisicas el valor de digital_participations es 0 y physical_participations tiene una cantidad (al crear el set solo se puede seleccionar un tipo), cuando son digitales es al reves, digital_participations tiene una cantidad y physical_participations es 0, por lo tanto al pinchar en cada tipo, debe haber un selector de set y mostrar solo los de su tipo en concreto (fisicos o digitales)

- Para vender estas participaciones digitales, se selecciona la cantidad de participaciones a vender y a continuación, al darle vender, deberá salir un modal donde colocar el email del cliente al que se le va a vender, y pueden ocurrir 2 cosas: 1 que el usuario exista, por lo tanto las participaciones quedan vinculadas directamente a ese usuario y aparecerian en su cartera y 2 que el usuario no esté registrado o que el email esté mal colocado, en este caso se pedirá confirmación de que el email no está registrado en la aplicación, por lo tanto se pedirá que revise el correo, en caso de continuar con el correo MAL, se enviará un email a esa dirección indicando que deberá registrarse para poder hacer uso de las participaciones adquiridas, quedando el correo guardado en caché pero vinculadas a ese usuario una vez se cree (verificar si es posible y como), una vez confirmado esto, se mostrará el modal para seleccionar el tipo de pago y ya se podrá vender.

- Al cobrar participaciones premiadas, solo puedo seleccionar varias de la misma entidad, ya que cada entidad se encarga individualmente de los pagos de las participaciones, por lo tanto, si hay participaciones de diferentes entidades, al seleccionar 1, solo podré seleccionar participaciones de la misma entidad de la participacion seleccionada, el resto podrias colocarlas en transparente.

- Lo mismo al donar

---

## Soluciones propuestas

### Laravel – QR por taco y venta de taco completo

**1. QR único por taco al crear el formato de diseño**

- **Dónde:** Generación de `tickets` en el flujo de diseño (p. ej. `DesignController`, servicio que crea `Set.tickets` y/o `design_formats`). Hoy cada ticket tiene `n` (número participación) y `r` (referencia). Para tacos, hace falta un identificador por “taco” (p. ej. por `book_number`).
- **Solución:**
  - Añadir en el JSON de cada ticket (o en una estructura por taco) un campo que identifique el taco, p. ej. `taco_ref` o `book_ref`: un código único por taco (por ejemplo `{set_id}-{book_number}` o un UUID). Ese valor es el que se codificará en el **QR de la portada** del taco.
  - Al generar el diseño por participaciones, para cada `book_number` (taco) generar **un** QR con ese `taco_ref` (no con la referencia de una participación concreta). Guardar en `design_formats` o en el set una estructura tipo `taco_qrs`: `[ { "book_number": 1, "taco_ref": "SET12-B1" }, ... ]` para poder resolver “qué taco es” al escanear.
  - En la **API** que resuelve el QR escaneado: si el contenido es un `taco_ref` (p. ej. prefijo o formato acordado), no buscar por `referencia` en `tickets`, sino por `taco_ref`/`book_number` en esa estructura; devolver `set_id`, `book_number`, rango de participaciones del taco y, llamando a la lógica existente por participaciones asignadas al vendedor, los **rangos disponibles** (véase punto 2).

**2. Mostrar participaciones disponibles al leer el QR del taco (incl. rangos si hay vendidas)**

- **Dónde:** API que hoy resuelve “referencia” → participación/set (p. ej. `ParticipationController::apiCheckByReference` o el endpoint que use la app para “venta por QR”). Añadir un endpoint o variante que acepte `taco_ref` (o `set_id` + `book_number`).
- **Solución:**
  - Endpoint tipo `GET /api/sellers/me/taco-by-qr?taco_ref=SET12-B1` (o `set_id` + `book_number` si la app envía eso tras decodificar el QR).
  - En backend: con `set_id` y `book_number`, obtener `participations_per_book` del design format del set; calcular `desde = (book_number - 1) * participations_per_book + 1`, `hasta = book_number * participations_per_book`. Filtrar participaciones del set en ese rango que estén `asignada` al vendedor y no vendidas.
  - Calcular **rangos continuos disponibles** (ej.: si están libres 1–10 y 31–50, devolver `[{ desde: 1, hasta: 10 }, { desde: 31, hasta: 50 }]`). Devolver también `set`, `sorteo`, `importe_total` por rango y datos necesarios para la ventana de confirmación en la app.
  - La **app**: al escanear el QR del taco, llama a este endpoint, recibe los rangos disponibles y muestra la ventana de confirmación (“Vas a vender participaciones 1–10 y 31–50 del taco X, total N participaciones, importe Y €”). Al confirmar, llamar a la API de venta por rango (o varias ventas por rango) con `desde`/`hasta` y método de pago.

**3. Portadas en PDF: una por taco, cada una con su QR**

- **Dónde:** Generación del PDF de impresión (p. ej. `DesignController`, vistas Blade que generan el PDF de participaciones/portadas).
- **Solución:**
  - Separar claramente “páginas de portada” de “páginas de participaciones” (y de “imagen trasera” si aplica).
  - Para cada taco (cada `book_number`), generar **una página de portada** que incluya el QR correspondiente a ese taco (el `taco_ref` del punto 1). Si hay 10 tacos, el PDF tendrá 10 páginas de portada (cada una con su QR), más las páginas de participaciones y las traseras.
  - Reutilizar la misma plantilla de portada; solo cambiar el dato del QR (y, si se imprime, texto tipo “Taco 1”, “Taco 2”, etc.) según `book_number`.

**4. Separar portada e imagen trasera en el PDF**

- **Dónde:** Misma generación de PDF y configuración del diseño (modelo `DesignFormat`, `output`, o vistas que montan el PDF).
- **Solución:**
  - En el modelo/vista del diseño, tratar “portada” y “imagen trasera” como elementos distintos: por ejemplo `cover_image_path` / `cover_template` para portada y `back_image_path` / `back_template` para la trasera.
  - Al generar el PDF: primero N páginas de portada (una por taco, con su QR); después las hojas de participaciones; al final, si hay imagen trasera, las páginas de “reverso” (una por taco o una global, según regla de negocio). Así se evita mezclar portada y trasera en una sola plantilla.

---

### Ionic – Regalo a cualquier usuario

**5. Permitir regalar a cualquier tipo de usuario (no solo cliente)**

- **Dónde (backend):** `app/Http/Controllers/ParticipationController.php`, método que valida el regalo (API de gift). Actualmente hay un `if (!$destinatario->isClient())` que devuelve error “Solo se puede regalar a usuarios con perfil de usuario”.
- **Solución:** Eliminar o comentar la comprobación que restringe a `isClient()`. Es decir, quitar el bloque:
  ```php
  if (!$destinatario->isClient()) {
      return response()->json([
          'success' => false,
          'message' => 'El correo no corresponde a un usuario. Solo se puede regalar a usuarios con perfil de usuario.',
      ], 422);
  }
  ```
  Así cualquier usuario registrado (vendedor, gestor, cliente, etc.) puede recibir el regalo. La comprobación de “existe el usuario” (`$destinatario`) y “no es uno mismo” se mantienen.

---

### Ionic – Venta de participaciones digitales

**6. En VENDER: opción “Participaciones físicas” y “Participaciones digitales” y selector de set por tipo**

- **Dónde (backend):** API que devuelve loterías/sets al vendedor: `SellerController::apiGetMyLotteries` (y el endpoint que devuelve los sets de una reserva/sorteo). Asegurar que cada set incluya `physical_participations` y `digital_participations`.
- **Dónde (app):** `src/app/venta/venta.page.ts` y `venta.page.html`: flujo “Entidad → Sorteo → …”. Hoy se cargan reservas/sets sin distinguir tipo.
- **Solución:**
  - En la API de loterías/sets del vendedor, incluir en cada set `physical_participations` y `digital_participations` (ya existen en `Set`). Si hace falta un endpoint específico de “sets por sorteo”, filtrar por `reserve_id` y devolver esos campos.
  - En la app, tras elegir entidad y sorteo, mostrar **dos opciones**: “Participaciones físicas” y “Participaciones digitales”. Según la elegida:
    - **Físicas:** filtrar sets con `physical_participations > 0` (y `digital_participations === 0`).
    - **Digitales:** filtrar sets con `digital_participations > 0` (y `physical_participations === 0`).
  - Mostrar un **selector de set** que liste solo los sets del tipo elegido. El resto del flujo (venta manual por rango, etc.) se mantiene para físicas; para digitales se usará el flujo del punto 7.

**7. Venta de participaciones digitales: cantidad, email del cliente, usuario existe o no, método de pago**

- **Backend:** Crear endpoint(s) para: (a) comprobar si un email está registrado; (b) vender participaciones digitales a un usuario existente (vincular a su cartera); (c) “reservar” venta a un email no registrado (guardar en caché/BD con email, set, cantidad, y enviar correo de invitación a registrarse; cuando el usuario se registre con ese email, vincular las participaciones a su cuenta).
  - Para (c): tabla tipo `pending_digital_sales` con `email`, `set_id`, `quantity`, `seller_id`, `payment_method`, etc. Al registrarse un usuario con ese email, un observer o job puede vincular esas participaciones (creándolas o asignando buyer_name/user). Si se prefiere no vincular hasta el registro, se envía el email y al registrarse se muestra “tienes participaciones pendientes de activar”.
- **App:** En el flujo “Participaciones digitales” → elegir set → indicar cantidad → “Vender”:
  1. Abrir modal para introducir **email del cliente**.
  2. Llamar a API “comprobar email”; si existe → confirmar venta y abrir modal de **método de pago**; al confirmar, llamar a venta digital (vinculación a cartera).
  3. Si no existe (o email incorrecto): mostrar mensaje “El correo no está registrado. ¿Continuar? Se enviará un email para que se registre.”; al confirmar, enviar email y guardar venta pendiente (caché/BD); luego modal de **método de pago** y completar venta. La vinculación a usuario se hará al registrarse (backend).

---

### Ionic – Cobrar y donar solo por misma entidad

**8. Cobrar: solo seleccionar participaciones de la misma entidad**

- **Backend:** En `apiGetCobrables` (ParticipationController), incluir en cada item el `entity_id` (o `entity_id` del set de la participación), para que la app sepa a qué entidad pertenece cada participación. Por ejemplo en `formatParticipationForWallet` añadir `'entity_id' => $entity ? $entity->id : null` (o exponerlo solo en el endpoint de cobrables/donables).
- **App:** En `src/app/cobrar-gestionar/cobrar-gestionar.page.ts` y `.html`:
  - Al cargar participaciones cobrables, asegurar que cada una tenga `entity_id` (o `entidad` + identificar entidad de forma unívoca).
  - Al **seleccionar la primera** participación, guardar su `entity_id`. Al pintar la lista, las participaciones con `entity_id` distinto al seleccionado se muestran en **transparente/deshabilitadas** (no seleccionables). Solo se pueden marcar participaciones de la misma entidad.
  - Al cambiar de “Cobrar” a “Donar” (o al abrir la pantalla), resetear la selección para que la primera selección vuelva a fijar la entidad.

**9. Donar: misma lógica que cobrar**

- **Solución:** Aplicar la **misma regla** que en el punto 8: en la pestaña/vista de “Donar”, usar la misma lista de participaciones (cobrables/donables) con `entity_id`. Al seleccionar una, el resto de entidades se muestran en transparente/deshabilitadas; solo se pueden elegir participaciones de la misma entidad para donar. Reutilizar la misma variable “entidad seleccionada” o “entity_id de la primera selección” en el componente.

---

## Resumen de archivos a tocar

| Tema | Laravel | Ionic |
|------|---------|--------|
| QR por taco | DesignController (o servicio que genera tickets/design); estructura `taco_ref` por book; endpoint resolución QR taco | App: pantalla/flujo “escanear QR taco” → llamar endpoint taco → ventana confirmación rangos → venta por rango |
| Portadas PDF | Vistas/PDF: una página portada por taco con su QR; separar portada / trasera | — |
| Regalo a cualquier usuario | `ParticipationController.php`: quitar `if (!$destinatario->isClient())` | — |
| Venta físicas/digitales | `SellerController`: loterías/sets con `physical_participations`/`digital_participations`; endpoints venta digital y email pendiente | `venta.page`: opción físico/digital, filtrar sets, selector set; flujo venta digital con modal email y método de pago |
| Cobrar / Donar por entidad | `ParticipationController::apiGetCobrables`: incluir `entity_id` en cada item | `cobrar-gestionar.page`: deshabilitar/transparente participaciones de otra entidad tras primera selección (cobrar y donar) |
