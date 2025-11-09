# 📋 Plan de Implementación - Fase 2: Sistema de Marca y Modelo

## Fecha: 2025-11-08
## Estado: PLANIFICACIÓN

---

## 🎯 Objetivo

Implementar un sistema de autocompletado para marca y modelo que:
1. Sugiera opciones desde `chileautos_marcas_modelos.json`
2. Permita ingresar valores personalizados
3. Requiera aprobación del admin para valores personalizados
4. Mantenga consistencia en la base de datos

---

## 🎨 Diseño UX/UI Propuesto

### Selector de Marca (Combobox con Autocompletado)

```
┌─────────────────────────────────────────────────────┐
│ Marca *                                              │
│ ┌─────────────────────────────────────────────────┐ │
│ │ Escribe o selecciona...                    [▼] │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ Sugerencias (al escribir):                          │
│ ┌─────────────────────────────────────────────────┐ │
│ │ ✓ Toyota (72 modelos)                           │ │
│ │ ✓ Chevrolet (54 modelos)                        │ │
│ │ ✓ Nissan (45 modelos)                           │ │
│ │ ...                                              │ │
│ │ ➕ Otra marca (especificar)                      │ │
│ └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘
```

### Selector de Modelo (Dependiente de Marca)

```
┌─────────────────────────────────────────────────────┐
│ Modelo *                                             │
│ ┌─────────────────────────────────────────────────┐ │
│ │ Escribe o selecciona...                    [▼] │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ Modelos de Toyota:                                   │
│ ┌─────────────────────────────────────────────────┐ │
│ │ ✓ Corolla                                       │ │
│ │ ✓ Yaris                                         │ │
│ │ ✓ RAV4                                          │ │
│ │ ...                                              │ │
│ │ ➕ Otro modelo (especificar)                     │ │
│ └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘
```

### Flujo con Marca/Modelo Personalizado

```
┌─────────────────────────────────────────────────────┐
│ ⚠️ Marca/Modelo Personalizado                       │
│                                                      │
│ Has ingresado una marca o modelo que no está en     │
│ nuestro catálogo. Un administrador revisará y       │
│ aprobará tu solicitud antes de publicar.            │
│                                                      │
│ Marca ingresada: BYD                                 │
│ Modelo ingresado: Seal                               │
│                                                      │
│ Tu publicación quedará como "Borrador" hasta que    │
│ sea aprobada.                                        │
│                                                      │
│ [Cambiar] [Continuar de todas formas]               │
└─────────────────────────────────────────────────────┘
```

---

## 🗄️ Cambios en Base de Datos

### Migración SQL

```sql
-- Agregar campos para marcas/modelos personalizados
ALTER TABLE publicaciones 
ADD COLUMN marca_personalizada TINYINT(1) DEFAULT 0 COMMENT 'Indica si la marca fue ingresada manualmente',
ADD COLUMN modelo_personalizado TINYINT(1) DEFAULT 0 COMMENT 'Indica si el modelo fue ingresado manualmente',
ADD COLUMN marca_original VARCHAR(100) NULL COMMENT 'Marca ingresada por usuario antes de aprobación',
ADD COLUMN modelo_original VARCHAR(100) NULL COMMENT 'Modelo ingresado por usuario antes de aprobación',
ADD COLUMN marca_modelo_aprobado TINYINT(1) DEFAULT 0 COMMENT 'Indica si admin aprobó marca/modelo personalizado',
ADD INDEX idx_marca_personalizada (marca_personalizada),
ADD INDEX idx_modelo_personalizado (modelo_personalizado),
ADD INDEX idx_marca_modelo_aprobado (marca_modelo_aprobado);

-- Tabla para tracking de marcas/modelos pendientes
CREATE TABLE IF NOT EXISTS marcas_modelos_pendientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT NOT NULL,
    marca_ingresada VARCHAR(100) NOT NULL,
    modelo_ingresado VARCHAR(100) NOT NULL,
    marca_sugerida VARCHAR(100) NULL COMMENT 'Marca sugerida por admin',
    modelo_sugerido VARCHAR(100) NULL COMMENT 'Modelo sugerido por admin',
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'modificado') DEFAULT 'pendiente',
    notas_admin TEXT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_revision TIMESTAMP NULL,
    admin_id INT NULL,
    FOREIGN KEY (publicacion_id) REFERENCES publicaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_estado (estado),
    INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 📁 Estructura de Archivos

### Nuevos Archivos a Crear

```
public/assets/js/
├── marca-modelo-selector.js       # Componente de autocompletado
└── marca-modelo-data.js           # Carga y caché del JSON

