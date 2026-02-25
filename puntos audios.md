# Puntos audios – Seguimiento

## Notas de la reunión

- **Gestión de participaciones físicas y digitales: capas y elementos no editables**
  - Las participaciones físicas y digitales deben ser idénticas en diseño.
  - Propuesta de dos capas: una con elementos no removibles (código QR, código de referencia y número de participación) y otra con campos editables.
  - Para digitales: se utiliza solo la imagen de la participación sin la "matriz" y sin la capa del QR, referencia y número.
  - Conclusión: el sistema debe almacenar la imagen base en un "set digital" y mostrarla tanto en ventas físicas como digitales para identificación visual.

- **Visualización al escanear y en la "cartera"**
  - Al escanear una participación (QR), debe mostrarse la imagen y debajo los datos.
  - La imagen se usa para que el usuario reconozca visualmente su participación dentro de la cartera.
  - No se genera una imagen por cada participación digital; se referencia una imagen común cuando corresponda.

- **Reglas para eliminación y recreación de sets de participaciones**
  - Los sets físicos pueden eliminarse y recrearse solo si no hay nada vendido ni asignado.
  - Si hay ventas/asignaciones, no se permite eliminar, salvo que se reviertan completamente.
  - Si se elimina un set, se elimina su imagen y se sustituye por la nueva.
  - En digitales: si ya hay participaciones vendidas/asignadas, se puede cambiar la imagen y los datos incorrectos sin problema.

- **Digitalización previa al sorteo: propuesta de "almacén" informativo**
  - Riesgo: conflictos si alguien digitaliza y luego la entidad entrega la participación a otro.
  - La responsabilidad recae en quien digitaliza, pero se busca minimizar riesgos.
  - Propuesta: permitir almacenar participaciones en una sección "Almacén" antes del sorteo, solo a modo informativo.
  - Tras el sorteo, se informa de los premios del "Almacén"; para cobrar, es obligatorio volver a leer el QR.
  - Conclusión: no permitir digitalización plena en la "cartera" antes del sorteo; usar "Almacén" informativo y validar con QR para cobro.

- **Generación y previsualización de PDFs/imágenes (portada, participación, trasera)**
  - Crear 3 PDFs/hojas distintas: portada, imagen de participación y trasera.
  - Ofrecer previsualización en pantalla (o imagen/PDF temporal) antes de generar el PDF definitivo.
  - Al finalizar los 3 diseños, antes de exportar (configurar tacos/cantidades), mostrar las 3 imágenes para confirmación.
  - Pantalla final con resumen: cantidad de participaciones, configuración de tacos, numeración, etc., y botón de aceptación.
  - Solicitud de feedback sobre el diseño de la última pantalla.

---

## Próximos pasos – Estado y propuestas

### 1. Implementar la lógica de capas: bloquear QR, referencia y número; permitir edición de otros campos.
- **Estado:** PENDIENTE
- **Qué hay ahora:** En diseño (DesignController / vistas design) hay edición de portada, participación y trasera con editor HTML; no hay distinción explícita entre “capa fija” (QR, referencia, número) y “capa editable”.
- **Propuesta:**
  - En el modelo o en el editor de diseño (design format): marcar en el HTML o en metadatos los elementos que son “bloqueados” (por ejemplo con `data-locked="qr|referencia|numero"` o clases `.layer-fixed`).
  - En el front (JS del editor): al cargar el diseño, detectar esos elementos y deshabilitar su edición/arrastre/eliminación (por ejemplo con ContentEditable desactivado en esos nodos o con reglas en el editor WYSIWYG).
  - Alternativa: generar la capa fija (QR + referencia + número) por código en backend y fusionarla con la capa editable al generar el PDF, de modo que en el editor solo se edite la capa “variable”.

