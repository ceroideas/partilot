# Configuración de Nginx para Laravel Storage

Si estás usando Nginx y tienes problemas con el acceso a archivos en `/storage`, necesitas configurar Nginx para servir estos archivos directamente.

## Configuración necesaria en Nginx

Agrega esta configuración en el bloque `server` de tu sitio en Nginx (generalmente en `/etc/nginx/sites-available/tu-sitio` o en Plesk):

```nginx
server {
    # ... otras configuraciones ...
    
    # Servir archivos estáticos de storage directamente
    location /storage {
        alias /ruta/completa/a/tu/proyecto/storage/app/public;
        
        # Intentar servir el archivo, si no existe devolver 404
        try_files $uri =404;
        
        # Headers de caché para imágenes
        expires 1y;
        add_header Cache-Control "public, immutable";
        
        # Desactivar logs para archivos estáticos (opcional)
        access_log off;
        
        # Tipos MIME comunes
        location ~* \.(png|jpg|jpeg|gif|ico|svg|webp)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
        }
        
        # Bloquear acceso a archivos sensibles
        location ~ /\. {
            deny all;
            access_log off;
            log_not_found off;
        }
    }
    
    # ... resto de configuración ...
    
    # Esta regla debe ir DESPUÉS de la regla de /storage
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

## Pasos a seguir:

1. **Encuentra la ruta completa a tu proyecto**: 
   ```bash
   pwd
   # O en Plesk, busca la ruta del dominio
   ```

2. **Edita la configuración de Nginx** (requiere acceso root o sudo):
   ```bash
   sudo nano /etc/nginx/sites-available/tu-sitio
   # O en Plesk: Panel > Dominios > tu-dominio > Configuración de Apache y Nginx
   ```

3. **Reemplaza `/ruta/completa/a/tu/proyecto`** con la ruta real de tu proyecto Laravel.

4. **Verifica la configuración**:
   ```bash
   sudo nginx -t
   ```

5. **Recarga Nginx**:
   ```bash
   sudo systemctl reload nginx
   # O en Plesk: simplemente guarda los cambios
   ```

## Verificar permisos

Asegúrate de que los permisos sean correctos:

```bash
# Permisos para storage
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# El usuario de Nginx (generalmente www-data o nginx) debe poder leer los archivos
# Verifica el usuario de Nginx:
ps aux | grep nginx

# Ajusta los permisos si es necesario:
sudo chown -R www-data:www-data storage
sudo chmod -R 755 storage
```

## Verificar el symlink

Asegúrate de que el symlink existe:

```bash
ls -la public/storage
# Debe mostrar algo como: storage -> /ruta/a/storage/app/public
```

Si no existe, créalo:

```bash
php artisan storage:link
```

## Nota importante

La configuración de `/storage` debe ir **ANTES** de la regla general que redirige todo a `index.php`, de lo contrario Laravel intentará procesar las peticiones de archivos estáticos.
