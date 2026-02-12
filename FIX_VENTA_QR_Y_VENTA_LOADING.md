# Correcciones venta-qr y venta (LoadingController → spinner)

Aplica estos cambios en tu **proyecto Ionic** (donde está `src/app/venta-qr/` y `src/app/venta/`).

---

## 1. `src/app/venta-qr/venta-qr.page.ts`

### a) Añadir la propiedad `loading` en la clase

Busca la línea del **constructor** y, **justo antes** del `constructor(`, añade:

```ts
  loading = false;

  constructor(
```

(Si ya tienes otras propiedades antes del constructor, añade `loading = false;` junto a ellas.)

### b) Quitar `LoadingController` del constructor

**Antes:**
```ts
    private loadingController: LoadingController,
```

**Después:** elimina por completo esa línea (incluida la coma). Si queda una coma huérfana antes, quítala también.

Ejemplo de constructor corregido:

```ts
  constructor(
    private router: Router,
    private ventasService: VentasService,
    private alertController: AlertController
  ) {}
```

### c) Comprobar el import

Al inicio del archivo debe **no** aparecer `LoadingController`. Si sigue:

```ts
import { AlertController, LoadingController } from '@ionic/angular';
```

cámbialo a:

```ts
import { AlertController } from '@ionic/angular';
```

---

## 2. `src/app/venta/venta.page.ts` (línea ~127)

Sustituye la llamada al loader por la propiedad:

**Antes:**
```ts
        await loading.dismiss();
```

**Después:**
```ts
        this.loading = false;
```

(Asegúrate de que en la clase `VentaPage` tengas la propiedad `loading = false;` y que en ese método uses `this.loading = true` al inicio y `this.loading = false` en éxito/error en lugar de crear/presentar/cerrar un loader.)

---

## Resumen

| Archivo | Qué hacer |
|--------|-----------|
| **venta-qr.page.ts** | 1) Añadir `loading = false;` en la clase. 2) Quitar el parámetro `private loadingController: LoadingController` del constructor. 3) Quitar `LoadingController` del import de `@ionic/angular`. |
| **venta.page.ts** | Cambiar `await loading.dismiss();` por `this.loading = false;` (y que el resto del flujo use ya `this.loading`). |

Con esto deberían desaparecer los errores NG2003, TS2304, TS2339 y TS2663 en estos dos archivos.
