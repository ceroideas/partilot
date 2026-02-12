# Correcciones para eliminar LoadingController y usar spinner

Aplica estos cambios en tu **proyecto Ionic/Angular** (donde ejecutas `ionic serve` o `ng serve`).

---

## 1. `src/app/venta-qr/venta-qr.page.ts`

- **Quitar** `LoadingController` del import de `@ionic/angular` y del constructor.
- **Añadir** propiedad en la clase: `loading = false;`
- **Sustituir** cualquier `await loading.present()` / `await loading.dismiss()` por `this.loading = true` y `this.loading = false`.

Ejemplo de import (debe quedar sin LoadingController):
```ts
import { AlertController } from '@ionic/angular';
```

Constructor sin LoadingController y clase con:
```ts
loading = false;
// ...
constructor(
  // ...
  private alertController: AlertController
) {}
```

En el método que hace la petición:
```ts
this.loading = true;
// ... petición ...
// en next y error:
this.loading = false;
```

---

## 2. `src/app/venta/venta.page.ts` (línea ~127)

- **Cambiar** `await loading.dismiss();` por `this.loading = false;` (en el callback donde salga ese código).

Busca en el archivo `loading.dismiss` y reemplaza por `this.loading = false`.

---

## 3. `src/app/digitalizar-participacion/digitalizar-participacion.page.ts`

- **Quitar** `LoadingController` del import y del constructor.
- **Añadir** en la clase: `loading = false;`
- **Sustituir** `await loading.dismiss()` por `this.loading = false` (línea ~97 y en todos los callbacks donde se use).

---

## 4. `src/app/escaner/escaner.page.ts`

- **Quitar** `LoadingController` del import y del constructor.
- **Añadir** en la clase: `loading = false;` y `loadingMessage = '';`
- **Sustituir** cualquier `await loading.dismiss()` por `this.loading = false` (p. ej. línea ~191).

---

## 5. `src/app/login/login.page.ts`

- **Quitar** `LoadingController` del import y del constructor.
- **Añadir** en la clase: `loading = false;`
- El resto del código que ya use `this.loading = true` / `this.loading = false` debe quedar igual.

---

## 6. `src/app/venta-manual/venta-manual.page.ts` (línea ~210)

- **Quitar** `LoadingController` del import y del constructor.
- **Añadir** en la clase: `loading = false;`
- **Sustituir** el bloque que usa `this.loadingController.create()` por:
  - Antes de la petición: `this.loading = true;`
  - En éxito y error: `this.loading = false;`
- En el HTML, mostrar un bloque con `*ngIf="loading"` y `<ion-spinner>` (igual que en otras páginas).

---

## Sobre el error NG6001 (“is not a directive, a component, or a pipe”)

Ese error suele aparecer cuando hay **errores en la clase** (constructor con dependencias que no existen, propiedades que no están declaradas, etc.). Cuando corrijas:

- Eliminar `LoadingController` del constructor en todas las páginas.
- Añadir las propiedades `loading` (y `loadingMessage` donde corresponda).

la compilación debería reconocer de nuevo las clases como componentes y el NG6001 debería desaparecer.

---

## Resumen por archivo

| Archivo | Quitar | Añadir en clase | Reemplazar en código |
|--------|--------|------------------|------------------------|
| venta-qr.page.ts | LoadingController (import + constructor) | `loading = false` | present/dismiss → this.loading true/false |
| venta.page.ts | — | — | `await loading.dismiss()` → `this.loading = false` |
| digitalizar-participacion.page.ts | LoadingController (import + constructor) | `loading = false` | `await loading.dismiss()` → `this.loading = false` |
| escaner.page.ts | LoadingController (import + constructor) | `loading = false`, `loadingMessage = ''` | `await loading.dismiss()` → `this.loading = false` |
| login.page.ts | LoadingController (import + constructor) | `loading = false` | — (si ya usas this.loading) |
| venta-manual.page.ts | LoadingController (import + constructor) | `loading = false` | loadingController.create/present/dismiss → this.loading true/false |

Si indicas la ruta de tu proyecto Ionic (por ejemplo `C:\ruta\a\tu-app-ionic`) o pegas aquí el contenido de uno de los `.page.ts` que falle, puedo devolverte el archivo ya corregido línea a línea.
