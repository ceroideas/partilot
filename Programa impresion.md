# Programa de impresión – Análisis y soluciones

- **1. Configurar márgenes: desplegable y guardar al dar Siguiente** ✅  
  Cuando se empieza a configurar, "Configurar márgenes" debería estar colapsado (p. ej. con un botón "Desplegar"); al dar a Desplegar se muestran las opciones. El botón "Guardar" confunde: se quiere que al dar "Siguiente" se guarde automáticamente y no haga falta un Guardar aparte.  
  **Dónde:** `resources/views/design/format.blade.php` (paso 1).  
  **Solución:** (1) Envolver la sección "Configurar márgenes" (desde el `<h4>` hasta el final de los inputs de márgenes/sangres/matriz) en un bloque colapsable (Bootstrap collapse): botón "Desplegar" que muestra/oculta el contenido. (2) En el JS del paso 1, al hacer clic en "Siguiente", guardar en el mismo flujo los valores de márgenes (y el resto del paso) antes de pasar al paso 2; no exigir un "Guardar" previo.

- **2. Texto "ticket(s)" → "participación/es"** ✅  
  Donde se pone "ticket(s)" debe decir "participación" o "participaciones".  
  **Dónde:** `resources/views/design/format.blade.php` y `resources/views/design/edit_format.blade.php`.  
  **Solución:** Sustituir en las vistas: "Medidas de cada ticket" → "Medidas de cada participación"; "Cantidad de tickets por hoja" → "Cantidad de participaciones por hoja"; "fondo del ticket" (tooltips/botones) → "fondo de la participación"; "Seleccionar fondo del ticket" (modal) → "Seleccionar fondo de la participación". Revisar también etiquetas como "Fondo ticket" en `edit_format.blade.php`. En el JS, variables como `ticketSizes` pueden quedarse por consistencia interna; lo que ve el usuario debe ser "participación/es".

- **3. Borde visible al seleccionar un campo**  
  Cuando se selecciona un campo en el diseño, debe verse bien el borde para apreciar el tamaño.  
  **Dónde:** `resources/views/design/format.blade.php` (y `edit_format.blade.php` si aplica): estilos `.elements.selected` y lógica que añade/quita la clase `selected`.  
  **Solución:** Reforzar el estilo de `.elements.selected`: por ejemplo `border: 2px solid #007bff !important; outline: 2px solid rgba(0,123,255,0.3); box-shadow: 0 0 0 2px rgba(0,123,255,0.2);` para que el borde sea muy visible. Asegurar que al hacer clic en un elemento se añada `selected` y se quite en los demás, y que el contenedor del diseño no robe el foco de forma que se pierda el borde.

- **4. Borrar campos con Suprimir o Borrar (teclado)** ✅  
  Además del botón de papelera, poder eliminar el elemento seleccionado con la tecla Suprimir o Retroceso.  
  **Dónde:** `resources/views/design/format.blade.php` y `edit_format.blade.php` (manejadores de teclado en el flujo del editor).  
  **Solución:** Registrar un `keydown` (por ejemplo en `$(document)` o en el contenedor del diseño) que, si hay un elemento seleccionado (`.elements.selected`) y la tecla es `Delete` o `Backspace`, llame a la misma lógica que el botón de eliminar (sin dejar que el evento borre texto dentro de inputs). Para elementos críticos (ref., QR, nº participación) no ejecutar eliminación aunque se pulse Suprimir (véase punto 5).

- **5. Elementos críticos: no eliminar ni cubrir**  
  El número de referencia, el código QR y el número de participación (en la participación y en la matriz) son críticos: se pueden mover, redimensionar, etc., pero no eliminar ni tener nada por encima (z-index).  
  **Dónde:** `resources/views/design/format.blade.php` (y `edit_format.blade.php`): elementos con referencia (placeholder de 20 dígitos), clase `.qr` y elemento con texto tipo "1/0001" (clase `.participation` o equivalente). PDF: `pdf_participation.blade.php` reemplaza referencia y "1/0001".  
  **Solución:** (1) Marcar elementos críticos en HTML con una clase fija, p. ej. `element-critical` (o `data-critical="true"`), en: el bloque que muestra la referencia, el `.elements.qr` y el que muestra "Nº 1/0001". (2) En el JS: al eliminar (botón papelera o Suprimir/Backspace), comprobar si el elemento seleccionado tiene esa clase/atributo; si es crítico, no eliminar y mostrar un mensaje breve ("Este elemento es obligatorio y no se puede eliminar"). (3) En el JS de capas (subir/bajar), no permitir que un elemento no crítico quede por encima de los críticos: al subir capa, si el elemento actual no es crítico y va a quedar por encima de un crítico, limitar z-index para que los críticos queden siempre visibles por encima del resto (p. ej. asignar z-index base alto a `.element-critical` y no superarlo con elementos no críticos).

