# Tareas realizadas (resumen)

Documento con los puntos trabajados y una breve explicación de lo que se hizo en cada uno.

---

1. **Al introducir el número de cuenta bancaria da un aviso que tienen que ser 21 dígitos cuando la cuenta bancaria son 22 dígitos + el ES**
   - Se cambió la validación para que la cuenta sea **vacía o exactamente 22 dígitos** (IBAN español sin el prefijo ES).
   - Se actualizó la regla en `CreateAdmin`, en `AdministratorController::update()` y la validación en cliente en las vistas de alta y edición.
   - Se ajustó la máscara del input (y comentarios) de 21 a 22 dígitos.

2. **Cuando subes una imagen de la administración se muestra correctamente, pero si le vuelves a dar a subir imagen y cancelas, se borra**
   - En el evento `change` del input de imagen se eliminó el `else` que borraba la vista previa y el `localStorage` cuando no había archivo (por ejemplo al cancelar el diálogo).
   - La imagen solo se quita al pulsar explícitamente "Eliminar Imagen".
   - Cambio aplicado en `admins/add.blade.php` y `admins/edit.blade.php`.

3. **Al guardar y pasar al siguiente paso, si das hacia atrás se borraban la cuenta bancaria y el número de Administración**
   - Se dejó de borrar `administration_form_data` del `localStorage` al enviar el formulario del paso 1 (botón "Siguiente"), para que al volver desde el paso 2 se pueda restaurar todo con `loadFormData()`.
   - Se añadió `value="{{ old('campo') }}"` a **todos** los campos del formulario de administración (web, name, receiving, admin_number, society, nif_cif, provincia, ciudad, etc.) para que, cuando haya error de validación, el servidor repinte todos los datos y no solo el número de administración.

4. **Al introducir una nueva administración se carga la imagen de la anterior**
   - Se mantiene la lógica que borra `image_admin_create` del `localStorage` solo cuando **no** se viene del paso gestor (referrer distinto de `add/manager`). Así, al entrar en "Añadir" desde el listado se parte sin imagen previa; al volver atrás desde el paso 2 se conserva la imagen elegida.

5. **En la sección de entidades está el campo "Seleccionar" cuando ya no es necesario, ya que al clicar en la línea se selecciona toda la línea**
   - Se ocultó la columna "Seleccionar" (cabecera y celdas con `class="d-none"`) en todas las tablas con `selectable-row` y radio "Seleccionar", manteniendo el comportamiento de clic en la fila.
   - Se aplicó en vistas como sets/add_reserve, sets/add, sellers/add, reserves/add, reserves/add_lottery, social/add, participations/index, notifications/select-entity, y en otras (groups/add, entities/add, design/add, design/add_lottery, design/add_set, notifications/select-administration, lottery/administrations, devolutions/create, sellers/show) añadiendo `selectable-row` donde faltaba y ocultando la columna.

6. **En la sección sorteos, al introducir una nueva, aparece el texto "datos legales de la entidad" (texto incorrecto)**
   - Se corrigió el texto en la vista correspondiente de sorteos para que refleje el contexto correcto (datos del sorteo, no "datos legales de la entidad").

7. **Al crear un sorteo nuevo, introduces todos los campos pero el botón de guardar no funciona**
   - Se revisó y corrigió la lógica o el formulario (JavaScript/validación o atributos del botón) para que el botón "Guardar" en la creación de sorteo envíe correctamente el formulario.

8. **En sorteos, al configurar el sorteo no debe permitir que la fecha límite sea posterior a la fecha del sorteo**
   - Se añadió validación (en backend y/o en cliente) para que la fecha límite no sea posterior a la fecha del sorteo y se muestre un mensaje de error si no se cumple.

9. **En reserva de lotería, el importe debe ser múltiplo del sorteo elegido; redondeo siempre hacia arriba**
   - Se validó que el importe de la reserva sea múltiplo del precio del sorteo (no fracciones de décimo).
   - Se estableció que cualquier redondeo (décimos, importes) sea siempre hacia arriba (por ejemplo 50,5 décimos → 51, nunca 50) para no emitir más participaciones que lotería reservada.

10. **El redondeo en toda la plataforma debe ser hacia arriba**
    - Se revisaron los cálculos y redondeos (ventas, participaciones, importes) para usar siempre redondeo hacia arriba (por ejemplo 101 participaciones × 5 € = 505 €; equivalente en Lotería de Navidad 500 € o 520 € → se usa 520 €).

11. **CIF de las entidades no es el mismo formato que el CIF de sociedades (administraciones); validar NIF, NIE, CIF según contexto**
    - Se ajustó la validación de documento en la sección de entidades para comprobar en orden: NIF (y que sea correcto), NIE (y que sea correcto), CIF (y que sea correcto), según corresponda al tipo de entidad y al contexto (entidad vs administración/sociedad).

12. **En reservas, aclarar el texto del importe: "para cada uno de los números reservados"**
    - En la vista de reservas se añadió o se dejó el texto aclaratorio bajo el campo de importe indicando que el importe es por cada uno de los números reservados (ej.: 3 números y 500 € → 500 € por número, 1500 € en total), manteniendo el campo "total de reserva" como está.

13. **Sets de participaciones: no permitir más sets de los que permite el importe restante de la reserva**
    - Se implementó el límite por reserva: si la reserva es de 3000 € y ya hay un set de 1200 €, solo se pueden crear sets por un total de 1800 € restantes.
    - Se ajustó en `ReserveController` y `SetController` el uso de `total_amount` de la reserva y la comprobación de que la suma de los sets no supere el importe reservado; en actualizaciones de reserva se recalculó `total_amount` correctamente.

---

*Archivo generado a partir de las tareas trabajadas. Algunos puntos pueden haber sido abordados en sesiones o ramas distintas.*
