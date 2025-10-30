/**
 * ============================================================================
 * CHILECHOCADOS - ADMIN FEEDBACK VISUAL
 * ============================================================================
 * 
 * Sistema de feedback visual para acciones del administrador
 * Proporciona retroalimentación clara y mejora la experiencia de usuario
 * 
 * Características:
 * - Estados de carga en botones
 * - Confirmaciones visuales
 * - Toasts de notificación
 * - Animaciones de transición
 * 
 * @version 1.0
 * @date 2025-10-30
 * ============================================================================
 */

// ============================================================================
// SISTEMA DE TOASTS
// ============================================================================

const Toast = {
  container: null,
  
  init() {
    if (!this.container) {
      this.container = document.createElement('div');
      this.container.id = 'toast-container';
      this.container.style.cssText = `
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 10000;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 400px;
      `;
      document.body.appendChild(this.container);
    }
  },
  
  show(message, type = 'info', duration = 4000) {
    this.init();
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icons = {
      success: '<i data-lucide="check-circle" style="width:20px;height:20px"></i>',
      error: '<i data-lucide="x-circle" style="width:20px;height:20px"></i>',
      warning: '<i data-lucide="alert-triangle" style="width:20px;height:20px"></i>',
      info: '<i data-lucide="info" style="width:20px;height:20px"></i>'
    };
    
    toast.innerHTML = `
      <div class="toast-icon">${icons[type] || icons.info}</div>
      <div class="toast-message">${message}</div>
      <button class="toast-close" onclick="this.parentElement.remove()">
        <i data-lucide="x" style="width:16px;height:16px"></i>
      </button>
    `;
    
    this.container.appendChild(toast);
    
    // Inicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
    
    // Animación de entrada
    setTimeout(() => toast.classList.add('toast-show'), 10);
    
    // Auto-remover
    if (duration > 0) {
      setTimeout(() => {
        toast.classList.remove('toast-show');
        setTimeout(() => toast.remove(), 300);
      }, duration);
    }
    
    return toast;
  },
  
  success(message, duration) {
    return this.show(message, 'success', duration);
  },
  
  error(message, duration) {
    return this.show(message, 'error', duration);
  },
  
  warning(message, duration) {
    return this.show(message, 'warning', duration);
  },
  
  info(message, duration) {
    return this.show(message, 'info', duration);
  }
};

// ============================================================================
// ESTADOS DE CARGA EN BOTONES
// ============================================================================

const ButtonLoader = {
  /**
   * Activar estado de carga en un botón
   * @param {HTMLElement} button - Elemento del botón
   * @param {string} loadingText - Texto durante la carga
   */
  start(button, loadingText = 'Procesando...') {
    if (!button) return;
    
    // Guardar estado original
    button.dataset.originalText = button.innerHTML;
    button.dataset.originalDisabled = button.disabled;
    
    // Aplicar estado de carga
    button.disabled = true;
    button.classList.add('btn-loading');
    button.innerHTML = `
      <span class="spinner"></span>
      <span>${loadingText}</span>
    `;
  },
  
  /**
   * Desactivar estado de carga
   * @param {HTMLElement} button - Elemento del botón
   */
  stop(button) {
    if (!button) return;
    
    // Restaurar estado original
    button.innerHTML = button.dataset.originalText || button.innerHTML;
    button.disabled = button.dataset.originalDisabled === 'true';
    button.classList.remove('btn-loading');
    
    // Limpiar datos
    delete button.dataset.originalText;
    delete button.dataset.originalDisabled;
  },
  
  /**
   * Mostrar éxito temporal
   * @param {HTMLElement} button - Elemento del botón
   * @param {string} successText - Texto de éxito
   * @param {number} duration - Duración en ms
   */
  success(button, successText = '¡Listo!', duration = 2000) {
    if (!button) return;
    
    const originalText = button.dataset.originalText || button.innerHTML;
    
    button.classList.add('btn-success');
    button.innerHTML = `
      <i data-lucide="check" style="width:18px;height:18px"></i>
      <span>${successText}</span>
    `;
    
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
    
    setTimeout(() => {
      button.classList.remove('btn-success');
      button.innerHTML = originalText;
      button.disabled = false;
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
    }, duration);
  }
};