### 2. Almacenar y referenciar una imagen base para participaciones digitales en el "set digital".
- **Estado:** PENDIENTE
- **Qué hay ahora:** Set tiene `physical_participations` y `digital_participations`; DesignFormat y participaciones usan `design_format_id` y set; no hay un “set digital” separado ni un campo explícito “imagen base para digital” en el set.
- **Propuesta:**
  - Añadir en el modelo Set (o en DesignFormat si el diseño es por formato) un campo `digital_image_path` o `base_image_url` que almacene la imagen de la participación “sin matriz ni QR” para uso en app (cartera y escaneo).
  - Al generar/exportar el diseño de participación, guardar además una versión “solo imagen base” (sin QR/referencia/número) y asociarla al set o al design_format.
  - En API de participaciones/cartera: devolver esa URL de imagen base cuando la participación sea digital y corresponda al mismo set/formato, para mostrar la misma imagen en escaneo y en cartera.

### 3. Desarrollar la sección "Almacén" para digitalización informativa previa al sorteo.
- **Estado:** PENDIENTE
- **Qué hay ahora:** Existe flujo de “digitalizar participación” en la app; no hay una sección específica “Almacén” ni estado tipo “en almacén (informativo)”.
- **Propuesta:**
  - Nuevo estado en participaciones o nueva entidad “Almacén” (por ejemplo `participation_pre_draw_entries`): participaciones “digitalizadas” antes del sorteo solo informativas (sin pasar a cartera como cobrables).
  - En backend: endpoint para “añadir al almacén” (referencia, sorteo, etc.) que cree el registro informativo; no actualizar `collected_at` ni dar la participación como “en cartera” hasta que tras el sorteo se valide con lectura de QR.
  - En app: pantalla “Almacén” que liste esas entradas; tras el sorteo, notificación de premios en almacén y obligar a escanear QR para pasar a cobro/cartera.

### 4. Requerir lectura de QR para el proceso de cobro post-sorteo.
- **Estado:** PARCIAL / REVISAR
- **Qué hay ahora:** Hay escáner (escaner), venta por QR (venta-qr), cobrar-gestionar y flujo de participation_collections/cobro; el cobro puede estar ligado a lectura de referencia/QR.
- **Propuesta (si no está cerrado):**
  - Asegurar que el endpoint de “solicitar cobro” o “añadir a participation_collection” exija que la participación se haya identificado vía QR (o referencia escaneada) en esa misma sesión, y que no se permita cobro “solo por referencia manual” para participaciones que estaban en “Almacén” o que requieren validación post-sorteo.
  - En app: en cobrar-gestionar, obligar a abrir escáner y completar lectura antes de permitir “Solicitar cobro” para esa participación.

### 5. Implementar previsualización de las 3 imágenes (portada/participación/trasera) antes de exportar.
- **Estado:** PARCIAL
- **Qué hay ahora:** DesignController genera PDF portada y trasera (`cover_html`, `back_html`); hay `preview-design` y previsualizaciones en el editor de diseño; exportación de portada-trasera (y participación) existe.
- **Propuesta:**
  - Antes de lanzar la exportación definitiva (tacos/cantidades), añadir un paso intermedio que muestre las 3 previsualizaciones (portada, hoja de participación, trasera) en pantalla —por ejemplo 3 pestañas o 3 bloques con iframe/imagen generada desde los mismos HTML que se usarán para el PDF—.
  - Reutilizar las rutas o métodos que ya generan `portada.pdf` / `trasera.pdf` / participación para servir una vista previa (o imagen) en lugar de descarga, y enlazar esa pantalla desde el flujo “Exportar” o “Configurar tacos”.

### 6. Añadir pantalla final con resumen de exportación (tacos, cantidades, numeración) y confirmación.
- **Estado:** PENDIENTE
- **Qué hay ahora:** Hay configuración de cantidades por talonario y flujo de diseño; no hay una pantalla única de “resumen final” con todos los datos y botón de aceptación antes de generar el PDF definitivo.
- **Propuesta:**
  - Tras configurar tacos/cantidades y numeración, mostrar una vista “Resumen de exportación” con: cantidad total de participaciones, número de tacos, participaciones por taco, rango de numeración, previsualización de las 3 hojas (enlace al punto 5) y botón “Confirmar y generar PDF”.
  - Al confirmar, ejecutar la generación actual del PDF (portada-trasera y participación si aplica) y descarga o guardado.

