# Nuevas tareas 16-02 – Con posibles soluciones

---

## Gestores

- ✅ **Tarea COMPLETADA:** Cuando vas a dar de alta el gestor, no puedo asignarle permisos de ningún tipo y no puedo editar el gestor de ninguna manera (creo que está ya con todos los permisos cuando se está creando pero si se intenta quitar algún permiso tiene que salir aviso de que no se puede ya que es el gestor principal). Veo que a los siguientes ya si se puede. Además el principal (que solo puede ser uno), se tiene que poder quitar como principal pero solo si se cambia por otro (no puede quedar el gestor principal desierto). Cuando se está quitando de principal te tiene que preguntar qué otro gestor va a ser el principal si no el proceso se interrumpe y no deja cambiarlo.

  **Posibles soluciones (referencia):**
  - En el alta del gestor: si es el primer gestor (principal), mostrar en la vista que tiene todos los permisos y no mostrar checkboxes de permisos (o mostrarlos deshabilitados con el aviso). Revisar `EntityController` (crear manager) y vista `entities/add_manager.blade.php` para no ocultar la opción de "editar" pero sí mostrar mensaje tipo "Gestor principal: tiene todos los permisos".
  - Al editar permisos del principal: en `EntityController::updateManagerPermissions` ya se fuerza todos los permisos a true y se muestra info; asegurar que en la vista `edit_manager_permissions.blade.php` el gestor principal se muestre en solo lectura con el texto que ya existe: "Gestor principal. Tiene todos los permisos y no se pueden restringir…"
  - Quitar como principal solo si hay otro: en la acción `set-primary-manager` (ruta `entities.set-primary-manager`) exigir siempre el ID del nuevo gestor principal en el request; si no se envía, devolver error 422 y mensaje "Debe seleccionar el nuevo gestor principal". En la vista `entities/show.blade.php`, en lugar de un solo botón "Principal" en el actual principal, mostrar un desplegable o modal para elegir "Quitar como principal y asignar a…" con lista de los demás gestores; al elegir uno se envía ese ID y se hace el cambio en una transacción (antiguo principal → is_primary false, nuevo → true).

  **Soluciones implementadas:**
  - ✅ **Alta del gestor principal:** Mejorado el mensaje en `add_manager.blade.php` con un alert informativo que explica claramente que el gestor principal tiene todos los permisos y que para cambiarlos debe asignar otro gestor como principal desde la ficha de la entidad.
  - ✅ **Edición de permisos del principal:** Mejorada la vista `edit_manager_permissions.blade.php` con un alert de advertencia más visible que incluye un botón para ir a la ficha de la entidad. El controlador ahora redirige con mensaje de error si se intenta cambiar permisos del principal.
  - ✅ **Quitar como principal:** Mejorada la validación en `EntityController::set_primary_manager()` para verificar que existe otro gestor disponible antes de permitir el cambio. El select en `show.blade.php` ahora requiere selección obligatoria y el botón está deshabilitado hasta seleccionar un gestor. Agregado JavaScript para validar antes de enviar y mostrar confirmación clara. El backend valida que no se pueda quitar el principal sin asignar otro y que el nuevo gestor sea diferente al actual.

---

## Diseño e impresión

- ✅ **Tarea 2 COMPLETADA:** En la sección diseño e impresión, si das al botón editar en un trabajo, te entra sin problemas a editar la participación pero si le das a la fila te muestra que está roto 404.

  **Posibles soluciones (referencia):**
  - La fila usa `data-href="{{ url('design/view', $design->id) }}"` pero no existe ruta `design/view`. Cambiar el clic de la fila para que vaya a la misma URL que el botón editar: en `resources/views/design/index.blade.php` poner `data-href="{{ route('design.editFormat', $design->id) }}"` en el `<tr class="row-clickable">`. Comprobar que el JS que hace el click en la fila (por ejemplo `row-clickable` o tabla) use ese `data-href` y que las celdas de acciones tengan la clase `no-click` para no disparar doble navegación.

  **Soluciones implementadas:**
  - ✅ **Corregida ruta de la fila:** Cambiado `data-href` en el `<tr class="row-clickable">` de `url('design/view', $design->id)` a `route('design.editFormat', $design->id)` para que use la misma ruta que el botón editar.
  - ✅ **Corregido enlace en Order ID:** También actualizado el enlace en la primera columna (Order ID) para que use `route('design.editFormat', $design->id)` en lugar de `url('design/view', $design->id)`.
  - ✅ **Verificado JavaScript:** El JavaScript ya maneja correctamente el clic en la fila usando `data-href` y evita la navegación cuando se hace clic en la columna de acciones (que tiene la clase `no-click`).

