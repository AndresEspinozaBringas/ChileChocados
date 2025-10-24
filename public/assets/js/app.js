/* ============================================================================
 * CHILECHOCADOS - JAVASCRIPT PRINCIPAL v2.0
 * ============================================================================
 * Actualizado: Octubre 2025
 * Features: Lucide Icons, Theme Switcher, Smooth Scroll, Lazy Loading
 * ============================================================================ */

(function() {
  'use strict';

  /* ==========================================================================
   * CONFIGURACIÓN GLOBAL
   * ========================================================================== */
  const CONFIG = {
    THEME_STORAGE_KEY: 'cc-theme',
    SCROLL_OFFSET: 80,
    DEBOUNCE_DELAY: 300,
    TOAST_DURATION: 3000,
    LAZY_LOAD_THRESHOLD: 200
  };


  /* ==========================================================================
   * INICIALIZACIÓN
   * ========================================================================== */
  document.addEventListener('DOMContentLoaded', function() {
    initLucideIcons();
    initThemeSwitcher();
    initMobileMenu();
    initSmoothScroll();
    initLazyLoading();
    initTooltips();
    initFormValidation();
    initSearchAutocomplete();
    initImageGallery();
    console.log('🚀 ChileChocados JS v2.0 initialized');
  });


  /* ==========================================================================
   * LUCIDE ICONS - Inicialización
   * ========================================================================== */
  function initLucideIcons() {
    // Verificar que Lucide esté cargado
    if (typeof lucide === 'undefined') {
      console.warn('⚠️ Lucide Icons not loaded. Add CDN: https://unpkg.com/lucide@latest');
      return;
    }

    // Crear todos los iconos
    lucide.createIcons();

    // Observer para iconos dinámicos
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.addedNodes.length) {
          lucide.createIcons();
        }
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true
    });

    console.log('✅ Lucide Icons initialized');
  }


  /* ==========================================================================
   * THEME SWITCHER - Sistema de temas mejorado
   * ========================================================================== */
  function initThemeSwitcher() {
    const root = document.documentElement;
    const themeBtn = document.getElementById('theme-toggle');

    if (!themeBtn) return;

    // Detectar tema inicial
    const savedTheme = localStorage.getItem(CONFIG.THEME_STORAGE_KEY);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');

    // Aplicar tema inicial
    setTheme(initialTheme, false);

    // Event listener
    themeBtn.addEventListener('click', function() {
      const currentTheme = root.getAttribute('data-theme') || 'light';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      setTheme(newTheme, true);
    });

    // Detectar cambios en preferencias del sistema
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
      if (!localStorage.getItem(CONFIG.THEME_STORAGE_KEY)) {
        setTheme(e.matches ? 'dark' : 'light', false);
      }
    });

    function setTheme(theme, animate) {
      // Agregar clase de transición
      if (animate) {
        root.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        setTimeout(() => {
          root.style.transition = '';
        }, 300);
      }

      // Aplicar tema
      root.setAttribute('data-theme', theme);
      localStorage.setItem(CONFIG.THEME_STORAGE_KEY, theme);

      // Actualizar texto del botón
      if (themeBtn) {
        themeBtn.innerHTML = theme === 'dark' 
          ? '<i data-lucide="sun"></i> <span>Modo Claro</span>'
          : '<i data-lucide="moon"></i> <span>Modo Oscuro</span>';
        lucide.createIcons();
      }

      // Mostrar notificación
      if (animate) {
        showToast(`Tema ${theme === 'dark' ? 'oscuro' : 'claro'} activado`);
      }
    }

    console.log('✅ Theme Switcher initialized');
  }


  /* ==========================================================================
   * MOBILE MENU - Menú móvil mejorado
   * ========================================================================== */
  function initMobileMenu() {
    const burgerBtn = document.getElementById('burger-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const overlay = document.querySelector('.mobile-menu-overlay');

    if (!burgerBtn || !mobileMenu) return;

    // Toggle menu
    burgerBtn.addEventListener('click', function() {
      const isOpen = mobileMenu.classList.contains('active');
      
      if (isOpen) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    // Cerrar con overlay
    if (overlay) {
      overlay.addEventListener('click', closeMenu);
    }

    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
        closeMenu();
      }
    });

    function openMenu() {
      mobileMenu.classList.add('active');
      document.body.style.overflow = 'hidden';
      burgerBtn.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
      mobileMenu.classList.remove('active');
      document.body.style.overflow = '';
      burgerBtn.setAttribute('aria-expanded', 'false');
    }

    console.log('✅ Mobile Menu initialized');
  }


  /* ==========================================================================
   * SMOOTH SCROLL - Scroll suave
   * ========================================================================== */
  function initSmoothScroll() {
    // Scroll suave para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
      anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // Ignorar # solo
        if (href === '#' || href === '#!') return;

        const target = document.querySelector(href);
        if (!target) return;

        e.preventDefault();

        const offsetTop = target.offsetTop - CONFIG.SCROLL_OFFSET;
        
        window.scrollTo({
          top: offsetTop,
          behavior: 'smooth'
        });

        // Actualizar URL sin saltar
        if (history.pushState) {
          history.pushState(null, null, href);
        }
      });
    });

    // Scroll to top button
    const scrollTopBtn = document.querySelector('.scroll-to-top');
    if (scrollTopBtn) {
      window.addEventListener('scroll', debounce(function() {
        if (window.pageYOffset > 300) {
          scrollTopBtn.classList.add('visible');
        } else {
          scrollTopBtn.classList.remove('visible');
        }
      }, 100));

      scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    }

    console.log('✅ Smooth Scroll initialized');
  }


  /* ==========================================================================
   * LAZY LOADING - Carga diferida de imágenes
   * ========================================================================== */
  function initLazyLoading() {
    // Usar Intersection Observer si está disponible
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            const img = entry.target;
            
            // Cargar imagen
            if (img.dataset.src) {
              img.src = img.dataset.src;
              img.removeAttribute('data-src');
            }

            if (img.dataset.srcset) {
              img.srcset = img.dataset.srcset;
              img.removeAttribute('data-srcset');
            }

            // Remover clase de loading
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-loaded');

            // Dejar de observar
            observer.unobserve(img);
          }
        });
      }, {
        rootMargin: `${CONFIG.LAZY_LOAD_THRESHOLD}px`
      });

      // Observar todas las imágenes lazy
      document.querySelectorAll('img[data-src]').forEach(function(img) {
        img.classList.add('lazy-loading');
        imageObserver.observe(img);
      });

      console.log('✅ Lazy Loading initialized (IntersectionObserver)');
    } else {
      // Fallback para navegadores antiguos
      loadImagesOnScroll();
      console.log('✅ Lazy Loading initialized (Scroll fallback)');
    }
  }

  function loadImagesOnScroll() {
    const images = document.querySelectorAll('img[data-src]');
    
    function loadImage() {
      images.forEach(function(img) {
        if (isInViewport(img)) {
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
        }
      });
    }

    window.addEventListener('scroll', debounce(loadImage, 100));
    loadImage(); // Cargar imágenes visibles inicialmente
  }

  function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
      rect.top >= -CONFIG.LAZY_LOAD_THRESHOLD &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) + CONFIG.LAZY_LOAD_THRESHOLD
    );
  }


  /* ==========================================================================
   * TOOLTIPS - Sistema de tooltips
   * ========================================================================== */
  function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');

    tooltips.forEach(function(element) {
      element.addEventListener('mouseenter', function() {
        const text = this.getAttribute('data-tooltip');
        const tooltip = createTooltip(text);
        
        document.body.appendChild(tooltip);
        positionTooltip(this, tooltip);

        // Guardar referencia
        this._tooltip = tooltip;
      });

      element.addEventListener('mouseleave', function() {
        if (this._tooltip) {
          this._tooltip.remove();
          this._tooltip = null;
        }
      });
    });

    function createTooltip(text) {
      const tooltip = document.createElement('div');
      tooltip.className = 'tooltip';
      tooltip.textContent = text;
      return tooltip;
    }

    function positionTooltip(element, tooltip) {
      const rect = element.getBoundingClientRect();
      const tooltipRect = tooltip.getBoundingClientRect();

      tooltip.style.top = `${rect.top - tooltipRect.height - 8}px`;
      tooltip.style.left = `${rect.left + (rect.width - tooltipRect.width) / 2}px`;
    }

    console.log('✅ Tooltips initialized');
  }


  /* ==========================================================================
   * TOAST NOTIFICATIONS - Sistema de notificaciones
   * ========================================================================== */
  function showToast(message, type = 'info') {
    const toast = document.getElementById('toast') || createToastContainer();

    // Actualizar contenido
    toast.textContent = message;
    toast.className = `toast ${type}`;
    
    // Mostrar
    toast.classList.add('show');

    // Ocultar después de delay
    setTimeout(function() {
      toast.classList.remove('show');
    }, CONFIG.TOAST_DURATION);
  }

  function createToastContainer() {
    const toast = document.createElement('div');
    toast.id = 'toast';
    toast.className = 'toast';
    document.body.appendChild(toast);
    return toast;
  }

  // Exponer globalmente
  window.showToast = showToast;


  /* ==========================================================================
   * FORM VALIDATION - Validación de formularios
   * ========================================================================== */
  function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(function(form) {
      form.addEventListener('submit', function(e) {
        if (!validateForm(this)) {
          e.preventDefault();
        }
      });

      // Validación en tiempo real
      const inputs = form.querySelectorAll('input, textarea, select');
      inputs.forEach(function(input) {
        input.addEventListener('blur', function() {
          validateField(this);
        });

        input.addEventListener('input', function() {
          // Limpiar error al escribir
          clearFieldError(this);
        });
      });
    });

    function validateForm(form) {
      let isValid = true;
      const inputs = form.querySelectorAll('[required], [data-validate]');

      inputs.forEach(function(input) {
        if (!validateField(input)) {
          isValid = false;
        }
      });

      return isValid;
    }

    function validateField(field) {
      const value = field.value.trim();
      const type = field.type;
      let isValid = true;
      let errorMessage = '';

      // Required
      if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Este campo es obligatorio';
      }

      // Email
      else if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
          isValid = false;
          errorMessage = 'Email inválido';
        }
      }

      // Teléfono chileno
      else if (field.dataset.validate === 'phone' && value) {
        const phoneRegex = /^(\+?56)?(\s?)(0?9)(\s?)[98765]\d{7}$/;
        if (!phoneRegex.test(value)) {
          isValid = false;
          errorMessage = 'Teléfono inválido (ej: +56 9 1234 5678)';
        }
      }

      // RUT chileno
      else if (field.dataset.validate === 'rut' && value) {
        if (!validateRUT(value)) {
          isValid = false;
          errorMessage = 'RUT inválido';
        }
      }

      // Min length
      else if (field.minLength && value.length < field.minLength) {
        isValid = false;
        errorMessage = `Mínimo ${field.minLength} caracteres`;
      }

      if (!isValid) {
        showFieldError(field, errorMessage);
      } else {
        clearFieldError(field);
      }

      return isValid;
    }

    function showFieldError(field, message) {
      clearFieldError(field);

      field.classList.add('error');
      
      const error = document.createElement('span');
      error.className = 'form-error';
      error.textContent = message;
      
      field.parentNode.appendChild(error);
    }

    function clearFieldError(field) {
      field.classList.remove('error');
      
      const error = field.parentNode.querySelector('.form-error');
      if (error) {
        error.remove();
      }
    }

    function validateRUT(rut) {
      // Limpiar formato
      rut = rut.replace(/[^0-9kK]/g, '');
      
      if (rut.length < 2) return false;

      const body = rut.slice(0, -1);
      const dv = rut.slice(-1).toUpperCase();

      // Calcular dígito verificador
      let suma = 0;
      let multiplo = 2;

      for (let i = body.length - 1; i >= 0; i--) {
        suma += multiplo * parseInt(body.charAt(i));
        multiplo = multiplo < 7 ? multiplo + 1 : 2;
      }

      const dvEsperado = 11 - (suma % 11);
      const dvFinal = dvEsperado === 11 ? '0' : dvEsperado === 10 ? 'K' : String(dvEsperado);

      return dv === dvFinal;
    }

    console.log('✅ Form Validation initialized');
  }


  /* ==========================================================================
   * SEARCH AUTOCOMPLETE - Autocompletado de búsqueda
   * ========================================================================== */
  function initSearchAutocomplete() {
    const searchInputs = document.querySelectorAll('input[type="search"][data-autocomplete]');

    searchInputs.forEach(function(input) {
      const resultsContainer = createResultsContainer(input);

      input.addEventListener('input', debounce(function() {
        const query = this.value.trim();

        if (query.length < 2) {
          hideResults(resultsContainer);
          return;
        }

        // Aquí normalmente harías un fetch a la API
        // Por ahora mostramos resultados de ejemplo
        const results = getExampleResults(query);
        showResults(resultsContainer, results);
      }, CONFIG.DEBOUNCE_DELAY));

      // Cerrar al hacer clic fuera
      document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
          hideResults(resultsContainer);
        }
      });
    });

    function createResultsContainer(input) {
      const container = document.createElement('div');
      container.className = 'autocomplete-results';
      input.parentNode.style.position = 'relative';
      input.parentNode.appendChild(container);
      return container;
    }

    function showResults(container, results) {
      if (results.length === 0) {
        hideResults(container);
        return;
      }

      container.innerHTML = results.map(result => `
        <div class="autocomplete-item">
          <i data-lucide="search"></i>
          <span>${result}</span>
        </div>
      `).join('');

      container.classList.add('active');
      lucide.createIcons();
    }

    function hideResults(container) {
      container.classList.remove('active');
    }

    function getExampleResults(query) {
      const examples = [
        'Toyota Corolla 2020',
        'Nissan Versa 2019',
        'Chevrolet Spark 2021',
        'Hyundai Accent 2020'
      ];
      
      return examples.filter(item => 
        item.toLowerCase().includes(query.toLowerCase())
      ).slice(0, 5);
    }

    console.log('✅ Search Autocomplete initialized');
  }


  /* ==========================================================================
   * IMAGE GALLERY - Galería de imágenes
   * ========================================================================== */
  function initImageGallery() {
    const galleryImages = document.querySelectorAll('[data-gallery]');

    galleryImages.forEach(function(img) {
      img.addEventListener('click', function() {
        openLightbox(this);
      });
      img.style.cursor = 'pointer';
    });

    function openLightbox(img) {
      const lightbox = createLightbox(img.src, img.alt);
      document.body.appendChild(lightbox);

      // Cerrar con click
      lightbox.addEventListener('click', function(e) {
        if (e.target === this || e.target.classList.contains('lightbox-close')) {
          this.remove();
          document.body.style.overflow = '';
        }
      });

      // Cerrar con ESC
      document.addEventListener('keydown', function onEscape(e) {
        if (e.key === 'Escape') {
          lightbox.remove();
          document.body.style.overflow = '';
          document.removeEventListener('keydown', onEscape);
        }
      });

      document.body.style.overflow = 'hidden';
    }

    function createLightbox(src, alt) {
      const lightbox = document.createElement('div');
      lightbox.className = 'lightbox';
      lightbox.innerHTML = `
        <button class="lightbox-close" aria-label="Cerrar">
          <i data-lucide="x"></i>
        </button>
        <img src="${src}" alt="${alt || 'Imagen'}">
      `;
      lucide.createIcons();
      return lightbox;
    }

    console.log('✅ Image Gallery initialized');
  }


  /* ==========================================================================
   * UTILITY FUNCTIONS - Funciones de utilidad
   * ========================================================================== */

  // Debounce para optimizar eventos
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func.apply(this, args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // Throttle para scroll events
  function throttle(func, wait) {
    let inThrottle;
    return function(...args) {
      if (!inThrottle) {
        func.apply(this, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, wait);
      }
    };
  }

  // Exponer utilidades globalmente
  window.ChileChocados = {
    showToast: showToast,
    debounce: debounce,
    throttle: throttle
  };

})();


/* ============================================================================
 * ANALYTICS Y TRACKING (Opcional)
 * ============================================================================ */
// Agregar aquí código de Google Analytics, Facebook Pixel, etc.
