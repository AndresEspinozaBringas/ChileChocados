/**
 * Sistema de Notificaciones en Tiempo Real para Admin
 * Actualiza badges y muestra notificaciones sin recargar
 * 
 * @version 1.0
 * @date 2025-10-30
 */

const AdminNotifications = {
  interval: null,
  updateFrequency: 30000, // 30 segundos
  soundEnabled: true,
  desktopEnabled: false,
  lastCounts: {},
  
  /**
   * Inicializar sistema de notificaciones
   */
  init() {
    console.log('🔔 Sistema de notificaciones iniciado');
    
    // Solicitar permiso para notificaciones desktop
    this.requestDesktopPermission();
    
    // Primera actualización inmediata
    this.update();
    
    // Actualizar cada 30 segundos
    this.interval = setInterval(() => {
      this.update();
    }, this.updateFrequency);
    
    // Limpiar al salir
    window.addEventListener('beforeunload', () => {
      this.stop();
    });
  },
  
  /**
   * Detener actualizaciones
   */
  stop() {
    if (this.interval) {
      clearInterval(this.interval);
      this.interval = null;
      console.log('🔕 Sistema de notificaciones detenido');
    }
  },

  /**
   * Actualizar notificaciones desde el servidor
   */
  async update() {
    try {
      const response = await fetch('/api/admin/notifications');
      
      if (!response.ok) {
        console.error('Error al obtener notificaciones');
        return;
      }
      
      const result = await response.json();
      
      if (result.success) {
        this.processNotifications(result.data);
      }
      
    } catch (error) {
      console.error('Error en actualización de notificaciones:', error);
    }
  },
  
  /**
   * Procesar y actualizar notificaciones
   */
  processNotifications(data) {
    // Actualizar badges en el menú
    this.updateBadges(data);
    
    // Verificar nuevas publicaciones pendientes
    if (data.nuevas_pendientes > 0) {
      this.showNewPendingNotification(data.nuevas_pendientes);
    }
    
    // Guardar contadores actuales
    this.lastCounts = data;
  },
  
  /**
   * Actualizar badges en el menú
   */
  updateBadges(data) {
    // Badge de publicaciones en menú desktop
    const pubBadges = document.querySelectorAll('.user-menu-item[href*="publicaciones"] .menu-badge');
    pubBadges.forEach(badge => {
      if (data.publicaciones_pendientes > 0) {
        badge.textContent = data.publicaciones_pendientes;
        badge.style.display = 'inline-flex';
      } else {
        badge.style.display = 'none';
      }
    });
    
    // Badge de mensajes
    const msgBadges = document.querySelectorAll('.user-menu-item[href*="mensajes"] .menu-badge, .mobile-menu-link[href*="mensajes"] .menu-badge');
    msgBadges.forEach(badge => {
      if (data.mensajes_sin_leer > 0) {
        badge.textContent = data.mensajes_sin_leer;
        badge.style.display = 'inline-flex';
      } else {
        badge.style.display = 'none';
      }
    });
    
    // Actualizar título de la página si hay pendientes
    this.updatePageTitle(data.publicaciones_pendientes);
  },

  /**
   * Mostrar notificación de nuevas publicaciones pendientes
   */
  showNewPendingNotification(count) {
    const mensaje = count === 1 
      ? 'Nueva publicación pendiente de aprobación'
      : `${count} nuevas publicaciones pendientes de aprobación`;
    
    // Toast
    if (typeof Toast !== 'undefined') {
      Toast.warning(mensaje, 6000);
    }
    
    // Sonido
    if (this.soundEnabled) {
      this.playNotificationSound();
    }
    
    // Notificación desktop
    if (this.desktopEnabled) {
      this.showDesktopNotification('ChileChocados Admin', mensaje);
    }
  },
  
  /**
   * Actualizar título de la página
   */
  updatePageTitle(count) {
    const baseTitle = 'Panel Admin - ChileChocados';
    
    if (count > 0) {
      document.title = `(${count}) ${baseTitle}`;
    } else {
      document.title = baseTitle;
    }
  },

  /**
   * Reproducir sonido de notificación
   */
  playNotificationSound() {
    try {
      // Crear audio context
      const audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const oscillator = audioContext.createOscillator();
      const gainNode = audioContext.createGain();
      
      oscillator.connect(gainNode);
      gainNode.connect(audioContext.destination);
      
      oscillator.frequency.value = 800;
      oscillator.type = 'sine';
      
      gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
      
      oscillator.start(audioContext.currentTime);
      oscillator.stop(audioContext.currentTime + 0.5);
    } catch (error) {
      console.log('No se pudo reproducir sonido');
    }
  },
  
  /**
   * Solicitar permiso para notificaciones desktop
   */
  requestDesktopPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission().then(permission => {
        this.desktopEnabled = permission === 'granted';
      });
    } else if ('Notification' in window && Notification.permission === 'granted') {
      this.desktopEnabled = true;
    }
  },

  /**
   * Mostrar notificación desktop
   */
  showDesktopNotification(title, message) {
    if ('Notification' in window && Notification.permission === 'granted') {
      const notification = new Notification(title, {
        body: message,
        icon: '/assets/icon.png',
        badge: '/assets/icon.png',
        tag: 'admin-notification',
        requireInteraction: false
      });
      
      notification.onclick = () => {
        window.focus();
        notification.close();
      };
      
      // Auto-cerrar después de 5 segundos
      setTimeout(() => notification.close(), 5000);
    }
  },
  
  /**
   * Configurar frecuencia de actualización
   */
  setUpdateFrequency(seconds) {
    this.updateFrequency = seconds * 1000;
    
    // Reiniciar con nueva frecuencia
    if (this.interval) {
      this.stop();
      this.init();
    }
  },
  
  /**
   * Activar/desactivar sonido
   */
  toggleSound(enabled) {
    this.soundEnabled = enabled;
    localStorage.setItem('admin_sound_enabled', enabled);
  }
};

// Auto-iniciar si estamos en una página admin
if (window.location.pathname.startsWith('/admin')) {
  document.addEventListener('DOMContentLoaded', () => {
    AdminNotifications.init();
  });
}

// Exportar para uso global
window.AdminNotifications = AdminNotifications;