- **Tarea 3:** Si subes una imagen como fondo no se aplica (no se sube).

  **Posibles soluciones:**
  - Revisar en `DesignController` (o el que maneje guardar formato) el campo que recibe el fondo (p. ej. `background_image` o dentro del JSON `format`). Asegurar que se hace `Storage::put()` o move al `public/uploads` y se guarda la ruta relativa en el modelo/vista. En la vista del editor (format.blade.php / edit_format.blade.php) que pinta el fondo, usar `asset()` o `Storage::url()` con esa ruta guardada. Revisar también que el formulario envíe el archivo como `multipart/form-data` y que no haya límites de tamaño en PHP que corten la subida.

- ✅ **Tarea 4 COMPLETADA:** El zoom debería ser mayor de 150% (por ejemplo hasta 400). Además el cuadro que lo contiene puede ser más grande para que no se quede tan pequeño al editarlo (que el scroll inferior esté más abajo).

  **Posibles soluciones (referencia):**
  - En las vistas del editor (`design/format.blade.php` y la compilada en `storage/framework/views/`) buscar la variable o constante que limita el zoom (p. ej. máximo 1.5). Subir el máximo a 4 (400%) y asegurar que el paso de zoom permita llegar hasta ahí. Para el cuadro: aumentar la altura (o min-height) del contenedor `.design-zoom-scroll` (o el que tenga `overflow: auto`) para que el área de edición sea más grande y el scroll inferior quede más abajo; se puede usar una altura en vh o un min-height en px. Nota: hacer que llegue al tope inferior de la caja que lo contiene.

  **Soluciones implementadas:**
  - ✅ **Aumentado zoom máximo a 400%:** Cambiado `designZoomSteps` de `[0.5, 0.75, 1, 1.25, 1.5]` a `[0.5, 0.75, 1, 1.25, 1.5, 2, 2.5, 3, 3.5, 4]` en ambos archivos (`format.blade.php` y `edit_format.blade.php`). Ahora el zoom puede llegar hasta 400% con pasos graduales.
  - ✅ **Aumentado tamaño del contenedor:** Modificado el CSS de `.design-zoom-scroll` aumentando `max-height` de `calc(100vh - 300px)` a `calc(100vh - 200px)` y agregado `min-height: 600px` para que el área de edición sea más grande y el scroll inferior quede más abajo, facilitando la edición con zoom alto.

- ✅ **Tarea 5 COMPLETADA:** Ahora tampoco carga imágenes dándole a agregar imágenes.

  **Posibles soluciones:**
  - Revisar en la misma vista/JS del diseño el botón “Agregar imágenes”: que la ruta que llama (AJAX o form) exista y devuelva OK; que los archivos se suban a una ruta accesible y que la respuesta devuelva la URL de la imagen para insertarla en el canvas/HTML. Comprobar consola del navegador (errores 404, CORS, o de subida) y que el backend reciba el file y lo guarde (mismo criterio que el fondo: `Storage` o `uploads` y guardar ruta).

