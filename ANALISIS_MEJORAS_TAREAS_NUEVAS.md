# Análisis y mejoras sugeridas – Tareas nuevas (Laravel)

Este documento analiza cada punto de `tareas nuevas.md` y propone mejoras concretas en el backend Laravel (y, donde aplique, en vistas o frontend del panel).

---

## 1. Imágenes en paso “Datos del gestor” (alta entidad)

**Problema:** Al pasar al paso 3 (Datos del gestor) no se muestran la imagen de la administración ni la de la entidad.

**Causa probable:** En `resources/views/entities/add_manager.blade.php` el wizard usa siempre iconos estáticos:
- Paso 1: `url('assets/admin.svg')`
- Paso 2: `url('assets/entidad.svg')`
- Paso 3: `url('assets/gestor.svg')`

Y en la tarjeta lateral se usa un icono genérico (`ri-account-circle-fill`) en lugar de las imágenes de administración y entidad.

**Mejoras sugeridas (Laravel):**

- En `EntityController::create_manager()`:
  - Asegurar que `selected_administration` tenga cargado el atributo `image` si existe en el modelo `Administration`.
  - Pasar a la vista la imagen de la entidad desde `entity_information['image']` (guardada en sesión en `store_information`).
- En la vista `entities/add_manager.blade.php`:
  - En los pasos 1 y 2 del wizard, si existen imágenes, usar `asset('uploads/' . $administration->image)` y `asset('uploads/' . $entityInformation['image'])` en lugar de (o además de) los SVG.
  - En la tarjeta lateral (resumen administración + entidad), mostrar:
    - Imagen de administración: `session('selected_administration')->image` → `asset('uploads/' . ...)` (o la ruta que use el modelo Administration).
    - Imagen de entidad: `session('entity_information')['image']` → `asset('uploads/' . ...)`.
  - Comprobar en `app/Models/Administration.php` si tiene `image` y dónde se guarda (tabla, disco) para usar la URL correcta.

---

## 2. Gestor principal: permisos y edición

**Problema:** No se pueden asignar permisos al dar de alta el gestor; no se puede editar el gestor principal; el principal debe poder quitarse solo si se asigna otro; al quitar de principal debe preguntar qué gestor será el nuevo principal.

**Mejoras sugeridas (Laravel):**

- **Modelo / BD:** Confirmar si existe un campo `is_main` o `principal` en la tabla de managers (o entidad/administración) para marcar el gestor principal. Si no existe, añadir migración (por ejemplo `managers.is_main` o en la entidad el `manager_id` “principal”).
- **ManagerController / políticas:**
  - Al editar un gestor que es el principal: no permitir quitar permisos (o mostrar mensaje fijo: “Es el gestor principal y tiene todos los permisos; no se pueden restringir”).
  - Endpoint o acción “quitar como principal”: validar que en la misma entidad/administración exista al menos otro gestor; si no, devolver error 422 con mensaje claro.
  - Endpoint o acción “asignar nuevo principal”: recibir el ID del nuevo gestor, validar que pertenezca a la misma entidad/administración, actualizar `is_main` (antiguo a false, nuevo a true) en una transacción.
- **Vistas / flujo:** El “quitar de principal” debería abrir un modal o paso que obligue a elegir el nuevo gestor principal; si no se elige, no ejecutar el cambio (el backend ya debe rechazar si no se envía el nuevo ID).

---

## 3. Imagen de fondo no carga

**Problema:** La imagen de fondo del diseño no carga.

**Áreas a revisar (Laravel + almacenamiento):**

- **DesignController / DesignFormat:** Ver cómo se guardan y sirven los `backgrounds` (por ejemplo en `design/saveFormat` y en las vistas que renderizan el diseño). Si la ruta se guarda relativa, al generar PDF o mostrar en otro dominio puede fallar.
- **Rutas y almacenamiento:** Comprobar que las URLs de las imágenes de fondo usen `asset()` o `Storage::url()` según corresponda, y que los archivos estén en `public/uploads` o en el disco configurado y con permisos correctos.
- **Vistas de diseño/PDF:** Revisar `resources/views/design/` (y las que inyectan HTML de participación/portada/trasera) y asegurar que las etiquetas `<img>` o `background-image` usen la URL absoluta (por ejemplo `config('app.url')` o `asset()`) para que DomPDF o el navegador las carguen.

---

## 4. Límites y borde al seleccionar/redimensionar elementos (textos)

