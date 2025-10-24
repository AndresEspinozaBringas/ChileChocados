<?php
/**
 * ============================================================================
 * CHILECHOCADOS - ICON HELPER
 * ============================================================================
 * 
 * Helper Class para Lucide Icons
 * Version: 1.0
 * Fecha: Octubre 2025
 * 
 * Uso:
 * Icon::render('search', 24, 'icon-class');
 * icon('user'); // función helper
 * 
 * Requiere: Lucide Icons CDN en header
 * <script src="https://unpkg.com/lucide@latest"></script>
 * 
 * ============================================================================
 */

class Icon {
    
    /**
     * Iconos disponibles con sus nombres en Lucide
     */
    private static $icons = [
        // Navegación
        'home' => 'home',
        'menu' => 'menu',
        'close' => 'x',
        'chevron-down' => 'chevron-down',
        'chevron-up' => 'chevron-up',
        'chevron-left' => 'chevron-left',
        'chevron-right' => 'chevron-right',
        'arrow-left' => 'arrow-left',
        'arrow-right' => 'arrow-right',
        
        // Usuario
        'user' => 'user',
        'user-circle' => 'user-circle',
        'users' => 'users',
        'login' => 'log-in',
        'logout' => 'log-out',
        'settings' => 'settings',
        'profile' => 'user',
        
        // Acciones
        'search' => 'search',
        'filter' => 'filter',
        'edit' => 'edit',
        'trash' => 'trash-2',
        'plus' => 'plus',
        'check' => 'check',
        'save' => 'save',
        'download' => 'download',
        'upload' => 'upload',
        'share' => 'share-2',
        
        // Información
        'info' => 'info',
        'alert' => 'alert-circle',
        'warning' => 'alert-triangle',
        'error' => 'x-circle',
        'success' => 'check-circle',
        'help' => 'help-circle',
        
        // Comunicación
        'mail' => 'mail',
        'message' => 'message-circle',
        'chat' => 'message-square',
        'phone' => 'phone',
        'bell' => 'bell',
        
        // Vehículos y Categorías
        'car' => 'car',
        'truck' => 'truck',
        'bike' => 'bike',
        'bus' => 'bus',
        'plane' => 'plane',
        'boat' => 'ship',
        'motorcycle' => 'bike',
        'rv' => 'caravan',
        
        // UI
        'eye' => 'eye',
        'eye-off' => 'eye-off',
        'heart' => 'heart',
        'star' => 'star',
        'bookmark' => 'bookmark',
        'calendar' => 'calendar',
        'clock' => 'clock',
        'map' => 'map',
        'pin' => 'map-pin',
        'tag' => 'tag',
        'dollar' => 'dollar-sign',
        'trending-up' => 'trending-up',
        'trending-down' => 'trending-down',
        
        // Archivos
        'file' => 'file',
        'folder' => 'folder',
        'image' => 'image',
        'camera' => 'camera',
        'paperclip' => 'paperclip',
        
        // Social
        'facebook' => 'facebook',
        'instagram' => 'instagram',
        'twitter' => 'twitter',
        'youtube' => 'youtube',
        'linkedin' => 'linkedin',
        
        // Otros
        'refresh' => 'refresh-cw',
        'external' => 'external-link',
        'link' => 'link',
        'copy' => 'copy',
        'more' => 'more-horizontal',
        'grid' => 'grid',
        'list' => 'list',
        'shield' => 'shield',
        'lock' => 'lock',
        'unlock' => 'unlock',
    ];
    
    /**
     * Renderizar un icono de Lucide
     * 
     * @param string $name Nombre del icono
     * @param int $size Tamaño del icono en px (default: 20)
     * @param string $class Clases CSS adicionales
     * @param array $attrs Atributos HTML adicionales
     * @return string HTML del icono
     */
    public static function render($name, $size = 20, $class = '', $attrs = []) {
        // Obtener nombre de Lucide
        $lucideName = self::$icons[$name] ?? $name;
        
        // Construir clases
        $classes = ['icon'];
        if ($class) {
            $classes[] = $class;
        }
        $classStr = implode(' ', $classes);
        
        // Construir atributos
        $attrStr = '';
        foreach ($attrs as $key => $value) {
            $attrStr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }
        
        // Generar HTML
        return sprintf(
            '<i data-lucide="%s" class="%s" style="width:%dpx;height:%dpx"%s></i>',
            htmlspecialchars($lucideName),
            htmlspecialchars($classStr),
            (int)$size,
            (int)$size,
            $attrStr
        );
    }
    