- ✅ **Tarea 6 COMPLETADA:** No se puede eliminar el trabajo del set seleccionado. En principio, se debería poder avisando que se va a eliminar (habría que ver si se podría). Pero ahora seguro que no deja eliminar el trabajo.

  **Posibles soluciones (referencia):**
  - Crear una ruta DELETE (o POST) para eliminar un diseño/trabajo, por ejemplo `design.format.destroy` que reciba el ID del `DesignFormat`. En el controlador: comprobar permisos, opcionalmente comprobar si hay participaciones vendidas (si no se debe permitir borrar con ventas, devolver error); si se permite, eliminar el `DesignFormat` (el modelo ya tiene lógica en `deleted` para participaciones). En la vista `design/index.blade.php`, el botón de la papelera debe llamar a esa ruta con confirm: “¿Eliminar este trabajo de diseño? Se eliminarán las participaciones asociadas.” y al confirmar redirigir al listado.

- **Tarea 7:** Cuando estamos diseñando la portada no sale en ningún sitio el QR para poder asignar tacos directamente. Además cuando se exporta, no está en ningún sitio ni la imagen de portada ni la imagen trasera, solo viene el PDF con las participaciones.

 **Parte 1 (✅ Completada):** Generar PDFs de portada y trasera al exportar
 **Parte 2 (Pendiente):** Mostrar QR en el editor de portada para asignar tacos directamente

 **Posibles soluciones:**
  - Portada/trasera: en el editor de portada y trasera (pasos donde se diseña cover/back) incluir un bloque o placeholder “QR / referencia de taco” que se rellene con el mismo dato que en participaciones (código de referencia o QR por taco), para que al asignar tacos se vea en pantalla. Exportación: en `DesignController` los métodos `exportCoverPdf` y `exportBackPdf` (o async) deben generar PDFs de portada y trasera; asegurar que la opción “Exportar” o “Configurar salida” incluya en el flujo la generación y descarga de esos PDFs además del de participaciones, y que en la pantalla de resumen/descarga se listen y enlacen los tres (participaciones, portada, trasera).

- ✅ **Tarea 8 COMPLETADA:** Si haces una participación de 6xA3 y luego editas el set y le dices que sea 8×3, el sistema mantiene los datos como si fuese 6xA3 pero los que se quedan fuera de la nueva participación al ser más pequeña. El sistema al cambiar el tamaño debería señalarlos en rojo los que estén fuera de las guías, como que esa parte no se generará en el PDF.

  **Posibles soluciones (referencia):**
  - Al guardar el nuevo tamaño (filas/columnas u hoja) en el formato, recalcular qué “celdas” o posiciones quedan fuera del nuevo grid. En el JSON del diseño (elementos posicionados) marcar con un flag o clase (p. ej. `outOfBounds: true`) los elementos cuya posición quede fuera del nuevo tamaño. En la vista del editor, al pintar los elementos, aplicar una clase CSS (p. ej. `.out-of-bounds`) que los muestre en rojo o con borde rojo y un tooltip “Fuera de área de impresión”. En la generación del PDF, no dibujar esos elementos o dibujarlos solo en modo “vista previa de advertencia” según criterio.

  **Solución implementada (Tarea 8):**
  - ✅ **Reescalado por porcentaje:** Al cambiar el tamaño del formato, los elementos del paso 2 (participación) se reescalan por porcentaje para mantenerse dentro del nuevo grid. Implementado en `edit_format.blade.php` y `format.blade.php` (lastTicketDimensions, pendingRescale, repositionParticipationElementsByScale, updateTicketInfo y al entrar en paso 2).

- ✅ **Tarea 9 COMPLETADA:** Si editas un trabajo, en la primera pantalla la imagen de las participaciones sale siempre por defecto A3 apaisado (3x2), aunque en el texto sea otra cosa.

  **Posibles soluciones (referencia):**
  - Al cargar la pantalla de edición de formato (`editFormat`), pasar el formato guardado (page, rows, cols, orientation) y en la vista no inicializar por defecto a A3 3x2; usar los valores de `$format` para el selector y la miniatura. Ajustar el JS que actualiza la imagen de las participaciones para que tome esos valores al cargar y no solo al cambiar.

  **Solución implementada:**
  - ✅ En `edit_format.blade.php` los selectores e inputs ya toman los valores de `$format` desde Blade. Añadida la función `initPreviewFromFormat()` que construye la miniatura de participaciones (`.preview-design`) según el valor actual de formato/página/filas/columnas/orientación sin resetear los campos. Se llama a `initPreviewFromFormat()` en el `$(document).ready` junto a `updateTicketInfo()`, de modo que al cargar la pantalla de edición la imagen coincide con el formato guardado.

