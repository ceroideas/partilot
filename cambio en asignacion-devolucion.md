necesito que ubiques donde es que estamos asignando las participaciones, devolucion y liquidaciones de un vendedor y de una entidad para realizar los siguientes cambios:

- al momento de realizar una asignación de participaciones, estamos seleccionando un set para tal fin, pero esto redunda un poco ya que los sets se crean (dentro de la misma reserva) siguiendo una secuencia numerica, por lo tanto tendriamos por ejemplo set 1 del 1 al 100, set 2 del 101 al 200 y set 3 del 201 al 300, entonces cual es el problema? que seleccionar set es un paso que deberiamos reducir a seleccionar una reserva, ya que en este caso si seleccionamos asignar participaciones fisicas, estas siempre van a tener una numeracion fija (aunque el set sea 1, 2, 3, etc) nunca va a haber en la misma reserva mas de 1 set con una participacion con el mismo numero (a menos que sean digitales pero estas se asignan por cantidad y no por rango, aunque tambien en las participaciones digitales deberiamos poder seleccionar reserva), entonces seria cambiar el select de seleccionar set, por seleccionar reserva PERO SOLO EN CASO EN QUE HAYA MAS DE 1 RESERVA ya que si solo hay 1 se seleccionaria automáticamente esta reserva, luego en el backend si seguimos por el ejemplo anterior, tengo los sets 1,2 y 3, del 1 al 100, del 101 al 200 y del 201 al 300, si quiero asignar participaciones en un rango que sobrepasa 1 set, por ejemplo del 91 al 110, creo que no pasa nada porque las participaciones se tratan por separado.

al momento de devolver tendriamos que hacer algo ligeramente distinto ya que actualmente tenemos la selección de sets tambien, seria cambiar por reservas, pero en caso de querer devolver participaciones de vendedor a entidad habria que tambien la seleccion de reserva y mostrar el rango de participaciones que tiene el vendedor asignadas para saber que devolver. captura 1

otra cosa es que actualmente si devuelvo 5 participaciones de un set fisico de vendedor a entidad, por ejemplo del 1 al 5, me quedan del 6 al 100 y en este momento las estaria marcando como vendidas y liquidandolas, pero lo que haremos es que en esta devolución vamos a colocar un boton mas a la izquierda y en color #333 que solo realice la devolución de las participaciones que he marcado para devolver (con texto aceptar), sin mandar a liquidar. y sin marcar las restantes como vendidas, seguiran como disponibles

al realizar devoluciones tambien es necesario que se marquen cuantas participaciones se han devuelto y que tipo de devolución estoy realizando, vendedor a entidad o de entidad a administracion, cuantas quedaron en el total que estan disponibles (cuando no se venden)

y teniendo en cuenta que en los logs se marca cuando una participacion ha sido devuelta por el vendedor, necesito que en participations, al mostrar el status de la participación, si el ultimo status es devuelta por el vendedor, el status de la participacion pase de ser "DISPONIBLE" a "DISPONIBLE DV"

si hay alguna duda con algo podemos seguir revisando

una vez que tengamos esto, debemos hacer el mismo cambio en la aplicación, al asignar y devolver, cambiar sets por reservas

---

## Propuesta de implementación

### 1. Tabla `participations`: ¿añadir columna reserva?

**No hace falta añadir una columna `reserve_id` (o "reserva") en `participations`.**

- Hoy: `participations.set_id` → `sets.reserve_id`. La reserva se obtiene como `participation->set->reserve`.
- El cambio es de **interfaz**: en asignación y devolución se elige **reserva** en lugar de **set**. En backend se siguen manejando participaciones (y por tanto sets) como hasta ahora; solo cambia cómo se construye el selector (por reserva) y qué participaciones se ofrecen (todas las de esa reserva, pudiendo cruzar sets si el rango lo requiere).
- Añadir `reserve_id` en `participations` sería redundante y obligaría a mantener dos FKs (set_id y reserve_id). Si en el futuro se prioriza rendimiento en consultas muy pesadas por reserva, se podría valorar denormalizar; por ahora no es necesario.

### 2. Resumen de cambios a realizar

| Área | Qué hacer |
|------|------------|
| **Asignación (web)** | Sustituir selector "Set" por "Reserva". Si la entidad tiene solo 1 reserva, preseleccionarla y no mostrar selector. Backend: al asignar por rango (ej. 91–110), seguir tratando participaciones por separado aunque crucen sets (sets 1 y 2). |
| **Devolución (web)** | Selector por reserva en lugar de set. En devolución vendedor → entidad: elegir reserva y mostrar el rango de participaciones asignadas al vendedor para esa reserva. |
| **Devolución sin liquidar** | Añadir botón a la izquierda, color #333, texto "Aceptar": solo ejecuta la devolución de las participaciones marcadas, **sin** mandar a liquidar. El flujo actual (devolver + liquidar) se mantiene en el botón existente. |
| **Registro de devoluciones** | Registrar: cantidad devuelta, tipo (vendedor→entidad / entidad→administración), y cuántas quedan disponibles (no vendidas). Reutilizar/ampliar tablas existentes (`devolutions`, `devolution_details`, `participation_activity_logs`) según convenga. |
| **Estado "DISPONIBLE DV"** | **No** añadir nuevo valor al enum de `participations.status`. Mantener en BD `status = 'disponible'`. Al **mostrar** el estado: si el último registro en `participation_activity_logs` para esa participación es `activity_type = 'returned_by_seller'`, mostrar la etiqueta como **"DISPONIBLE DV"** en lugar de "DISPONIBLE". Implementar esta lógica en vista/API (accesor en modelo o consulta del último log). |
| **App (móvil)** | Mismo criterio: asignar y devolver por reserva en lugar de por set. |

### 3. Dónde está el código actual

- **Asignación / devolución (web):** `resources/views/devolutions/create.blade.php` (selector `#selector-set`, flujo asignar/devolver); `app/Http/Controllers/DevolutionsController.php` (lógica por `set_id`, validaciones, creación de devoluciones).
- **Listado y detalle de devoluciones:** `resources/views/devolutions/index.blade.php`, `show.blade.php`, `edit.blade.php` (muestran set/reserva).
- **Estado de participación:** modelo `Participation` y vistas/APIs que muestran `status`; tabla `participation_activity_logs` (campo `activity_type`: `returned_by_seller`).
- **API (app):** `app/Http/Controllers/ApiController.php` (endpoints que usan `set_id` para asignar/devolver).

### 4. Orden sugerido

1. Backend: endpoints/acciones que acepten `reserve_id` además de (o en lugar de) `set_id` donde corresponda; al recibir reserva, resolver los sets de esa reserva y trabajar con sus participaciones.
2. Web: cambiar selector set → reserva en `devolutions/create.blade.php` y lógica JS que carga sets/participaciones; botón "Aceptar" solo devolución.
3. Mostrar "DISPONIBLE DV" según último log (vista/API).
4. Registro explícito de tipo de devolución y totales disponibles.
5. Réplica del flujo por reserva en la aplicación móvil.