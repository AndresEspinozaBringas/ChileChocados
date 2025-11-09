/**
 * MarcaModeloSelector - Componente de autocompletado para marcas y modelos
 * Carga datos desde chileautos_marcas_modelos.json y proporciona autocompletado
 */

class MarcaModeloSelector {
    constructor(marcaInputId, modeloInputId, jsonPath = '/chileautos_marcas_modelos.json') {
        this.marcaInput = document.getElementById(marcaInputId);
        this.modeloInput = document.getElementById(modeloInputId);
        this.jsonPath = jsonPath;
        this.data = null;
        this.marcaPersonalizada = false;
        this.modeloPersonalizado = false;
        
        if (!this.marcaInput || !this.modeloInput) {
            console.error('MarcaModeloSelector: Inputs no encontrados');
            return;
        }
        
        this.init();
    }

    async init() {
        try {
            await this.loadData();
            this.setupMarcaAutocomplete();
            this.setupModeloAutocomplete();
            this.setupEventListeners();
            
            // Si hay una marca pre-seleccionada (modo edición), cargar sus modelos
            if (this.marcaInput.value) {
                await this.onMarcaChange();
            }
        } catch (error) {
            console.error('Error inicializando MarcaModeloSelector:', error);
        }
    }

    async loadData() {
        // Intentar cargar desde localStorage (caché)
        const cached = localStorage.getItem('marcas_modelos_data');
        const cacheTime = localStorage.getItem('marcas_modelos_cache_time');
        
        // Caché válido por 24 horas
        if (cached && cacheTime && (Date.now() - parseInt(cacheTime)) < 86400000) {
            this.data = JSON.parse(cached);
            console.log('Datos de marcas/modelos cargados desde caché');
            return;
        }

        // Cargar desde API (base de datos)
        try {
            const response = await fetch('/api/marcas');
            if (!response.ok) {
                throw new Error('Error cargando marcas desde API');
            }
            
            const result = await response.json();
            this.data = { marcas: result.marcas || [] };
            
            // Guardar en caché
            localStorage.setItem('marcas_modelos_data', JSON.stringify(this.data));
            localStorage.setItem('marcas_modelos_cache_time', Date.now().toString());
            
            console.log('Datos de marcas/modelos cargados desde API (BD)');
            console.log(`Total marcas: ${this.data.marcas.length}`);
        } catch (error) {
            console.error('Error cargando datos de marcas/modelos:', error);
            // Fallback: permitir ingreso libre
            this.data = { marcas: [] };
        }
    }

    setupMarcaAutocomplete() {
        // Crear datalist con marcas
        const datalist = document.createElement('datalist');
        datalist.id = 'marcas-list';
        
        if (this.data && this.data.marcas) {
            console.log(`📋 Configurando autocompletado con ${this.data.marcas.length} marcas`);
            
            this.data.marcas.forEach(marca => {
                const option = document.createElement('option');
                option.value = marca.nombre;
                option.setAttribute('data-modelos', marca.cantidadModelos);
                datalist.appendChild(option);
            });
        }
        
        // Agregar opción "Otra marca"
        const otraOption = document.createElement('option');
        otraOption.value = '__OTRA__';
        otraOption.textContent = '➕ Otra marca (especificar)';
        datalist.appendChild(otraOption);
        
        this.marcaInput.setAttribute('list', 'marcas-list');
        this.marcaInput.setAttribute('autocomplete', 'off');
        
        // Agregar datalist al DOM
        if (this.marcaInput.parentElement) {
            this.marcaInput.parentElement.appendChild(datalist);
        }
    }

    setupModeloAutocomplete() {
        // Se configura dinámicamente al seleccionar marca
        this.modeloInput.setAttribute('autocomplete', 'off');
    }

    setupEventListeners() {
        // Event listener para cambio de marca
        this.marcaInput.addEventListener('change', async () => await this.onMarcaChange());
        this.marcaInput.addEventListener('blur', async () => await this.onMarcaBlur());
        
        // Event listener para cambio de modelo
        this.modeloInput.addEventListener('change', () => this.onModeloChange());
        this.modeloInput.addEventListener('blur', () => this.onModeloBlur());
    }

