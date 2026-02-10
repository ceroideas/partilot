# Tareas a realizar

---

## 1. Imagen de la administración en todos los sitios ✅

**Problema:** La imagen de la administración tiene que aparecer en todos los sitios donde está el icono (ejemplo: cuando pasas a meter los datos de la persona encargada de la administración, pone los datos de la administración pero no sale la imagen subida. Tampoco sale cuando vas a crear entidad y eliges la administración; abajo a la izquierda no aparece el logo de la administración).

**Soluciones aplicables:**

- **Datos de la persona encargada (gestor de la administración):** En `resources/views/admins/add_manager.blade.php` ya se usa `session('administration.image')` para el logo (líneas 107-108). Comprobar que al guardar el paso 1 de “crear administración” la imagen quede en sesión con la clave correcta (p. ej. `session('administration.image')`) y que la ruta sea la misma que usa el controlador (en `AdministratorController` las imágenes se guardan en `public_path('images')`, por tanto la URL debe ser `url('images/' . session('administration.image'))`). Si la sesión no se rellena con la imagen al elegir/subir en el paso 1, hay que asegurar en `AdministratorController` que en el método que guarda el paso de “administración” se haga `session(['administration' => array_merge(session('administration', []), ['image' => $filename]])` (o equivalente) cuando se sube imagen.
- **Crear entidad – elegir administración:** En `resources/views/entities/add.blade.php` la tabla de administraciones solo muestra nombre, Nº receptor, provincia, etc., y no hay celda/columna para el logo. Añadir una columna (o un bloque “abajo a la izquierda” según el diseño) que muestre el logo de cada administración. Como el listado viene de `$administrations`, cada fila tiene `$administration->image`. Mostrar el logo con algo como: `@if($administration->image) <img src="{{ asset('images/' . $administration->image) }}" alt="Logo" class="..." /> @else <i class="ri-building-line"></i> @endif`. Si el diseño es “abajo a la izquierda” al seleccionar una fila, se puede rellenar un panel lateral con los datos de la administración seleccionada (incluida la imagen) vía JavaScript al marcar el radio de esa fila.
- **Resumen:** Revisar todos los puntos del flujo “administraciones” (add, edit, show, add_manager, edit_manager) y sustituir el icono genérico por la imagen cuando exista `administration->image` o `session('administration.image')`, usando siempre la ruta `images/` acorde a donde guarda `AdministratorController`.

---

## 2. Logo de la entidad donde corresponda ✅

**Problema:** El logo de la entidad tiene que aparecer donde corresponda, además de donde se sube (ejemplo: cuando das de alta un gestor salen los datos de la entidad pero no el logo).

**Soluciones aplicables:**

- **Alta de gestor desde una entidad:** En `resources/views/entities/show.blade.php`, en el formulario “Registrar gestor” (bloque `#register-manager-form`, aprox. líneas 894-936), los datos de la entidad están **hardcodeados** (Fademur, La Rioja, etc.). Ahí debe mostrarse la entidad actual: `$entity->name`, `$entity->province`, `$entity->address`, etc., y **añadir el logo de la entidad**. Si la entidad tiene imagen en `$entity->image`, mostrarla en el recuadro donde ahora hay un icono genérico, por ejemplo: `@if($entity->image) <img src="{{ asset('uploads/' . $entity->image) }}" alt="Logo entidad" style="width:100%;height:100%;object-fit:cover;" /> @else <i class="ri-account-circle-fill"></i> @endif`. La ruta (`uploads/` u otra) debe coincidir con donde se guardan las imágenes de entidad en `EntityController` (p. ej. `public_path('uploads')`).
- **Otros sitios:** Revisar vistas donde se muestran “datos de la entidad” (listados de entidades, selección de entidad en reservas/participaciones, etc.) y, si hay un bloque de “datos” o “resumen” de la entidad, añadir la imagen con `$entity->image` y la misma ruta base que en el controlador.

---

## 3. CIF de entidad (G48123987 y reglas para asociaciones/clubes) ✅

**Problema:** El CIF de la entidad no lo acepta. G48123987 es correcto para un club deportivo. Las reglas para CIF de entidades (asociaciones, clubes, congregaciones religiosas, etc.) son distintas: según tipo de entidad el carácter de control puede ser **número** o **letra**. Para tipo **G** (asociaciones, clubes) el estándar AEAT permite **número o letra** (híbrido). Correspondencia número → letra: 0=J, 1=A, 2=B, 3=C, 4=D, 5=E, 6=F, 7=G, 8=H, 9=I.