app/controllers/
└── MarcaModeloController.php      # API para marcas/modelos

app/models/
└── MarcaModelo.php                # Modelo para gestión

app/views/pages/admin/
└── marcas-modelos-pendientes.php  # Panel de aprobación

database/migrations/
└── add_marca_modelo_personalizado.sql  # Migración
```

### Archivos a Modificar

```
app/views/pages/publicaciones/publish.php
app/controllers/PublicacionController.php
app/controllers/AdminController.php
public/index.php (rutas)
```

---

## 🔧 Implementación Técnica

### 1. Componente JavaScript de Autocompletado

**Archivo:** `public/assets/js/marca-modelo-selector.js`

```javascript
class MarcaModeloSelector {
    constructor(marcaInputId, modeloInputId, jsonPath) {
        this.marcaInput = document.getElementById(marcaInputId);
        this.modeloInput = document.getElementById(modeloInputId);
        this.jsonPath = jsonPath;
        this.data = null;
        this.init();
    }

    async init() {
        await this.loadData();
        this.setupMarcaAutocomplete();
        this.setupModeloAutocomplete();
    }

    async loadData() {
        // Intentar cargar desde localStorage (caché)
        const cached = localStorage.getItem('marcas_modelos_data');
        const cacheTime = localStorage.getItem('marcas_modelos_cache_time');
        
        // Caché válido por 24 horas
        if (cached && cacheTime && (Date.now() - parseInt(cacheTime)) < 86400000) {
            this.data = JSON.parse(cached);
            return;
        }

        // Cargar desde JSON
        const response = await fetch(this.jsonPath);
        this.data = await response.json();
        
        // Guardar en caché
        localStorage.setItem('marcas_modelos_data', JSON.stringify(this.data));
        localStorage.setItem('marcas_modelos_cache_time', Date.now().toString());
    }

    setupMarcaAutocomplete() {
        // Crear datalist con marcas
        const datalist = document.createElement('datalist');
        datalist.id = 'marcas-list';
        
        this.data.marcas.forEach(marca => {
            const option = document.createElement('option');
            option.value = marca.nombre;
            option.textContent = `${marca.nombre} (${marca.cantidadModelos} modelos)`;
            datalist.appendChild(option);
        });
        
        // Agregar opción "Otra marca"
        const otraOption = document.createElement('option');
        otraOption.value = '__OTRA__';
        otraOption.textContent = '➕ Otra marca (especificar)';
        datalist.appendChild(otraOption);
        
        this.marcaInput.setAttribute('list', 'marcas-list');
        this.marcaInput.parentElement.appendChild(datalist);
        
        // Event listener para cambio de marca
        this.marcaInput.addEventListener('change', () => this.onMarcaChange());
    }

    setupModeloAutocomplete() {
        // Se configura dinámicamente al seleccionar marca
    }

    onMarcaChange() {
        const marcaSeleccionada = this.marcaInput.value;
        
        // Limpiar modelo
        this.modeloInput.value = '';
        
        // Buscar marca en datos
        const marca = this.data.marcas.find(m => m.nombre === marcaSeleccionada);
        
        if (marca) {
            // Marca encontrada: cargar modelos
            this.loadModelos(marca.modelos);
            this.modeloInput.disabled = false;
        } else if (marcaSeleccionada === '__OTRA__') {
            // Marca personalizada
            this.showMarcaPersonalizadaInput();
        } else {
            // Marca no encontrada: permitir ingreso libre
            this.modeloInput.disabled = false;
            this.showWarningMarcaPersonalizada();
        }
    }