// ============================================================================
// CONFIRMACIONES VISUALES
// ============================================================================

const Confirm = {
  /**
   * Mostrar confirmación visual antes de acción destructiva
   * @param {Object} options - Opciones de configuración
   * @returns {Promise<boolean>}
   */
  show(options = {}) {
    const defaults = {
      title: '¿Estás seguro?',
      message: 'Esta acción no se puede deshacer',
      confirmText: 'Sí, continuar',
      cancelText: 'Cancelar',
      type: 'warning', // warning, danger, info
      onConfirm: null,
      onCancel: null
    };
    
    const config = { ...defaults, ...options };
    
    return new Promise((resolve) => {
      // Crear overlay
      const overlay = document.createElement('div');
      overlay.className = 'confirm-overlay';
      
      // Crear modal
      const modal = document.createElement('div');
      modal.className = `confirm-modal confirm-${config.type}`;
      
      const icons = {
        warning: '<i data-lucide="alert-triangle" style="width:48px;height:48px"></i>',
        danger: '<i data-lucide="alert-octagon" style="width:48px;height:48px"></i>',
        info: '<i data-lucide="info" style="width:48px;height:48px"></i>'
      };
      
      modal.innerHTML = `
        <div class="confirm-icon">${icons[config.type] || icons.warning}</div>
        <h3 class="confirm-title">${config.title}</h3>
        <p class="confirm-message">${config.message}</p>
        <div class="confirm-actions">
          <button class="btn btn-outline confirm-cancel">${config.cancelText}</button>
          <button class="btn btn-primary confirm-confirm">${config.confirmText}</button>
        </div>
      `;
      
      overlay.appendChild(modal);
      document.body.appendChild(overlay);
      
      // Inicializar iconos
      if (typeof lucide !== 'undefined') {
        lucide.createIcons();
      }
      
      // Animación de entrada
      setTimeout(() => {
        overlay.classList.add('confirm-show');
        modal.classList.add('confirm-show');
      }, 10);
      
      // Handlers
      const remove = () => {
        overlay.classList.remove('confirm-show');
        modal.classList.remove('confirm-show');
        setTimeout(() => overlay.remove(), 300);
      };
      
      modal.querySelector('.confirm-cancel').addEventListener('click', () => {
        remove();
        if (config.onCancel) config.onCancel();
        resolve(false);
      });
      
      modal.querySelector('.confirm-confirm').addEventListener('click', () => {
        remove();
        if (config.onConfirm) config.onConfirm();
        resolve(true);
      });
      
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          remove();
          resolve(false);
        }
      });
    });
  },
  
  /**
   * Confirmación para eliminar
   */
  delete(itemName = 'este elemento') {
    return this.show({
      title: '¿Eliminar?',
      message: `¿Estás seguro de que deseas eliminar ${itemName}? Esta acción no se puede deshacer.`,
      confirmText: 'Sí, eliminar',
      cancelText: 'Cancelar',
      type: 'danger'
    });
  },
  
  /**
   * Confirmación para aprobar
   */
  approve(itemName = 'esta publicación') {
    return this.show({
      title: '¿Aprobar?',
      message: `¿Deseas aprobar ${itemName}? Será visible públicamente.`,
      confirmText: 'Sí, aprobar',
      cancelText: 'Cancelar',
      type: 'info'
    });
  }
};

// ============================================================================
// ANIMACIONES DE TRANSICIÓN
// ============================================================================

