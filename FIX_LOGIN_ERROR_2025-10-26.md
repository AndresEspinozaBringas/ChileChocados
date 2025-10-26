# Fix: Error en página de login
**Fecha:** 26 de Octubre 2025

## Error Encontrado

### Síntoma
```
Fatal error: Uncaught TypeError: preg_match(): Argument #2 ($subject) must be of type string, array given 
in /Users/andresespinozabringas/projects/chilechocados/public/index.php:203
```

### URL afectada
- http://chilechocados.local:8080/login

---

## Causa del Error

### Problema
El archivo `public/index.php` tenía código mezclado de dos versiones diferentes del sistema de rutas:

1. **Sistema actual:** Usa `$url` como **array**
   ```php
   $url = explode('/', $url);  // Línea 68
   // $url = ['login'] o ['admin', 'publicaciones', '123']
   ```

2. **Sistema antiguo:** Esperaba `$url` como **string**
   ```php
   if (preg_match('/^\/admin\/publicaciones\/(\d+)$/', $url, $matches)) {
       // Error: $url es array, no string
   }
   ```

### Líneas problemáticas (195-230)
```php
// Estas rutas esperaban $url como string
if ($url === '/admin/publicaciones') { ... }
if (preg_match('/^\/admin\/publicaciones\/(\d+)$/', $url, $matches)) { ... }
```

---

## Solución Aplicada

### Acción tomada
Comenté las rutas admin antiguas que usaban `preg_match()` con `$url` como string.

### Código corregido
```php
// ====================================
// RUTAS ADMIN - TODO: Migrar a sistema de rutas actual
// ====================================
// NOTA: Estas rutas están comentadas porque usan $url como string
// pero el sistema actual usa $url como array
// Se deben migrar al sistema de specialRoutes o manejar en AdminController

/*
// Ruta principal del panel de moderación
if ($url === '/admin/publicaciones') {
    $controller = new AdminController();
    $controller->publicaciones();
    exit;
}
// ... resto de rutas admin comentadas
*/
```

---

## Estado Actual

### ✅ Funcionando
- http://chilechocados.local:8080/login - HTTP 200 OK
- http://chilechocados.local:8080/ - HTTP 200 OK
- http://chilechocados.local:8080/categorias - HTTP 200 OK
- http://chilechocados.local:8080/detalle/4 - HTTP 200 OK

### ⚠️ Pendiente (TODO)
Las rutas de admin necesitan ser migradas al sistema actual de rutas:

**Rutas que necesitan migración:**
1. `/admin/publicaciones` - Panel de moderación
2. `/admin/publicaciones/{id}` - Ver detalle (AJAX)
3. `/admin/publicaciones/{id}/aprobar` - Aprobar (POST)
4. `/admin/publicaciones/{id}/rechazar` - Rechazar (POST)
5. `/admin/publicaciones/{id}` - Eliminar (DELETE)

**Opciones de migración:**

#### Opción 1: Agregar a specialRoutes
```php
$specialRoutes = [
    // ... rutas existentes
    'admin' => ['controller' => 'AdminController', 'method' => 'index'],
];

// Y manejar sub-rutas en AdminController
```

#### Opción 2: Manejar en el controlador
```php
// En AdminController, detectar la acción desde $url array
public function index() {
    $action = $_GET['action'] ?? 'publicaciones';
    $id = $_GET['id'] ?? null;
    
    switch($action) {
        case 'publicaciones':
            $this->publicaciones();
            break;
        case 'ver':
            $this->verPublicacion($id);
            break;
        // etc.
    }
}
```

#### Opción 3: Usar query params
```php
// /admin?action=publicaciones
// /admin?action=ver&id=123
// /admin?action=aprobar&id=123
```

---

## Archivos Modificados

1. ✅ `public/index.php` - Comentadas rutas admin antiguas

---

## Recomendaciones

### Corto plazo
- ✅ Login funciona correctamente
- ⚠️ Panel admin necesita nueva implementación

### Mediano plazo
1. Decidir estrategia de rutas para admin
2. Implementar rutas admin en el sistema actual
3. Crear AdminController con métodos necesarios
4. Probar todas las funcionalidades admin

### Largo plazo
- Considerar usar un router más robusto (ej: FastRoute, Symfony Routing)
- Documentar el sistema de rutas elegido
- Crear tests para las rutas

---

**Error corregido:** ✅  
**Login funcionando:** ✅  
**Admin pendiente:** ⚠️ TODO