    loadModelos(modelos) {
        // Crear/actualizar datalist de modelos
        let datalist = document.getElementById('modelos-list');
        if (!datalist) {
            datalist = document.createElement('datalist');
            datalist.id = 'modelos-list';
            this.modeloInput.parentElement.appendChild(datalist);
        }
        
        datalist.innerHTML = '';
        
        modelos.forEach(modelo => {
            const option = document.createElement('option');
            option.value = modelo.nombre;
            datalist.appendChild(option);
        });
        
        // Agregar opción "Otro modelo"
        const otroOption = document.createElement('option');
        otroOption.value = '__OTRO__';
        otroOption.textContent = '➕ Otro modelo (especificar)';
        datalist.appendChild(otroOption);
        
        this.modeloInput.setAttribute('list', 'modelos-list');
    }

    showMarcaPersonalizadaInput() {
        // Mostrar input para marca personalizada
        // TODO: Implementar UI
    }

    showWarningMarcaPersonalizada() {
        // Mostrar advertencia de marca personalizada
        // TODO: Implementar UI
    }
}

// Inicializar al cargar página
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('marca-input')) {
        new MarcaModeloSelector(
            'marca-input',
            'modelo-input',
            '/chileautos_marcas_modelos.json'
        );
    }
});
```

### 2. Controlador Backend

**Archivo:** `app/controllers/MarcaModeloController.php`

```php
<?php

namespace App\Controllers;

use App\Models\MarcaModelo;
use App\Helpers\Auth;

class MarcaModeloController
{
    private $marcaModeloModel;

    public function __construct()
    {
        $this->marcaModeloModel = new MarcaModelo();
    }

    /**
     * API: Buscar marcas
     * GET /api/marcas?q=toyota
     */
    public function buscarMarcas()
    {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        
        // Cargar JSON
        $json = file_get_contents(__DIR__ . '/../../chileautos_marcas_modelos.json');
        $data = json_decode($json, true);
        
        // Filtrar marcas
        $marcas = array_filter($data['marcas'], function($marca) use ($query) {
            return stripos($marca['nombre'], $query) !== false;
        });
        
        // Limitar a 10 resultados
        $marcas = array_slice($marcas, 0, 10);
        
        echo json_encode(['marcas' => array_values($marcas)]);
    }

    /**
     * API: Obtener modelos de una marca
     * GET /api/modelos?marca=Toyota
     */
    public function obtenerModelos()
    {
        header('Content-Type: application/json');
        
        $marca = $_GET['marca'] ?? '';
        
        if (empty($marca)) {
            echo json_encode(['error' => 'Marca requerida', 'modelos' => []]);
            return;
        }
        
        // Cargar JSON
        $json = file_get_contents(__DIR__ . '/../../chileautos_marcas_modelos.json');
        $data = json_decode($json, true);
        
        // Buscar marca
        $marcaData = null;
        foreach ($data['marcas'] as $m) {
            if (strcasecmp($m['nombre'], $marca) === 0) {
                $marcaData = $m;
                break;
            }
        }
        
        if ($marcaData) {
            echo json_encode(['modelos' => $marcaData['modelos']]);
        } else {
            echo json_encode(['error' => 'Marca no encontrada', 'modelos' => []]);
        }
    }

    /**
     * Panel de admin: Listar marcas/modelos pendientes
     * GET /admin/marcas-modelos-pendientes
     */
    public function listarPendientes()
    {
        Auth::requireAdmin();
        
        $pendientes = $this->marcaModeloModel->getPendientes();
        
        require_once __DIR__ . '/../views/pages/admin/marcas-modelos-pendientes.php';
    }

    /**
     * Aprobar marca/modelo personalizado
     * POST /admin/marcas-modelos/{id}/aprobar
     */
    public function aprobar($id)
    {
        Auth::requireAdmin();
        
        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token inválido';
            header('Location: ' . BASE_URL . '/admin/marcas-modelos-pendientes');
            exit;
        }
        