**Problema:** Al seleccionar un elemento se deben ver los límites; al hacer pequeño o grande debe verse el borde (sobre todo en textos).

**Nota:** Esto es sobre todo lógica de frontend del editor de diseño (JavaScript/Canvas o HTML/CSS). En Laravel no suele estar esa lógica.

**Sugerencia:** Revisar el JS del editor (por ejemplo en `public/` o en las vistas de design) que maneja la selección y el redimensionado, y asegurar que:
- Los elementos seleccionados tengan una clase o atributo que dibuje un borde/outline (por ejemplo `box-sizing: border-box` y `outline` o `border`).
- Los textos tengan contenedor con dimensiones definidas para que el borde coincida con el área visible.

---

## 5. Zoom > 150% y barras de desplazamiento

**Problema:** El zoom hace la participación más grande hasta 150% y se sale del cuadrado; deberían aparecer barras de desplazamiento.

**Nota:** Es principalmente comportamiento del frontend del editor (contenedor con overflow y zoom).

**Sugerencia:** En el CSS/JS del editor de diseño:
- El contenedor que envuelve la participación debería tener `overflow: auto` (o `scroll`) y un tamaño fijo/máximo.
- El zoom debería aplicarse con `transform: scale()` (o similar) sobre el contenido interior, de modo que al superar el 100% aparezcan automáticamente scrollbars dentro de ese contenedor.

---

## 6. Configurar salida: botón “Siguiente” guarda pero no sale ni muestra PDFs

**Problema:** En editar → Configurar salida, al pulsar “Siguiente” dice que guarda pero no sale ni muestra los PDFs generados.

**Mejoras sugeridas (Laravel + frontend):**

- **Backend:** Revisar la ruta/acción que se llama al pulsar “Siguiente” en el paso de configurar salida (por ejemplo en `DesignController` o en rutas `design.*`). Debe:
  - Guardar correctamente la configuración de salida (por ejemplo en `DesignFormat` o en el JSON de `output`).
  - Devolver una respuesta que el frontend pueda usar para: (1) marcar el paso como completado y (2) redirigir o mostrar la pantalla de “PDFs generados” con enlaces de descarga.
- **Frontend:** Tras recibir éxito del guardado, redirigir a la vista de resumen/descarga de PDFs (o mostrar un modal con enlaces a `design/pdf/participation/{id}`, `design/pdf/cover/{id}`, etc.) en lugar de quedarse en la misma pantalla sin feedback.

---

## 7. Icono imprimir: se queda “pensando” y pantalla de error

**Problema:** Al dar al icono de imprimir cuando está generado, se queda cargando y luego sale pantalla de error.

**Mejoras sugeridas (Laravel):**

- Identificar la ruta que usa el botón de imprimir (por ejemplo si es `exportParticipationPdf`, `exportCoverPdf`, o una variante async).
- Revisar logs en `storage/logs/laravel.log` en el momento del fallo (timeout, memoria, excepción en DomPDF).
- Aumentar si hace falta `max_execution_time` y `memory_limit` solo para esas acciones de PDF (en el controlador ya hay 300 s y 1024M en algunos métodos).
- Si la generación es pesada, usar la variante asíncrona (`exportParticipationPdfAsync` + `checkPdfStatus` + `downloadPdf`) y en el frontend mostrar “Generando…” y luego enlace de descarga cuando el job termine.
- Asegurar que las rutas de PDF no requieran un body o método incorrecto (por ejemplo el botón debe hacer GET a la URL de descarga o POST si la ruta lo exige).

---

## 8. Participaciones: set aparece creado pero no se puede ver ni descargar el PDF

**Problema:** En Participaciones, al seleccionar la entidad sí sale que el set está creado (parece que el PDF se generó) pero no se puede ver ni descargar.

**Mejoras sugeridas (Laravel):**

- Comprobar que, al “finalizar” el diseño o configurar salida, se guarde correctamente el `design_format_id` o la relación set–design y que exista un registro en `design_formats` con el HTML necesario.
- En la vista de participaciones/sets por entidad, mostrar enlaces o botones “Ver PDF participación”, “Descargar portada”, etc., que apunten a:
  - `route('design.exportParticipationPdfAsync', $designFormatId)` o
  - `route('design.downloadPdf', $jobId)` si se usa generación asíncrona,
  y que el controlador devuelva el archivo o redirija a la descarga.
- Si el PDF se genera en un job, asegurar que la tabla o almacenamiento de “jobs” de PDF (por ejemplo `generated_pdfs` o el filesystem) sea accesible y que la ruta `design/pdf/download/{job_id}` encuentre el archivo y lo envíe con `response()->download()`.