    /**
     * Verificar si un icono existe
     * 
     * @param string $name Nombre del icono
     * @return bool
     */
    public static function exists($name) {
        return isset(self::$icons[$name]);
    }
    
    /**
     * Obtener todos los iconos disponibles
     * 
     * @return array
     */
    public static function getAll() {
        return array_keys(self::$icons);
    }
    
    /**
     * Obtener iconos por categoría
     * 
     * @param string $category Categoría (navegacion, usuario, acciones, etc.)
     * @return array
     */
    public static function getByCategory($category) {
        $categories = [
            'navegacion' => ['home', 'menu', 'close', 'chevron-down', 'chevron-up', 'chevron-left', 'chevron-right', 'arrow-left', 'arrow-right'],
            'usuario' => ['user', 'user-circle', 'users', 'login', 'logout', 'settings', 'profile'],
            'acciones' => ['search', 'filter', 'edit', 'trash', 'plus', 'check', 'save', 'download', 'upload', 'share'],
            'informacion' => ['info', 'alert', 'warning', 'error', 'success', 'help'],
            'comunicacion' => ['mail', 'message', 'chat', 'phone', 'bell'],
            'vehiculos' => ['car', 'truck', 'bike', 'bus', 'plane', 'boat', 'motorcycle', 'rv'],
            'ui' => ['eye', 'eye-off', 'heart', 'star', 'bookmark', 'calendar', 'clock', 'map', 'pin', 'tag', 'dollar'],
            'archivos' => ['file', 'folder', 'image', 'camera', 'paperclip'],
            'social' => ['facebook', 'instagram', 'twitter', 'youtube', 'linkedin'],
        ];
        
        return $categories[$category] ?? [];
    }
}


/**
 * Función helper global para renderizar iconos rápidamente
 * 
 * @param string $name Nombre del icono
 * @param int $size Tamaño del icono (default: 20)
 * @param string $class Clases CSS adicionales
 * @return string HTML del icono
 */
function icon($name, $size = 20, $class = '') {
    return Icon::render($name, $size, $class);
}


/**
 * ============================================================================
 * EJEMPLOS DE USO
 * ============================================================================
 * 
 * // En tus vistas PHP:
 * 
 * // Uso básico
 * <?php echo Icon::render('search'); ?>
 * 
 * // Con tamaño personalizado
 * <?php echo Icon::render('user', 32); ?>
 * 
 * // Con clase CSS
 * <?php echo Icon::render('heart', 24, 'text-red'); ?>
 * 
 * // Con función helper
 * <?php echo icon('menu'); ?>
 * <?php echo icon('car', 28, 'icon-primary'); ?>
 * 
 * // Con atributos adicionales
 * <?php echo Icon::render('bell', 20, 'notification-icon', ['data-count' => '5']); ?>
 * 
 * // Verificar si existe
 * <?php if (Icon::exists('search')) { ?>
 *     <?php echo icon('search'); ?>
 * <?php } ?>
 * 
 * // Listar todos los iconos disponibles
 * <?php $icons = Icon::getAll(); ?>
 * <?php foreach ($icons as $iconName): ?>
 *     <div><?php echo icon($iconName); ?> <?php echo $iconName; ?></div>
 * <?php endforeach; ?>
 * 
 * // Obtener iconos por categoría
 * <?php $vehiculos = Icon::getByCategory('vehiculos'); ?>
 * <?php foreach ($vehiculos as $iconName): ?>
 *     <?php echo icon($iconName, 32); ?>
 * <?php endforeach; ?>
 * 
 * ============================================================================
 */