- **Tarea 10:** En la pantalla dos “Diseñar participación”, cuando en la primera has cambiado el tamaño de las participaciones y le das a siguiente, no guarda el diseño; simplemente pasa a la siguiente pantalla, por tanto no guarda lo que se ha hecho.

  **Posibles soluciones:**
  - Al pulsar “Siguiente” en el paso 1 (configurar formato), antes de cambiar de paso hacer una petición para guardar el formato (márgenes, tamaño, etc.) igual que hace el botón “Guardar” si existe. Reutilizar la misma ruta/acción que usa ese botón (p. ej. `design.updateFormat` o store) con los datos del formulario del paso 1; solo cuando la respuesta sea OK, avanzar al paso 2. Así el tamaño y márgenes quedan guardados antes de diseñar.

- ✅ **Tarea 11 COMPLETADA:** El botón de guardar en la primera pantalla de configurar formato solo debe salir cuando despliegas “Configurar márgenes”, ya que solo sirve en ese caso. Debería estar al lado o debajo de configurar márgenes (el desplegable).

  **Posibles soluciones:**
  - En la vista del paso 1 (configurar formato), mover el botón “Guardar” dentro del bloque desplegable de “Configurar márgenes” (o justo debajo del desplegable). Mostrarlo solo cuando el desplegable esté abierto (por ejemplo con un `v-if`/`@if` o mostrando/ocultando con JS cuando se expande el acordeón).

- ✅ **Tarea 12 COMPLETADA:** Cuando llegas a configurar salida, sale el botón de siguiente, dice que lo guarda pero no deja hacer nada más (no se sale, no muestra el PDF generado/s). Debería salir una pantalla con el resumen de lo que se va a generar y cuando se genere abriría el PDF en pantalla y el sistema debería ir a la pantalla inicial donde están los sets.

  **Posibles soluciones:**
  - Al pulsar “Siguiente” en configurar salida: (1) Guardar la configuración de salida. (2) Redirigir a una nueva vista “Resumen / Generando” que muestre: “Se generarán: PDF participaciones, PDF portada, PDF trasera” (según corresponda). (3) En esa pantalla lanzar la generación (ya sea en cola o síncrona) y mostrar enlaces de descarga cuando estén listos, o abrir en nueva pestaña el PDF de participaciones (y opcionalmente portada/trasera). (4) Incluir un botón “Volver a sets / Diseño e impresión” que redirija a la lista de diseños/sets (p. ej. `route('design.index')` o la URL inicial de diseño).

- **Tarea 13:** En los sets de participaciones digitales, entiendo que hay que generar también la imagen de la participación para que aparezcan y se puedan asignar. Si las digitales no tienen imagen no se podría asignar al igual que las físicas (las físicas si no hay impresión no hay posibilidad de asignar ya que no se ha generado el PDF con QR y número de referencia).

  **Posibles soluciones:**
  - Para sets digitales: al dar por cerrado el diseño (o al “generar” el set digital), generar una imagen de participación (por ejemplo un PNG o un frame del PDF por participación) y guardarla asociada al set o a cada participación digital, de forma que en la app/panel al listar participaciones digitales se muestre esa imagen y se pueda asignar (mismo flujo conceptual que “taco” en físicas). Revisar modelo Participation/Set por tipo digital y añadir campo o relación para imagen de participación si no existe; reutilizar la misma lógica de diseño (render del diseño con datos de la participación) para generar esa imagen.

---

## XML y exportación