    async onMarcaChange() {
        const marcaSeleccionada = this.marcaInput.value.trim();
        
        // Limpiar modelo
        this.modeloInput.value = '';
        this.modeloPersonalizado = false;
        
        if (!marcaSeleccionada) {
            this.modeloInput.disabled = true;
            this.marcaPersonalizada = false;
            this.removeWarning();
            return;
        }
        
        // Buscar marca en datos locales
        const marca = this.findMarca(marcaSeleccionada);
        
        if (marca) {
            // Marca encontrada: cargar modelos desde API
            await this.loadModelosFromAPI(marcaSeleccionada);
            this.modeloInput.disabled = false;
            this.marcaPersonalizada = false;
            this.removeWarning();
        } else if (marcaSeleccionada === '__OTRA__') {
            // Marca personalizada: mostrar input
            this.showMarcaPersonalizadaInput();
        } else {
            // Marca no encontrada: permitir ingreso libre
            this.modeloInput.disabled = false;
            this.marcaPersonalizada = true;
            this.showWarningMarcaPersonalizada();
            
            // Limpiar datalist de modelos
            this.clearModelosDatalist();
        }
    }

    async onMarcaBlur() {
        const marcaSeleccionada = this.marcaInput.value.trim();
        
        if (!marcaSeleccionada) return;
        
        const marca = this.findMarca(marcaSeleccionada);
        
        if (!marca && marcaSeleccionada !== '__OTRA__') {
            this.marcaPersonalizada = true;
            this.showWarningMarcaPersonalizada();
        }
    }

    onModeloChange() {
        const modeloSeleccionado = this.modeloInput.value.trim();
        const marcaSeleccionada = this.marcaInput.value.trim();
        
        if (!modeloSeleccionado || !marcaSeleccionada) return;
        
        if (modeloSeleccionado === '__OTRO__') {
            this.showModeloPersonalizadoInput();
            return;
        }
        
        // Verificar si el modelo existe para la marca
        const marca = this.findMarca(marcaSeleccionada);
        if (marca) {
            const modelo = marca.modelos.find(m => 
                m.nombre.toLowerCase() === modeloSeleccionado.toLowerCase()
            );
            
            if (!modelo) {
                this.modeloPersonalizado = true;
                this.showWarningModeloPersonalizado();
            } else {
                this.modeloPersonalizado = false;
                if (!this.marcaPersonalizada) {
                    this.removeWarning();
                }
            }
        }
    }

    onModeloBlur() {
        this.onModeloChange();
    }

    findMarca(nombreMarca) {
        if (!this.data || !this.data.marcas) return null;
        
        return this.data.marcas.find(m => 
            m.nombre.toLowerCase() === nombreMarca.toLowerCase()
        );
    }

    async loadModelosFromAPI(marca) {
        try {
            const response = await fetch(`/api/modelos?marca=${encodeURIComponent(marca)}`);
            if (!response.ok) {
                throw new Error('Error cargando modelos desde API');
            }
            
            const result = await response.json();
            const modelos = result.modelos || [];
            
            console.log(`Modelos cargados para ${marca}:`, modelos.length);
            
            // Crear/actualizar datalist de modelos
            this.loadModelos(modelos);
        } catch (error) {
            console.error('Error cargando modelos:', error);
            // Permitir ingreso libre si falla
            this.clearModelosDatalist();
        }
    }

