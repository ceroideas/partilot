# Flujo de Devoluciones (Laravel → Ionic)

Resumen del flujo de devoluciones en la web Laravel para replicar en la app Ionic: selector de entidades, sorteos y devolución por unidad o por rango.

---

## 1. Orden del flujo en la web

1. **Entidad** → Seleccionar entidad para la devolución.
2. **Tipo de devolución** → Devolución Vendedor | Devolución Administración | Anulación.
3. *(Si es vendedor)* **Vendedor** → Seleccionar vendedor.
4. **Sorteo** → Sorteos asignados a la entidad (por reservas de esa entidad).
5. **Participaciones** → Elegir **Set** y luego **por unidad** (un número) o **por rango** (desde–hasta).
6. **Liquidación** → Resumen, pagos y aceptar.

Para la app podéis simplificar a: **Entidad → Sorteo → Vista unidad/rango** (y luego liquidación si la app lo incluye).

---

## 2. APIs usadas (rutas API Laravel)

Base: `/api/devolutions/` (con auth).

| Uso | Método | Ruta | Parámetros | Respuesta |
|-----|--------|------|------------|-----------|
| Listar entidades | GET | `/entities` | — | `{ success, entities: [{ id, name, province, city, administration_name, status }] }` |
| Sorteos de la entidad | GET | `/lotteries` | `entity_id` | `{ success, lotteries: [{ id, name, description, draw_date }] }` |
| Sets de la entidad + sorteo | GET | `/sets-by-entity` | `entity_id`, `lottery_id` | `{ success, sets: [{ id, set_name, set_number, reserve_id, reserve }] }` |
| Validar participaciones (unidad o rango) | POST | `/validate` | Ver abajo | `{ success, participations: [{ id, number, participation_code }] }` |
| Resumen liquidación | GET | `/liquidation-summary` | Ver abajo | `{ success, summary: { total_participations, ventas_registradas, returned_participations, total_liquidation, ... } }` |
| Crear devolución | POST | `/` | Ver abajo | `{ success, devolution_id }` |

*(Si en la app hacéis también “devolución vendedor”, se usan además `GET /sellers?entity_id=`, `GET /sets?seller_id=&lottery_id=` y en validate/store `seller_id`.)*

---

## 3. Validar participaciones (unidad o rango)

**POST** `/api/devolutions/validate`

- **Siempre:** `entity_id`, `lottery_id`, `set_id`.
- **Opcional (devolución vendedor):** `seller_id`.
- **Por rango:** `desde`, `hasta` (números de participación, no IDs).
- **Por unidad:** `participation_id` = número de participación (no el ID de BD).

Ejemplo por rango:

```json
{
  "entity_id": 1,
  "lottery_id": 2,
  "set_id": 10,
  "desde": 1,
  "hasta": 50
}
```

Ejemplo por unidad:

```json
{
  "entity_id": 1,
  "lottery_id": 2,
  "set_id": 10,
  "participation_id": 25
}
```

Respuesta: `{ "success": true, "participations": [ { "id", "number", "participation_code" }, ... ] }`.  
Esas participaciones son las que luego se envían como “a devolver” en el store.

---

## 4. Resumen de liquidación

**GET** `/api/devolutions/liquidation-summary`

Query: `entity_id`, `lottery_id`, `set_id` (opcional si hay participaciones), `participations[]` = array de IDs de participación (las seleccionadas a devolver).

Sirve para mostrar total participaciones, ventas registradas, devueltas y total a pagar antes de confirmar.

---

## 5. Crear devolución (store)

**POST** `/api/devolutions/`

Cuerpo (devolución administración, sin vendedor):

```json
{
  "entity_id": 1,
  "lottery_id": 2,
  "set_id": 10,
  "return_reason": "Devolución de entidad a administración",
  "liquidacion": {
    "devolver": [ 101, 102, 103 ],
    "vender": [],
    "pagos": [
      { "payment_method": "efectivo", "amount": 100 },
      { "payment_method": "bizum", "amount": 50 }
    ]
  }
}
```

- `liquidacion.devolver`: IDs de participación que se devuelven (las que el usuario eligió por unidad/rango).
- `liquidacion.vender`: el backend lo calcula (las del set que no se devuelven).
- `liquidacion.pagos`: opcional; si no hay pagos, se puede enviar array vacío.

Para **devolución vendedor** añadir `seller_id` en la raíz y en validate.

---

## 6. Vista “por unidad o por rango” (paso participaciones)

En la web:

- **Set:** desplegable/cards con los sets devueltos por `sets-by-entity` (entity + lottery ya elegidos).
- **Por rango:** dos inputs “Desde” y “Hasta” (números de participación). Al validar se llama a `validate` con `desde` y `hasta`.
- **Por unidad:** un input “Número de participación”. Al validar se llama a `validate` con `participation_id`.
- Mutuamente excluyentes: si se rellena rango, se deshabilitan unidad y viceversa (como en la tercera captura de la app).

Las participaciones devueltas por `validate` se añaden a una lista “participaciones a devolver”; luego se pasa a liquidación (resumen + pagos) y finalmente a `POST /api/devolutions/` con esos IDs en `liquidacion.devolver`.

---

## 7. Resumen para la app Ionic

1. **Selector de entidades** → `GET /api/devolutions/entities` (misma idea que en otras secciones).
2. **Selector de sorteos** → `GET /api/devolutions/lotteries?entity_id=X` (sorteos asignados a la entidad).
3. **Vista unidad/rango:**
   - Cargar sets: `GET /api/devolutions/sets-by-entity?entity_id=X&lottery_id=Y`.
   - Usuario elige set y luego:
     - **Unidad:** input número → `POST /api/devolutions/validate` con `entity_id`, `lottery_id`, `set_id`, `participation_id`.
     - **Rango:** inputs desde/hasta → mismo `validate` con `desde` y `hasta`.
   - Añadir las participaciones devueltas por validate a la “lista a devolver”.
4. **Liquidación (opcional en app):** `GET /api/devolutions/liquidation-summary?...&participations[]=...` y luego `POST /api/devolutions/` con `liquidacion.devolver` y `liquidacion.pagos` si aplica.

Con esto tenéis el flujo Laravel documentado para replicar en Ionic: entidad → sorteo → vista por unidad o por rango (y luego liquidación si la implementáis).