- **Tarea 14:** Si le das al icono de descargar XML en los sets de participaciones, descarga un XML que no puede ser en las participaciones físicas, ya que salen REF000001 y en la participación diseñada viene un código de 21 dígitos.

  **Posibles soluciones:**
  - En `SetController::downloadXml`, en lugar de generar siempre `<r>REF` + número de orden de 1 a N, obtener la referencia real de cada participación. Para sets físicos con diseño: si existen participaciones con `design_format_id` y tienen un código de referencia (campo de 21 dígitos en BD o generado desde el diseño), usar ese código en el nodo `<r>` del XML. Es decir: iterar las participaciones del set (o el rango que corresponda) y para cada una escribir su referencia real; si no hay participación creada aún, se puede seguir usando REF000001 como fallback solo para ese caso, pero si el set tiene diseño y participaciones generadas, usar la referencia del diseño (21 dígitos).

- **Tarea 15:** Si le das al icono de descargar XML en los sets de participaciones digitales, hace lo mismo que con las físicas; por tanto todas las participaciones tendrían la misma numeración, porque con cada trabajo pondría REF000001, REF000002.

  **Posibles soluciones:**
  - Diferenciar en `downloadXml` si el set es físico o digital. Para sets digitales: las referencias deben ser únicas por participación (por ejemplo un ID único o código de 21 dígitos por participación digital). Si las participaciones digitales se crean con un identificador único por participación, usar ese en el XML; si no, generar uno por participación (por ejemplo `DIG` + set_id + número de participación o un UUID corto) para que cada línea del XML tenga referencia distinta y no se repita REF000001 por set.

- **Tarea 16:** En el XML el campo `<urlweb>` está vacío.

  **Posibles soluciones:**
  - En `SetController::downloadXml` se usa `$administration->web`. Comprobar que el modelo Administration tenga el campo `web` y que esté rellenado en BD. Si la administración no tiene web, usar un valor por defecto desde config (p. ej. `config('app.url')`) o desde la entidad/administración alternativa. Asegurar que al guardar/editar administración exista el campo “web” en el formulario y se persista.

- **Tarea 17:** En el XML, si son más de un número, debe venir el número y luego el importe que juega cada número. (Ejemplo: participación con 4 números y precio 20€ → 23145 5€, 43567 5€, etc.; si son 3 números y 20€ → 6,66€ por número.)

  **Posibles soluciones:**
  - Cambiar la estructura del XML en la sección `<numeros>` (o donde se listen los números): para cada número incluir el importe que le corresponde. Calcular: `importe_por_numero = round(precio_participacion / count(numeros), 2)`. Generar algo como `<numero valor="23145" importe="5.00"/>` o `<numero><valor>23145</valor><importe>5.00</importe></numero>` por cada número, usando el mismo redondeo a 2 decimales cuando no sea exacto (ej. 6,66€).

- **Tarea 18:** El sistema debe siempre redondear de manera que nunca se jueguen más participaciones ni más cantidad de lotería (en €) que la reservada. Ejemplo: 12.000€ de reserva, participaciones de 4,20€ + 0,8€ donativo (5€ total). 12.000/4,20 = 2857,14 → se podrían hacer 2857 participaciones, NO 2858.

  **Posibles soluciones:**
  - En todos los cálculos donde se derive “número máximo de participaciones” a partir de la reserva (importe reservado / importe por participación de lotería), usar `floor()`: `max_participaciones = floor(reserva_total_loteria / importe_loteria_por_participacion)`. Revisar `SetController` (creación/actualización de set), reservas y cualquier sitio donde se calcule total_participations o se reparta importe; aplicar siempre redondeo a la baja para participaciones y para importe total repartido, de modo que la suma no supere nunca la reserva.

---

## Datos de reserva y usuarios

- **Tarea 19:** Datos de reserva, botón editar en el listado, atrás y se queda en la misma pantalla y hay que volverle a dar para salir.

  **Posibles soluciones:**
  - El “atrás” probablemente es `history.back()` o un enlace que no cambia la URL. Cambiar el botón “Atrás” en la vista de edición de reserva para que sea un enlace fijo a la lista de reservas (p. ej. `route('reserves.index')` o la que corresponda) en lugar de depender del historial. Así siempre se sale al listado con un solo clic.

