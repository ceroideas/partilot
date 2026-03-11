# Devoluciones de participaciones digitales – Propuesta

## Situación actual

- **Devoluciones (web/app):** El flujo está pensado para participaciones **físicas**:
  - Se elige reserva → se introducen **rango** (desde/hasta) o **una participación** por número/QR.
  - El backend usa `participation_number` y `desde`/`hasta`; no distingue explícitamente set físico vs digital.
- **Participaciones digitales:** Existen como filas en `participations` con `participation_code` tipo `1D/00001` y `participation_number` 1, 2, 3… (igual que físicas pero en sets con `digital_participations` > 0).
- En la práctica **no hay un flujo claro** para “devolver X participaciones digitales” (por cantidad o por lista), y la UI de devoluciones (rango desde/hasta) no está orientada a digital.

---

## Opciones de diseño

### Opción A – Devolución por cantidad (recomendada para digital)

**Idea:** En reservas/sets con participaciones digitales, permitir “Devolver **N** participaciones digitales” sin elegir números concretos.

- **Web (devolutions/create):**
  - Si la reserva elegida tiene al menos un set **solo digital** (o participaciones digitales):
    - Mostrar bloque: “Participaciones digitales – Cantidad a devolver” con input numérico y botón “Añadir a la devolución”.
  - Al enviar, el front envía por ejemplo `liquidacion.devolver_digital: { reserve_id, cantidad }` o `cantidad_digital: N` + `reserve_id`.
- **Backend:**
  - Aceptar `cantidad_digital` (o similar) + `reserve_id` (o `set_id` si se trabaja por set).
  - Resolver participaciones del vendedor (o entidad) en esa reserva/set con `participation_code LIKE '1D/%'` y `status IN ('asignada','disponible')`, ordenadas por id (o por número), tomar las primeras **N** y tratarlas igual que las físicas en la devolución:
    - Crear filas en `devolution_details` con `action` = `devolver` o `devolver_vendedor`.
    - Poner las participaciones en `disponible`, quitar `seller_id` si aplica, registrar en `participation_activity_logs`.
  - Reutilizar la misma transacción y el mismo `Devolution` que para las participaciones físicas (una sola devolución puede mezclar físicas + digitales).

**Ventajas:** Muy simple para el usuario (solo indica “cuántas”). Coherente con “asignar por cantidad” en digital.  
**Inconvenientes:** No se eligen números concretos (se devuelven las primeras N por el criterio que se defina).

---

### Opción B – Listar participaciones digitales y elegir cuáles devolver

**Idea:** Mostrar la lista de participaciones digitales asignadas (por reserva/set y vendedor) y marcar cuáles devolver (checkboxes o selección múltiple).

- **Backend:** Endpoint o ampliación del actual que, dado `reserve_id` (o `set_id`) + `seller_id`, devuelva participaciones con `participation_code LIKE '1D/%'` y `status IN ('asignada','disponible')`.
- **Web/App:** En el paso de “elegir participaciones a devolver”, si hay digitales, mostrar una sección “Participaciones digitales” con esa lista y checkboxes; las seleccionadas se envían como IDs en `liquidacion.devolver` (igual que las físicas que ya se envían por ID).

**Ventajas:** Control total (el usuario ve y elige cada participación).  
**Inconvenientes:** Más carga en UI y en datos si hay muchas digitales; puede ser incómodo en móvil.

---

### Opción C – Mismo flujo que físico (rango por número)

**Idea:** Las digitales ya tienen `participation_number` (1, 2, 3…). Permitir en la misma pantalla “desde/hasta” que, si la reserva/set es digital, interprete el rango sobre esas participaciones.

- **Cambios mínimos:** Asegurar que al validar participaciones por reserva + desde/hasta no se excluyan sets digitales (hoy el filtro es por `reserve_id`/`set_id` y `participation_number`; si el set es digital, las filas existen y el filtro ya las incluiría).
- Comprobar que en `devolutions/create` se carguen reservas/sets que tengan **solo** digital (hoy podría estar oculto si algo filtra por “tiene físicas”).
- En la UI, dejar claro que “Desde/Hasta” en un set digital se refiere a los números 1…N de ese set digital.

**Ventajas:** Un solo flujo para físico y digital; pocos cambios.  
**Inconvenientes:** Menos intuitivo para quien piensa en “devolver 5 digitales” sin pensar en números; si hay varios sets digitales en la reserva, hay que aclarar a qué set aplica el rango (p. ej. elegir set dentro de la reserva cuando hay varios).

---

## Recomendación

- **Fase 1 (rápida):** Implementar **Opción A (devolución por cantidad)** para participaciones digitales:
  - Backend: parámetro `cantidad_digital` + `reserve_id` (y opcionalmente `set_id` si se quiere acotar a un set). Resolver N participaciones digitales asignadas/devolubles y añadirlas a la misma devolución.
  - Web: en `devolutions/create`, al elegir reserva, si tiene sets digitales, mostrar “Cantidad de participaciones digitales a devolver” y botón “Añadir”; enviar esa cantidad en el payload.
  - App: mismo concepto en la pantalla de devolución (gestor-devolución).
- **Fase 2 (opcional):** Si se necesita elegir participaciones concretas, añadir **Opción B** (listado con checkboxes) para digitales, reutilizando el mismo `liquidacion.devolver` por IDs.

---

## Detalles técnicos (Opción A)

1. **Identificar set/reserva digital**
   - Set: `digital_participations > 0 && physical_participations == 0`.
   - Reserva: tiene al menos un set que cumpla lo anterior.

2. **Query participaciones digitales a devolver**
   - Condiciones: `reserve_id` (vía `sets.reserve_id`) o `set_id`, `seller_id` si devolución vendedor→entidad, `status IN ('asignada','disponible')`, `participation_code LIKE '1D/%'`.
   - Orden: por ejemplo `participations.id` o `participation_number`.
   - Limitar a `cantidad_digital`.

3. **Registro**
   - Mismas tablas y acciones que para físicas: `devolution_details` (`action` = `devolver` / `devolver_vendedor`), actualización de `participations` (status, seller_id), `participation_activity_logs`.

4. **Validación**
   - Comprobar que la reserva/set tenga suficientes participaciones digitales asignadas/devolubles antes de crear la devolución.

Si quieres, el siguiente paso puede ser bajar esto a cambios concretos en `DevolutionsController`, rutas y en `create.blade.php` (y en la app) para la Opción A.

Nota: Me encaja hacer la opcion A sin opcionales teniendo en cuenta lo siguiente también: tenemos 2 flujos de devoluciones, la de vendedor a entidad y la de entidad a administración. En la primera hay que tener en cuenta que las participaciones, sean fisicas o digitales que se devuelvan pasan a estar disponibles para reasignarlas y las restantes siguen con su status normal. Cuando es de entidad a administración las devueltas si cambian a devueltas y las restantes se deberan liquidar ya que se consideran vendidas.