### 7. Definir reglas de eliminación y recreación de sets según estado de ventas/asignaciones.
- **Estado:** PENDIENTE
- **Qué hay ahora:** `SetController::destroy` solo comprueba permisos (`canAccessEntity`); no comprueba si hay participaciones vendidas o asignadas antes de eliminar.
- **Propuesta:**
  - Antes de `$set->delete()` en `SetController::destroy`: comprobar si existe alguna participación del set con estado “vendida”/asignada (por ejemplo `Participation::where('set_id', $set->id)->whereIn('status', ['vendida', 'asignada'])->exists()` o el criterio que uséis).
  - Si hay ventas/asignaciones: no eliminar; devolver error claro (“No se puede eliminar el set: tiene participaciones vendidas o asignadas. Revierte las ventas o asignaciones antes de eliminarlo.”).
  - Si el set es “solo digital” y la regla de negocio permite cambiar imagen con ventas: aplicar lógica distinta (por ejemplo permitir eliminar/recambiar solo imagen o diseño sin borrar el set).
  - Opcional: en el listado de sets, mostrar indicador “Con ventas” y deshabilitar el botón Eliminar cuando no sea permitido.

---

## Clasificación: Nuevo vs corrección / mejora de comportamiento

| # | Punto | Tipo | Motivo breve |
|---|--------|------|------------------|
| 1 | Capas: bloquear QR, referencia y número | **Nuevo** | El editor no distingue hoy “elementos fijos” vs editables; se añade una regla de diseño nueva (capas bloqueadas), no se corrige un fallo. |
| 2 | Imagen base set digital | **Nuevo** | No existe hoy el concepto de “imagen base para digital”; es nueva información y nueva forma de mostrar la participación en app. |
| 3 | Sección Almacén | **Nuevo** | Flujo y pantalla nuevos (digitalización informativa previa al sorteo); no existía antes. |
| 4 | Cobro post-sorteo obligatorio con QR | **Corrección / mejora de comportamiento** | Si hoy se puede solicitar cobro sin haber leído el QR (p. ej. solo con referencia manual), se está cerrando un hueco: el proceso debe exigir lectura de QR para cobro. Refuerzo de reglas de negocio y consistencia. |
| 5 | Previsualización 3 imágenes antes de exportar | **Nuevo (mejora de flujo)** | No es que la exportación falle; se añade un paso de revisión antes de generar el PDF para evitar exportar sin confirmar. |
| 6 | Pantalla resumen exportación + confirmación | **Nuevo (mejora de flujo)** | Nueva pantalla y paso de confirmación explícita; evita generar PDF sin que el usuario revise cantidades/tacos/numeración. |
| 7 | Reglas eliminación sets (ventas/asignaciones) | **Corrección de bug / comportamiento peligroso** | Hoy se puede eliminar un set aunque tenga participaciones vendidas o asignadas; eso puede dejar datos incoherentes o referencias rotas. Se corrige un comportamiento incorrecto y peligroso. |

**Resumen:**
- **Nuevas funcionalidades (1, 2, 3):** capas bloqueadas, imagen base digital, Almacén.
- **Mejoras de flujo (5, 6):** previsualización y pantalla de resumen antes de exportar.
- **Correcciones / refuerzo de reglas (4, 7):** obligar QR para cobro y no permitir borrar sets con ventas/asignaciones.

---

## Resumen rápido

| # | Punto | Estado |
|---|--------|--------|
| 1 | Capas: bloquear QR, referencia y número | PENDIENTE |
| 2 | Imagen base set digital | PENDIENTE |
| 3 | Sección Almacén (digitalización informativa) | PENDIENTE |
| 4 | Cobro post-sorteo obligatorio con QR | PARCIAL / REVISAR |
| 5 | Previsualización 3 imágenes antes de exportar | PARCIAL |
| 6 | Pantalla resumen exportación + confirmación | PENDIENTE |
| 7 | Reglas eliminación sets (ventas/asignaciones) | PENDIENTE |

Ningún punto está marcado como **TERMINADO** porque, tras revisar el código, o bien no existe la funcionalidad descrita o está solo parcialmente cubierta. Cuando implementes cada uno, puedes cambiar su estado en este archivo a **TERMINADO** y añadir la fecha o commit de cierre.