- **Tarea 20:** Imagen de un usuario, la sube pero no la muestra en su ficha. Si le das a editar sí la muestra.

  **Posibles soluciones:**
  - En la vista de ficha del usuario (show) no se está mostrando el avatar. Comprobar que el controlador que carga la ficha (p. ej. para vendedor o usuario) pase el usuario con el campo `image` o `avatar` cargado. En la vista show, añadir el mismo bloque que en el formulario de edición para mostrar la foto: por ejemplo `<div class="photo-preview-3" style="background-image: url('{{ asset('uploads/' . $user->image) }}');">` (o el campo que use el modelo User). Si la imagen se guarda en otro disco (storage), usar `Storage::url($user->image)` y asegurar el enlace simbólico `php artisan storage:link`.

- **Tarea 21:** Cuando estás dentro de un usuario, le das a Cartera o Historial y no te dirige a ningún sitio. Si le das a Cartera debería abrirse la cartera y poner que no hay movimientos o que no hay productos, y en el historial igual.

  **Posibles soluciones:**
  - Revisar los enlaces “Cartera” e “Historial” en la vista de detalle del usuario (p. ej. `sellers/show` o `users/show`). Deben apuntar a rutas existentes, por ejemplo `route('sellers.wallet', $user->id)` y `route('sellers.history', $user->id)` (o equivalentes). Si esas rutas no existen, crearlas: cada una mostrará la misma estructura de cartera/historial pero filtrada por ese usuario, y si no hay datos mostrar mensaje “No hay movimientos” / “No hay productos”.

- **Tarea 22:** En la sección de usuarios, eliges uno y al entrar (sin darle a editar) sale el estado y el botón “Cambiar” pero el botón no funciona. No debería estar ahí y debería estar en la parte izquierda como los vendedores o los gestores y obviamente funcionar.

  **Posibles soluciones:**
  - Revisar la vista de detalle del usuario (show): quitar el bloque “estado + Cambiar” de la posición actual si no funciona. Añadir en la barra lateral izquierda (junto a “Vendedores”, “Gestores”, etc.) un bloque “Estado” con el estado actual y un botón “Cambiar” que envíe a la ruta que actualice el estado (por ejemplo POST a `users/{id}/status` o similar). Conectar el botón al endpoint que cambie el estado y recargue o redirija para que el cambio se vea.

---

## Participaciones y tacos

- **Tarea 23:** En la sección participaciones has creado dos sets físicos y uno digital. En el primer set físico le das a “saber”, te salen los tacos y le das a un taco y te salen las participaciones pero te pone que están vendidas las 60 y disponibles las 60. En el siguiente set físico (3/00001, contando el digital como set) le das a ver tacos, te sale el taco pero si le das dentro no hay participaciones y también están como vendidas todas y disponibles. NUNCA puede estar así: la suma de todas las categorías (vendidas, devueltas, anuladas, disponibles, disponibles DV) debe ser coherente.

  **Posibles soluciones:**
  - Revisar cómo se calculan “vendidas” y “disponibles” en la vista de participaciones/tacos. Debe haber una única fuente de verdad: por ejemplo conteo por `status` de las filas de `participations` (vendida, devuelta, anulada, disponible, disponible_dv). Ajustar la query para que los totales se obtengan con `selectRaw`/`groupBy` por status y que “disponibles” + “vendidas” + “devueltas” + “anuladas” + “disponibles_dv” = total de participaciones del set/taco. Revisar también que al listar participaciones de un taco no se filtren mal (por ejemplo por `design_format_id` o `participation_number`) y que no se dupliquen filas. Si el set 3/00001 no tiene participaciones creadas aún (porque es digital o no se han generado), no debería mostrarse “60 vendidas y 60 disponibles”; en ese caso mostrar 0 en cada categoría hasta que existan registros en `participations` para ese set/taco, y asegurar que la creación de participaciones al generar el PDF o al activar el set rellene correctamente la tabla.

