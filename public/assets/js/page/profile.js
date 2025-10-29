/**
 * ============================================================================
 * CHILECHOCADOS - PROFILE PAGE SCRIPTS
 * ============================================================================
 * Funcionalidad para la página de perfil de usuario
 */

(function() {
    'use strict';

    /**
     * ============================================================================
     * MANEJO DE TABS
     * ============================================================================
     */
    function initTabs() {
        const tabs = document.querySelectorAll('.profile-tab');
        
        if (!tabs.length) return;
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                // Remover active de todas las tabs
                tabs.forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.profile-tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Activar tab seleccionada
                this.classList.add('active');
                const targetContent = document.getElementById('tab-' + tabName);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
                
                // Guardar tab activa en localStorage
                try {
                    localStorage.setItem('profileActiveTab', tabName);
                } catch (e) {
                    // Si localStorage no está disponible, ignorar
                }
            });
        });
        
        // Restaurar tab activa desde localStorage
        try {
            const savedTab = localStorage.getItem('profileActiveTab');
            if (savedTab) {
                const savedTabButton = document.querySelector(`.profile-tab[data-tab="${savedTab}"]`);
                if (savedTabButton) {
                    savedTabButton.click();
                }
            }
        } catch (e) {
            // Si localStorage no está disponible, ignorar
        }
    }

    /**
     * ============================================================================
     * SUBIR FOTO DE PERFIL
     * ============================================================================
     */
    function initPhotoUpload() {
        const input = document.getElementById('foto-input');
        if (!input) return;
        
        // No usar onchange inline, manejar con addEventListener
        input.addEventListener('change', function() {
            subirFotoPerfil();
        });
    }

    window.subirFotoPerfil = function() {
        const input = document.getElementById('foto-input');
        const file = input?.files[0];
        
        if (!file) return;
        
        // Validar tipo de archivo
        const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!tiposPermitidos.includes(file.type)) {
            mostrarAlerta('Tipo de archivo no permitido. Solo se aceptan JPG, PNG, GIF o WEBP', 'error');
            input.value = '';
            return;
        }
        
        // Validar tamaño (5MB)
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            mostrarAlerta('La imagen es muy grande. El tamaño máximo es 5MB', 'error');
            input.value = '';
            return;
        }
        
        // Crear FormData
        const formData = new FormData();
        formData.append('foto_perfil', file);
        
        // Mostrar preview inmediato
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            if (preview) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="profile-avatar">';
            }
        };
        reader.readAsDataURL(file);
        
        // Deshabilitar botón mientras se sube
        const changeButton = document.querySelector('.profile-avatar-change');
        const originalHTML = changeButton?.innerHTML;
        if (changeButton) {
            changeButton.disabled = true;
            changeButton.innerHTML = '<svg class="spin" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>';
        }
        
        // Enviar archivo
        fetch(window.BASE_URL + '/perfil/subir-foto', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarAlerta('Foto actualizada correctamente', 'success');
                // Recargar después de 1 segundo para ver la nueva foto
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al subir la foto: ' + error.message, 'error');
            
            // Restaurar preview anterior
            const preview = document.getElementById('avatar-preview');
            if (preview && preview.dataset.original) {
                preview.innerHTML = preview.dataset.original;
            }
        })
        .finally(() => {
            // Restaurar botón
            if (changeButton && originalHTML) {
                changeButton.disabled = false;
                changeButton.innerHTML = originalHTML;
            }
            input.value = '';
        });
    };

    /**
     * ============================================================================
     * VALIDACIÓN DE FORMULARIO DE CONTRASEÑA
     * ============================================================================
     */
    function initPasswordValidation() {
        const form = document.getElementById('form-password');
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            const passwordActual = document.getElementById('password_actual');
            const passwordNueva = document.getElementById('password_nueva');
            const passwordConfirmar = document.getElementById('password_confirmar');
            
            if (!passwordActual || !passwordNueva || !passwordConfirmar) return;
            
            // Validar que la contraseña actual no esté vacía
            if (passwordActual.value.trim() === '') {
                e.preventDefault();
                mostrarAlerta('Debes ingresar tu contraseña actual', 'error');
                passwordActual.focus();
                return false;
            }
            
            // Validar que las contraseñas coincidan
            if (passwordNueva.value !== passwordConfirmar.value) {
                e.preventDefault();
                mostrarAlerta('Las contraseñas no coinciden', 'error');
                passwordConfirmar.focus();
                return false;
            }
            
            // Validar requisitos de contraseña
            const nueva = passwordNueva.value;
            
            if (nueva.length < 8) {
                e.preventDefault();
                mostrarAlerta('La contraseña debe tener al menos 8 caracteres', 'error');
                passwordNueva.focus();
                return false;
            }
            
            if (!/[A-Z]/.test(nueva)) {
                e.preventDefault();
                mostrarAlerta('La contraseña debe contener al menos una letra mayúscula', 'error');
                passwordNueva.focus();
                return false;
            }
            
            if (!/[a-z]/.test(nueva)) {
                e.preventDefault();
                mostrarAlerta('La contraseña debe contener al menos una letra minúscula', 'error');
                passwordNueva.focus();
                return false;
            }
            
            if (!/[0-9]/.test(nueva)) {
                e.preventDefault();
                mostrarAlerta('La contraseña debe contener al menos un número', 'error');
                passwordNueva.focus();
                return false;
            }
            
            // Validar que no sea igual a la actual
            if (passwordActual.value === nueva) {
                e.preventDefault();
                mostrarAlerta('La nueva contraseña debe ser diferente a la actual', 'error');
                passwordNueva.focus();
                return false;
            }
        });
        
        // Indicador de fortaleza de contraseña (opcional)
        const passwordNueva = document.getElementById('password_nueva');
        if (passwordNueva) {
            passwordNueva.addEventListener('input', function() {
                verificarFortalezaPassword(this.value);
            });
        }
    }

    /**
     * Verificar fortaleza de la contraseña
     */
    function verificarFortalezaPassword(password) {
        // Esta función se puede expandir para mostrar un indicador visual
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        // Aquí se podría actualizar un elemento visual de fortaleza
        // Por ahora solo log en consola para debugging
        const strengthText = ['Muy débil', 'Débil', 'Media', 'Buena', 'Excelente'];
        console.log('Fortaleza de contraseña:', strengthText[strength] || 'Muy débil');
    }

    /**
     * ============================================================================
     * VALIDACIÓN DE FORMULARIO DE DATOS PERSONALES
     * ============================================================================
     */
    function initPersonalDataValidation() {
        const form = document.querySelector('form[action*="actualizar"]');
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre');
            const apellido = document.getElementById('apellido');
            const telefono = document.getElementById('telefono');
            
            // Validar nombre
            if (nombre && nombre.value.trim().length < 2) {
                e.preventDefault();
                mostrarAlerta('El nombre debe tener al menos 2 caracteres', 'error');
                nombre.focus();
                return false;
            }
            
            // Validar apellido
            if (apellido && apellido.value.trim().length < 2) {
                e.preventDefault();
                mostrarAlerta('El apellido debe tener al menos 2 caracteres', 'error');
                apellido.focus();
                return false;
            }
            
            // Validar teléfono chileno (opcional pero si está debe ser válido)
            if (telefono && telefono.value.trim() !== '') {
                const telefonoLimpio = telefono.value.replace(/\D/g, '');
                if (telefonoLimpio.length < 8 || telefonoLimpio.length > 15) {
                    e.preventDefault();
                    mostrarAlerta('El teléfono debe tener entre 8 y 15 dígitos', 'error');
                    telefono.focus();
                    return false;
                }
            }
        });
    }

    /**
     * ============================================================================
     * SISTEMA DE ALERTAS
     * ============================================================================
     */
    function mostrarAlerta(mensaje, tipo = 'info') {
        // Buscar contenedor de alertas o crear uno
        let alertContainer = document.querySelector('.alert-container');
        
        if (!alertContainer) {
            alertContainer = document.createElement('div');
            alertContainer.className = 'alert-container';
            alertContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            document.body.appendChild(alertContainer);
        }
        
        // Crear alerta
        const alert = document.createElement('div');
        alert.className = `alert alert-${tipo}`;
        alert.style.cssText = 'margin-bottom: 10px; padding: 12px 16px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideInRight 0.3s ease-out;';
        
        // Colores según tipo
        const colores = {
            success: { bg: '#10B981', text: '#FFFFFF' },
            error: { bg: '#EF4444', text: '#FFFFFF' },
            warning: { bg: '#F59E0B', text: '#FFFFFF' },
            info: { bg: '#3B82F6', text: '#FFFFFF' }
        };
        
        const color = colores[tipo] || colores.info;
        alert.style.backgroundColor = color.bg;
        alert.style.color = color.text;
        
        alert.textContent = mensaje;
        
        // Agregar al contenedor
        alertContainer.appendChild(alert);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            alert.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                alert.remove();
                // Remover contenedor si está vacío
                if (alertContainer.children.length === 0) {
                    alertContainer.remove();
                }
            }, 300);
        }, 5000);
    }

    /**
     * ============================================================================
     * ANIMACIONES CSS
     * ============================================================================
     */
    function addAnimationStyles() {
        if (document.getElementById('profile-animations')) return;
        
        const style = document.createElement('style');
        style.id = 'profile-animations';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            
            .spin {
                animation: spin 1s linear infinite;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * ============================================================================
     * CONFIRMACIÓN DE ACCIONES DESTRUCTIVAS
     * ============================================================================
     */
    function initConfirmations() {
        // Confirmar antes de eliminar publicación
        const deleteButtons = document.querySelectorAll('[data-action="delete"]');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('¿Estás seguro de que deseas eliminar esta publicación? Esta acción no se puede deshacer.')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    }

    /**
     * ============================================================================
     * INICIALIZACIÓN
     * ============================================================================
     */
    function init() {
        // Esperar a que el DOM esté completamente cargado
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }
        
        // Inicializar componentes
        addAnimationStyles();
        initTabs();
        initPhotoUpload();
        initPasswordValidation();
        initPersonalDataValidation();
        initConfirmations();
        
        console.log('✅ Profile page initialized');
    }

    // Inicializar
    init();

})();