        $marcaSugerida = $_POST['marca_sugerida'] ?? null;
        $modeloSugerido = $_POST['modelo_sugerido'] ?? null;
        $notas = $_POST['notas'] ?? null;
        
        // Aprobar
        $this->marcaModeloModel->aprobar($id, $marcaSugerida, $modeloSugerido, $notas, $_SESSION['user_id']);
        
        $_SESSION['success'] = 'Marca/modelo aprobado exitosamente';
        header('Location: ' . BASE_URL . '/admin/marcas-modelos-pendientes');
        exit;
    }

    /**
     * Rechazar marca/modelo personalizado
     * POST /admin/marcas-modelos/{id}/rechazar
     */
    public function rechazar($id)
    {
        Auth::requireAdmin();
        
        // Validar CSRF
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token inválido';
            header('Location: ' . BASE_URL . '/admin/marcas-modelos-pendientes');
            exit;
        }
        
        $motivo = $_POST['motivo'] ?? 'No especificado';
        
        // Rechazar
        $this->marcaModeloModel->rechazar($id, $motivo, $_SESSION['user_id']);
        
        $_SESSION['success'] = 'Marca/modelo rechazado';
        header('Location: ' . BASE_URL . '/admin/marcas-modelos-pendientes');
        exit;
    }
}
```

### 3. Modelo

**Archivo:** `app/models/MarcaModelo.php`

```php
<?php

namespace App\Models;

use PDO;

class MarcaModelo extends Model
{
    protected $table = 'marcas_modelos_pendientes';

    /**
     * Obtener marcas/modelos pendientes de aprobación
     */
    public function getPendientes()
    {
        $sql = "SELECT 
                    mmp.*,
                    p.titulo as publicacion_titulo,
                    u.nombre as usuario_nombre,
                    u.email as usuario_email
                FROM {$this->table} mmp
                INNER JOIN publicaciones p ON mmp.publicacion_id = p.id
                INNER JOIN usuarios u ON p.usuario_id = u.id
                WHERE mmp.estado = 'pendiente'
                ORDER BY mmp.fecha_creacion ASC";
        
        return $this->query($sql);
    }

    /**
     * Aprobar marca/modelo
     */
    public function aprobar($id, $marcaSugerida, $modeloSugerido, $notas, $adminId)
    {
        // Obtener registro
        $registro = $this->find($id);
        if (!$registro) return false;

        // Actualizar publicación
        $publicacionModel = new Publicacion();
        $publicacionModel->update($registro->publicacion_id, [
            'marca' => $marcaSugerida ?? $registro->marca_ingresada,
            'modelo' => $modeloSugerido ?? $registro->modelo_ingresado,
            'marca_personalizada' => 1,
            'modelo_personalizado' => 1,
            'marca_modelo_aprobado' => 1,
            'estado' => 'pendiente' // Cambiar de borrador a pendiente
        ]);

        // Actualizar registro de aprobación
        return $this->update($id, [
            'estado' => $marcaSugerida || $modeloSugerido ? 'modificado' : 'aprobado',
            'marca_sugerida' => $marcaSugerida,
            'modelo_sugerido' => $modeloSugerido,
            'notas_admin' => $notas,
            'fecha_revision' => date('Y-m-d H:i:s'),
            'admin_id' => $adminId
        ]);
    }

    /**
     * Rechazar marca/modelo
     */
    public function rechazar($id, $motivo, $adminId)
    {
        // Obtener registro
        $registro = $this->find($id);
        if (!$registro) return false;

        // Actualizar publicación (mantener como borrador)
        $publicacionModel = new Publicacion();
        $publicacionModel->update($registro->publicacion_id, [
            'motivo_rechazo' => $motivo
        ]);

        // Actualizar registro
        return $this->update($id, [
            'estado' => 'rechazado',
            'notas_admin' => $motivo,
            'fecha_revision' => date('Y-m-d H:i:s'),
            'admin_id' => $adminId
        ]);
    }

