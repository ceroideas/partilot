<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prueba Lightbox2</title>
  <!-- CSS de Lightbox2 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />
  <style>
    .gallery {
      display: flex;
      gap: 16px;
      margin-top: 40px;
      justify-content: center;
    }
    .gallery img {
      width: 200px;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #eee;
      cursor: pointer;
      transition: box-shadow 0.2s;
    }
    .gallery img:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    body {
      font-family: Arial, sans-serif;
      background: #fafafa;
      padding: 40px;
    }
    h1 {
      text-align: center;
    }
  </style>
</head>
<body>
  <h1>Galería de prueba con Lightbox2</h1>
  <div class="gallery">
    <a href="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80" data-lightbox="product-gallery" data-title="Imagen de ejemplo 1">
      <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80" alt="Ejemplo 1">
    </a>
    <a href="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=800&q=80" data-lightbox="product-gallery" data-title="Imagen de ejemplo 2">
      <img src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80" alt="Ejemplo 2">
    </a>
    <a href="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=800&q=80" data-lightbox="product-gallery" data-title="Imagen de ejemplo 3">
      <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80" alt="Ejemplo 3">
    </a>
  </div>

  <!-- JS de Lightbox2 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
</body>
</html> 