const Animate = {
  /**
   * Fade out y remover elemento
   */
  fadeOut(element, callback) {
    element.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    element.style.opacity = '0';
    element.style.transform = 'translateX(20px)';
    
    setTimeout(() => {
      if (element.parentNode) {
        element.parentNode.removeChild(element);
      }
      if (callback) callback();
    }, 300);
  },
  
  /**
   * Highlight temporal
   */
  highlight(element, color = '#10B981') {
    const originalBg = element.style.backgroundColor;
    element.style.transition = 'background-color 0.3s ease';
    element.style.backgroundColor = color + '20'; // 20 = alpha
    
    setTimeout(() => {
      element.style.backgroundColor = originalBg;
    }, 1000);
  },
  
  /**
   * Shake para errores
   */
  shake(element) {
    element.classList.add('animate-shake');
    setTimeout(() => element.classList.remove('animate-shake'), 500);
  }
};

// ============================================================================
// EXPORTAR PARA USO GLOBAL
// ============================================================================

window.Toast = Toast;
window.ButtonLoader = ButtonLoader;
window.Confirm = Confirm;
window.Animate = Animate;

// ============================================================================
// ESTILOS CSS INYECTADOS
// ============================================================================

const styles = document.createElement('style');
styles.textContent = `
  /* Toasts */
  .toast {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    border-left: 4px solid;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
    min-width: 300px;
  }
  
  .toast-show {
    opacity: 1;
    transform: translateX(0);
  }
  
  .toast-success { border-color: var(--cc-success, #10B981); }
  .toast-error { border-color: var(--cc-danger, #EF4444); }
  .toast-warning { border-color: var(--cc-warning, #F59E0B); }
  .toast-info { border-color: var(--cc-info, #3B82F6); }
  
  .toast-icon { flex-shrink: 0; }
  .toast-success .toast-icon { color: var(--cc-success, #10B981); }
  .toast-error .toast-icon { color: var(--cc-danger, #EF4444); }
  .toast-warning .toast-icon { color: var(--cc-warning, #F59E0B); }
  .toast-info .toast-icon { color: var(--cc-info, #3B82F6); }
  
  .toast-message {
    flex: 1;
    font-size: 14px;
    line-height: 1.5;
    color: var(--cc-text-primary, #1A1A1A);
  }
  
  .toast-close {
    flex-shrink: 0;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    color: var(--cc-text-tertiary, #666);
    transition: color 0.2s;
  }
  
  .toast-close:hover {
    color: var(--cc-text-primary, #1A1A1A);
  }
  
  /* Button Loading */
  .btn-loading {
    position: relative;
    pointer-events: none;
  }
  
  .spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
  }
  
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  
  .btn-success {
    background-color: var(--cc-success, #10B981) !important;
    border-color: var(--cc-success, #10B981) !important;
  }
  
  /* Confirm Modal */
  .confirm-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    transition: background 0.3s ease;
    padding: 20px;
  }
  
  .confirm-overlay.confirm-show {
    background: rgba(0, 0, 0, 0.5);
  }
  
  .confirm-modal {
    background: white;
    border-radius: 16px;
    padding: 32px;
    max-width: 500px;
    width: 100%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
    transition: all 0.3s ease;
  }
  
  .confirm-modal.confirm-show {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
  
  .confirm-icon {
    margin-bottom: 16px;
  }
  
  .confirm-warning .confirm-icon { color: var(--cc-warning, #F59E0B); }
  .confirm-danger .confirm-icon { color: var(--cc-danger, #EF4444); }
  .confirm-info .confirm-icon { color: var(--cc-info, #3B82F6); }
  
  .confirm-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 12px;
    color: var(--cc-text-primary, #1A1A1A);
  }
  
  .confirm-message {
    font-size: 15px;
    color: var(--cc-text-secondary, #4A4A4A);
    margin-bottom: 24px;
    line-height: 1.6;
  }
  
  .confirm-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
  }
  
  /* Animations */
  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
  }
  
  .animate-shake {
    animation: shake 0.5s ease;
  }
`;

document.head.appendChild(styles);