- **6. Botón de zoom** ✅  
  No hay botón de zoom en el programa de impresión.  
  **Dónde:** Barra de herramientas del diseño en `format.blade.php` y `edit_format.blade.php`.  
  **Solución:** Añadir controles de zoom (p. ej. "+" / "-" y/o selector de porcentaje) que apliquen `transform: scale(...)` (o `zoom`) al contenedor del diseño (p. ej. el div que envuelve `.format-box` / `#containment-wrapper`). Guardar la escala en una variable y actualizar al cambiar; opcionalmente persistir en `localStorage` por paso.

- **7. Indicador de carga (subir imagen, guardar, etc.)**  
  Cuando el programa está haciendo algo (subir imagen, guardar, cambiar algo…) no se ve feedback y no se sabe si ha registrado la acción.  
  **Dónde:** Llamadas asíncronas en `format.blade.php` y `edit_format.blade.php`: `fetch('api/upload-image')`, `fetch('api/design/save-format')`, `fetch('api/generarQr')`, `$.ajax` para `api/design/save-snapshot`, y botones que disparan esas acciones.  
  **Solución:** (1) Mostrar un indicador de carga (spinner/overlay) al iniciar la petición y ocultarlo al terminar (then/finally o done/fail/always). (2) Deshabilitar el botón o el área relevante mientras dura la petición para evitar doble clic. (3) Reutilizar el mismo componente (p. ej. un div fijo con spinner y texto "Guardando..." / "Subiendo imagen...") para todas las operaciones del diseño.

- **8. Error "Token no proporcionado" al guardar para pasar a diseñar portada** ✅  
  Al dar a guardar para pasar a diseñar portada aparece `{ success: false, message: "Token no proporcionado." }` y no se puede seguir.  
  **Causa:** Las rutas `POST /api/design/save-format` y `POST /api/design/save-snapshot` están definidas dos veces en `routes/api.php`: una vez sin middleware (líneas ~54 y ~61) y otra dentro de `Route::middleware('auth.api')` (grupo que usa `AuthenticateApiToken`). Si la petición llega a la ruta protegida (p. ej. por orden de registro o caché de rutas), el middleware exige Bearer y devuelve ese mensaje. La pantalla de diseño se usa desde la web con sesión, sin envío de token.  
  **Solución:** Asegurar que las peticiones desde el formulario web de diseño no requieran token. La opción más segura es **quitar las rutas duplicadas de diseño del grupo `auth.api`** en `routes/api.php`, dejando solo las definiciones públicas de `design/save-format` y `design/save-snapshot`, de modo que el guardado desde la web use solo sesión/CSRF. Si en el futuro se llama a estas mismas URLs desde una app móvil con token, se pueden añadir rutas específicas bajo `auth.api` con otro path (p. ej. `design/mobile/save-format`) o decidir si el diseño por web debe usar otra ruta con middleware `web` + `auth` (sesión).

---

## Resumen de archivos a tocar

| Punto | Archivos |
|-------|----------|
| 1 | ✅ `resources/views/design/format.blade.php` (collapse + guardar al Siguiente) |
| 2 | ✅ `format.blade.php`, `edit_format.blade.php` (textos ticket → participación) |
| 3 | ✅ `format.blade.php`, `edit_format.blade.php` – CSS borde selección |
| 4 | ✅ `format.blade.php`, `edit_format.blade.php` – keydown Delete/Backspace |
| 5 | ✅ `format.blade.php`, `edit_format.blade.php` – element-critical + JS |
| 6 | ✅ `format.blade.php` – controles zoom (pasos 2, 3, 4) |
| 7 | ✅ `format.blade.php`, `edit_format.blade.php` – spinner/overlay en fetch/ajax |
| 8 | `routes/api.php` – ✅ eliminadas rutas duplicadas de design dentro de `auth.api` |