    loadModelos(modelos) {
        // Crear/actualizar datalist de modelos
        let datalist = document.getElementById('modelos-list');
        if (!datalist) {
            datalist = document.createElement('datalist');
            datalist.id = 'modelos-list';
            if (this.modeloInput.parentElement) {
                this.modeloInput.parentElement.appendChild(datalist);
            }
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

    clearModelosDatalist() {
        const datalist = document.getElementById('modelos-list');
        if (datalist) {
            datalist.innerHTML = '';
        }
        this.modeloInput.removeAttribute('list');
    }

    showMarcaPersonalizadaInput() {
        // Limpiar el input y permitir ingreso libre
        this.marcaInput.value = '';
        this.marcaInput.placeholder = 'Ingresa la marca del vehículo';
        this.marcaInput.focus();
        this.marcaPersonalizada = true;
        this.modeloInput.disabled = false;
        this.clearModelosDatalist();
        this.showWarningMarcaPersonalizada();
    }

    showModeloPersonalizadoInput() {
        // Limpiar el input y permitir ingreso libre
        this.modeloInput.value = '';
        this.modeloInput.placeholder = 'Ingresa el modelo del vehículo';
        this.modeloInput.focus();
        this.modeloPersonalizado = true;
        this.showWarningModeloPersonalizado();
    }

    showWarningMarcaPersonalizada() {
        this.removeWarning();
        
        const warning = document.createElement('div');
        warning.id = 'marca-modelo-warning';
        warning.className = 'alert alert-warning mt-3';
        warning.innerHTML = `
            <div class="d-flex align-items-start">
                <svg class="icon me-2 flex-shrink-0" style="width: 20px; height: 20px;">
                    <use xlink:href="/assets/icons.svg#alert-triangle"></use>
                </svg>
                <div>
                    <strong>Marca personalizada</strong>
                    <p class="mb-0 mt-1">Has ingresado una marca que no está en nuestro catálogo. 
                    Un administrador revisará y aprobará tu solicitud antes de publicar.</p>
                </div>
            </div>
        `;
        
        // Insertar después del campo de modelo
        const container = this.modeloInput.closest('.form-group') || this.modeloInput.parentElement;
        if (container && container.parentElement) {
            container.parentElement.insertBefore(warning, container.nextSibling);
        }
    }

    showWarningModeloPersonalizado() {
        this.removeWarning();
        
        const warning = document.createElement('div');
        warning.id = 'marca-modelo-warning';
        warning.className = 'alert alert-warning mt-3';
        warning.innerHTML = `
            <div class="d-flex align-items-start">
                <svg class="icon me-2 flex-shrink-0" style="width: 20px; height: 20px;">
                    <use xlink:href="/assets/icons.svg#alert-triangle"></use>
                </svg>
                <div>
                    <strong>Modelo personalizado</strong>
                    <p class="mb-0 mt-1">Has ingresado un modelo que no está en nuestro catálogo. 
                    Un administrador revisará y aprobará tu solicitud antes de publicar.</p>
                </div>
            </div>
        `;
        
        // Insertar después del campo de modelo
        const container = this.modeloInput.closest('.form-group') || this.modeloInput.parentElement;
        if (container && container.parentElement) {
            container.parentElement.insertBefore(warning, container.nextSibling);
        }
    }

    removeWarning() {
        const warning = document.getElementById('marca-modelo-warning');
        if (warning) {
            warning.remove();
        }
    }

    // Método público para verificar si hay marca/modelo personalizado
    tienePersonalizados() {
        return this.marcaPersonalizada || this.modeloPersonalizado;
    }

    // Método público para obtener información de personalización
    getPersonalizacionInfo() {
        return {
            marcaPersonalizada: this.marcaPersonalizada,
            modeloPersonalizado: this.modeloPersonalizado,
            marca: this.marcaInput.value,
            modelo: this.modeloInput.value
        };
    }
}

// Inicializar al cargar página
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Inicializando MarcaModeloSelector...');
    
    const marcaInput = document.getElementById('marca');
    const modeloInput = document.getElementById('modelo');
    
    console.log('Marca input:', marcaInput);
    console.log('Modelo input:', modeloInput);
    
    if (marcaInput && modeloInput) {
        console.log('✅ Inputs encontrados, creando selector...');
        window.marcaModeloSelector = new MarcaModeloSelector('marca', 'modelo');
    } else {
        console.error('❌ No se encontraron los inputs de marca/modelo');
    }
});