    /**
     * Crear solicitud de marca/modelo personalizado
     */
    public function crearSolicitud($publicacionId, $marca, $modelo)
    {
        return $this->create([
            'publicacion_id' => $publicacionId,
            'marca_ingresada' => $marca,
            'modelo_ingresado' => $modelo,
            'estado' => 'pendiente'
        ]);
    }
}
```

---

## 📝 Tareas de Implementación

### Fase 2.1: Base de Datos y Backend (2 horas)
- [ ] Crear migración SQL
- [ ] Ejecutar migración en BD de desarrollo
- [ ] Crear modelo `MarcaModelo.php`
- [ ] Crear controlador `MarcaModeloController.php`
- [ ] Agregar rutas en `public/index.php`

### Fase 2.2: Frontend - Autocompletado (2 horas)
- [ ] Crear `marca-modelo-selector.js`
- [ ] Integrar en `publish.php`
- [ ] Reemplazar inputs de texto por combobox
- [ ] Implementar caché en localStorage
- [ ] Agregar estilos CSS

### Fase 2.3: Panel de Admin (1.5 horas)
- [ ] Crear vista `marcas-modelos-pendientes.php`
- [ ] Implementar tabla de pendientes
- [ ] Formulario de aprobación/rechazo
- [ ] Notificaciones al usuario

### Fase 2.4: Testing y Ajustes (1 hora)
- [ ] Probar flujo completo
- [ ] Validar dark mode
- [ ] Ajustar UX según feedback
- [ ] Documentar cambios

---

## 🎯 Criterios de Aceptación

### Funcionales
- ✅ Usuario puede seleccionar marca desde lista
- ✅ Modelos se cargan dinámicamente según marca
- ✅ Usuario puede ingresar marca/modelo personalizado
- ✅ Publicación con marca personalizada queda en borrador
- ✅ Admin recibe notificación de marca pendiente
- ✅ Admin puede aprobar/modificar/rechazar
- ✅ Usuario recibe notificación de decisión

### Técnicos
- ✅ Autocompletado funciona sin librerías externas
- ✅ Caché en localStorage reduce peticiones
- ✅ Validación backend de marcas/modelos
- ✅ Migración SQL sin errores
- ✅ Compatible con sistema actual

### UX
- ✅ Interfaz intuitiva y clara
- ✅ Feedback visual en cada paso
- ✅ Mensajes de error descriptivos
- ✅ Compatible con dark mode

---

## ⚠️ Riesgos y Mitigaciones

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| JSON muy grande ralentiza carga | Media | Bajo | Implementar caché en localStorage |
| Usuarios ingresan marcas incorrectas | Alta | Medio | Validación y sugerencias del admin |
| Conflictos con publicaciones existentes | Baja | Alto | Migración cuidadosa, no modificar datos existentes |
| Autocompletado no funciona en móvil | Media | Medio | Fallback a select nativo en móvil |

---

## 📅 Cronograma Estimado

**Inicio:** Después de aprobación de Fase 1  
**Duración:** 6-7 horas de desarrollo  
**Entrega:** 1 día hábil

### Día 1 (Mañana)
- Migración de BD
- Backend (modelo + controlador)
- Rutas

### Día 1 (Tarde)
- Frontend (JavaScript)
- Integración en publish.php
- Estilos CSS

### Día 2 (Mañana)
- Panel de admin
- Testing
- Ajustes finales

---

## 🤔 Decisiones Pendientes

1. **¿Permitir múltiples solicitudes de la misma marca/modelo?**
   - Opción A: Crear tabla de marcas/modelos aprobados para reutilizar
   - Opción B: Cada publicación requiere aprobación individual

2. **¿Notificar al usuario por email cuando se aprueba/rechaza?**
   - Requiere integración con sistema de emails

3. **¿Agregar marcas aprobadas al JSON automáticamente?**
   - Requiere proceso de actualización del JSON

---

**Preparado por:** Kiro AI  
**Fecha:** 2025-11-08  
**Versión:** 1.0  
**Estado:** 📋 PLANIFICACIÓN