**Soluciones aplicables:**

- **Regla actual:** En `app/Rules/EntityDocument.php` el método `validateCif()` para tipos que no son A,B,E,H exige que el carácter de control sea **solo letra** (`$letters[$checkDigit]`). Por eso G48123987 (control “7”) falla.
- **Cambio recomendado:** Para la letra inicial **G** (asociaciones, clubes), aceptar tanto el dígito calculado como su letra equivalente. Tras calcular `$checkDigit` (0-9) y `$letters = 'JABCDEFGHI'`, hacer:
  - Si `$firstChar === 'G'`: validar `$control === (string) $checkDigit || $control === $letters[$checkDigit]`.
  - Opcional: para P, Q, R, S, N, W seguir exigiéndo solo letra; para G (y si se desea ser permisivo con otros híbridos) aceptar número o letra.
- Así G48123987 será válido (control “7” coincide con el dígito de control) y también lo será si alguien escribe la letra (ej. G4812398G).

---

## 4. Fecha límite: máximo día anterior al sorteo (23:59) ✅

**Problema:** La fecha límite debería ser como máximo el día anterior al sorteo (a las 23:59). El día del sorteo ya debería estar liquidado.

**Soluciones aplicables:**

- **Backend – Sorteos:** En `app/Http/Controllers/LotteryController.php` la validación actual es `before_or_equal:draw_date`, lo que permite que la fecha límite sea **el mismo día** del sorteo. Cambiarla a **estrictamente anterior** al día del sorteo. Opciones:
  - Regla personalizada: p. ej. `'deadline_date' => ['nullable', 'date', new \App\Rules\DeadlineBeforeDrawDate($request->draw_date)]` donde la regla compruebe `Carbon::parse($value)->endOfDay() < Carbon::parse($draw_date)->startOfDay()` (límite a 23:59 del día anterior).
  - O validación inline: `'deadline_date' => 'nullable|date|before:draw_date'` (en Laravel `before:draw_date` es estricto: deadline < draw_date). Ajustar mensaje de error a algo como: “La fecha límite debe ser como máximo el día anterior al sorteo (23:59)”.
- **Backend – Sets/Reservas:** En `app/Rules/DeadlineBeforeLottery.php` actualmente se usa `$deadlineDate->lte($lotteryDate)`, es decir permite el mismo día. Cambiar a que la fecha límite sea **estrictamente anterior** al día del sorteo, p. ej. `$deadlineDate->endOfDay()->lt(Carbon::parse($lotteryDate)->startOfDay())` o comparar solo fechas (sin hora) y exigir `deadline < draw_date`. Actualizar el mensaje de la regla en consecuencia.
- **Frontend:** En `resources/views/lottery/add.blade.php`, `lottery/edit.blade.php` y en vistas de sets (add_information, edit) donde se fija `max` en el input de fecha límite, asegurar que el `max` sea el **día anterior** a la fecha del sorteo (p. ej. en JS: si `draw_date` es D, entonces `deadline_date.max = D - 1 día`). Así se alinea con la regla de negocio “máximo 23:59 del día anterior”.

---

## 5. Entidad incorrecta al registrar gestor (Fademur en vez de Loterías sin fronteras) ✅

**Problema:** Si entráis a Entidades → Loterías sin fronteras → Gestores → Añadir → Registrar gestor, sale “Fademur” en vez de “Loterías sin fronteras”.

**Soluciones aplicables:**

- **Causa:** En `resources/views/entities/show.blade.php`, dentro del formulario “Registrar gestor” (`#register-manager-form`, aprox. líneas 922-936), el nombre de la entidad, provincia, dirección, etc. están **hardcodeados** (“FADEMUR”, “La Rioja”, “Avd. Club Deportivo 28”) en lugar de usar la entidad de la vista.
- **Solución:** Sustituir esos textos fijos por los datos reales de la entidad que se está viendo:
  - Nombre: `{{ $entity->name ?? 'Sin nombre' }}`
  - Provincia: `{{ $entity->province ?? 'Sin provincia' }}`
  - Dirección: `{{ $entity->address ?? 'No especificada' }}`
  - Ciudad: `{{ $entity->city ?? 'No especificada' }}`
  - Teléfono: `{{ $entity->phone ?? 'No especificado' }}`
- El formulario ya envía `entity_id` correctamente con `route('entities.register-manager', $entity->id)`. Solo hay que corregir la parte visual del recuadro de “datos de la entidad” para que muestre `$entity` y, si aplica, el logo (ver tarea 2).