---

## 9. Sets de participaciones digitales: imagen de la participación para asignar

**Problema:** En sets de participaciones digitales, se entiende que hay que generar también la imagen de la participación para que aparezcan y se puedan asignar.

**Mejoras sugeridas (Laravel):**

- Definir en el modelo `Set` o en la lógica de “participaciones digitales” si existe un campo (por ejemplo `participation_image` o `snapshot_path`) por participación o por set.
- Si las participaciones digitales se crean desde el mismo diseño que las físicas, reutilizar la generación de imagen (por ejemplo la que usa `saveSnapshot` en `DesignController` o la que genera previews) y guardar la URL o path en la participación o en el set.
- En los endpoints que listan participaciones/sets para asignar (por ejemplo en `ParticipationController` o `SetController` para la app/panel), incluir en la respuesta el campo de imagen (por ejemplo `snapshot_path` o `participation_image`) para que el frontend pueda mostrarlas.

---

## 10. Cantidad total de participaciones (por defecto 600)

**Problema:** No se coge la cantidad total de participaciones y parece que sale por defecto 600.

**Mejoras sugeridas (Laravel):**

- Revisar dónde se lee `total_participations` para el set en el flujo de diseño/PDF:
  - `DesignController::exportParticipationPdf` y `GenerateParticipationPdfJob` usan `$set->total_participations ?? 0`. Si el set no tiene valor, queda 0; comprobar si en otro sitio se usa un default 600.
- En el formulario o vista donde se crea/edita el set (por ejemplo `SetController`, vistas en `sets/` o `reserves/`), asegurar que el campo “cantidad total de participaciones” se envía y se valida y se persiste en `sets.total_participations` (por ejemplo con `required|integer|min:1`).
- Revisar si hay algún valor por defecto 600 en migraciones, seeders o en el modelo `Set` (`$attributes`) y alinearlo con la lógica de negocio (o quitarlo si debe ser siempre explícito).

---

## 11. Participaciones por entidad: no sale la imagen de la entidad

**Problema:** En la sección de participaciones, al elegir una entidad para ver los sets, no sale la imagen de la entidad.

**Mejoras sugeridas (Laravel):**

- En el controlador que devuelve la lista de sets por entidad (por ejemplo `SetController` o el que usa la vista de participaciones al filtrar por entidad), cargar la relación `entity` con el atributo `image`:
  - Por ejemplo: `Set::with('entity:id,name,image')->where(...)`.
- En la vista correspondiente (por ejemplo en `participations/index` o la que lista sets por entidad), mostrar la imagen con `asset('uploads/' . $entity->image)` cuando `$entity->image` exista, y un placeholder o icono cuando no.
- Comprobar que la entidad pasada a la vista sea la misma que la del set (por ejemplo `$set->entity` o la entidad seleccionada en el filtro) y que no se esté usando una variable sin relación cargada.

---

## Resumen de archivos a tocar (Laravel)

| Tarea | Archivos principales |
|-------|----------------------|
| 1. Imágenes en paso gestor | `EntityController.php`, `entities/add_manager.blade.php`, modelo `Administration` |
| 2. Gestor principal | `ManagerController.php`, migración/ modelo Manager o Entity, políticas, vistas de gestores |
| 3. Imagen de fondo | `DesignController.php`, vistas en `design/`, rutas de assets |
| 6. Siguiente en configurar salida | `DesignController.php`, rutas `design.*`, JS del editor |
| 7. Imprimir / PDF | `DesignController.php` (export*Pdf, async, download), logs, frontend que llama a la ruta |
| 8. Ver/descargar PDF desde participaciones | Controlador de participaciones/sets, vistas que listan sets, rutas de descarga PDF |
| 9. Imagen participaciones digitales | Modelo Set/Participation, `DesignController::saveSnapshot`, endpoints de listado |
| 10. Total participaciones | `SetController.php`, vistas de creación/edición de set, modelo Set |
| 11. Imagen entidad en participaciones | Controlador que lista sets por entidad, vista que muestra la lista |

Las tareas 4 y 5 (límites/borde al seleccionar y zoom con scroll) se resuelven en el frontend del editor de diseño (JS/CSS), no en Laravel.

Si quieres, el siguiente paso puede ser implementar una de estas mejoras en concreto (por ejemplo la 1 o la 11) y te indico los cambios exactos en código.
