# Correcciones pestaña Resultados/Sorteos (Ionic)

Aplica estos cambios en tu proyecto **Ionic** (por ejemplo `C:\Users\Jorge\proyectos\sipart`).

## 1. Error NG5002: carácter "ñ" en el template

El parser de Angular **no admite la letra "ñ"** en expresiones. Hay que usar una propiedad sin ñ.

### En `src/app/resultados/resultados.page.html`

**Busca** (línea ~85):
```html
<span class="sorteo-text">Sorteo: {{ resultado.nombreCompleto || resultado.numero + '/' + resultado.año }}</span>
```

**Sustituye por:**
```html
<span class="sorteo-text">Sorteo: {{ resultado.nombreCompleto || (resultado.numero + '/' + resultado.anio) }}</span>
```

(Usa `anio` en lugar de `año` y agrupa la concatenación entre paréntesis para evitar ambigüedades.)

---

## 2. Error TS2304: Cannot find name 'reintegro'

En el `.ts` se devuelve `reintegro` pero esa variable ya no existe; ahora se usa `extracciones`.

### En `src/app/resultados/resultados.page.ts`

**Busca** en el `return` del map (donde se construye el objeto de cada sorteo) la línea:
```ts
reintegro: reintegro,
```

**Elimínala** por completo (o sustitúyela por):
```ts
reintegro: extracciones.length ? extracciones.join('-') : null,
```

Y en el mismo objeto, donde pone **`año:`**, cámbialo a **`anio:`** para que coincida con el HTML:
```ts
anio: nombreCompleto.split('/')[1] || new Date(lottery.draw_date).getFullYear().toString().slice(-2) || '',
```

---

## 3. 401 Unauthorized y CORS

La API de resultados debe llamarse con el prefijo **/api**. La URL correcta es:

`http://127.0.0.1:8000/api/lottery/results`

### En `src/app/core/services/lottery.service.ts`

Asegúrate de que la petición use `apiUrl` (que en `environment` suele ser `http://127.0.0.1:8000/api`):

```ts
getResults(): Observable<any[]> {
  return this.http.get<any[]>(`${this.apiUrl}/lottery/results`).pipe(
  // ...
```

**No** uses algo como `this.apiUrl.replace('/api', '')` ni una URL sin `/api`.

### En el backend (Laravel)

En `routes/api.php` la ruta pública ya está definida así (fuera del grupo `auth.api`):

```php
Route::get('/lottery/results', [LotteryController::class, 'apiGetAllResults']);
```

Con el prefijo de API de Laravel, la URL final es: `http://127.0.0.1:8000/api/lottery/results`.

Si aun así ves CORS o 401:
- Comprueba que `config/cors.php` tenga `'paths' => ['api/*', ...]` y `'allowed_origins' => ['*']` (o incluye `http://localhost:8100`).
- Prueba en el navegador: `http://127.0.0.1:8000/api/lottery/results` y verifica que devuelve JSON sin pedir login.

---

## Resumen de cambios

| Archivo | Qué hacer |
|---------|-----------|
| `resultados.page.html` | `resultado.año` → `resultado.anio` y agrupar expresión del sorteo |
| `resultados.page.ts` | Propiedad `año` → `anio`; quitar o reemplazar `reintegro: reintegro` |
| `lottery.service.ts` | Usar `${this.apiUrl}/lottery/results` (con /api en la base) |
