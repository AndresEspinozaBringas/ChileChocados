<?php
/**
 * Componente: Icono de Notificaciones
 * 
 * Icono de campana/alerta estandarizado para usar en toda la aplicación
 * 
 * @param int $size Tamaño del icono en px (default: 24)
 * @param int $count Número de notificaciones (opcional)
 * @param string $class Clases CSS adicionales (opcional)
 * @param string $id ID del elemento (opcional)
 * @param string $onclick Función onclick (opcional)
 * @param string $ariaLabel Etiqueta ARIA (default: 'Notificaciones')
 */

// Parámetros con valores por defecto
$size = $size ?? 24;
$count = $count ?? 0;
$class = $class ?? '';
$id = $id ?? '';
$onclick = $onclick ?? '';
$ariaLabel = $ariaLabel ?? 'Notificaciones';

// Construir atributos
$idAttr = $id ? 'id="' . htmlspecialchars($id) . '"' : '';
$onclickAttr = $onclick ? 'onclick="' . htmlspecialchars($onclick) . '"' : '';
$classAttr = $class ? 'class="header-action-btn ' . htmlspecialchars($class) . '"' : 'class="header-action-btn"';
?>

<button <?php echo $classAttr; ?> <?php echo $idAttr; ?> <?php echo $onclickAttr; ?> aria-label="<?php echo htmlspecialchars($ariaLabel); ?>">
    <?php echo icon('bell', $size); ?>
    <?php if ($count > 0): ?>
        <span class="notification-badge"><?php echo $count; ?></span>
    <?php endif; ?>
</button>
