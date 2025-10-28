#!/bin/bash

# ============================================================================
# Script de Sincronización Inteligente a DreamHost
# ============================================================================
# Este script compara archivos locales con el servidor y solo sube cambios
# ============================================================================

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuración
SSH_HOST="iad1-shared-b8-24.dreamhost.com"
SSH_USER="lgongo"
SSH_PORT="22"
REMOTE_PATH="/home/lgongo/chilechocados.cl"

echo -e "${BLUE}============================================${NC}"
echo -e "${BLUE}Sincronización ChileChocados${NC}"
echo -e "${BLUE}============================================${NC}"
echo ""

# Función para mostrar ayuda
show_help() {
    echo "Uso: $0 [OPCIÓN]"
    echo ""
    echo "Opciones:"
    echo "  --dry-run    Simular sin hacer cambios (recomendado primero)"
    echo "  --check      Solo verificar diferencias"
    echo "  --sync       Sincronizar archivos modificados"
    echo "  --full       Subir todos los archivos (deploy completo)"
    echo "  --help       Mostrar esta ayuda"
    echo ""
    exit 0
}

# Verificar directorio
if [ ! -f "composer.json" ]; then
    echo -e "${RED}Error: No estás en el directorio raíz del proyecto${NC}"
    exit 1
fi

# Procesar argumentos
MODE="check"
DRY_RUN=""

case "$1" in
    --dry-run)
        MODE="sync"
        DRY_RUN="--dry-run"
        echo -e "${YELLOW}Modo: Simulación (no se harán cambios)${NC}"
        ;;
    --check)
        MODE="check"
        echo -e "${YELLOW}Modo: Solo verificación${NC}"
        ;;
    --sync)
        MODE="sync"
        echo -e "${YELLOW}Modo: Sincronización${NC}"
        ;;
    --full)
        MODE="full"
        echo -e "${YELLOW}Modo: Deploy completo${NC}"
        ;;
    --help)
        show_help
        ;;
    "")
        MODE="check"
        echo -e "${YELLOW}Modo: Solo verificación (usa --sync para aplicar cambios)${NC}"
        ;;
    *)
        echo -e "${RED}Opción inválida: $1${NC}"
        show_help
        ;;
esac

echo ""

# Archivos y directorios a excluir
EXCLUDE_OPTS=(
    --exclude='.git'
    --exclude='.vscode'
    --exclude='node_modules'
    --exclude='vendor'
    --exclude='_archive'
    --exclude='wireframes_reference'
    --exclude='*.html'
    --exclude='deploy_temp'
    --exclude='*.md'
    --exclude='*.sh'
    --exclude='.DS_Store'
    --exclude='logs/*.log'
    --exclude='.env'
    --exclude='public/uploads/*'
    --exclude='*.sql'
    --exclude='test_*.php'
    --exclude='debug_*.php'
    --exclude='fix_*.php'
    --exclude='crear_*.php'
    --exclude='generar_*.php'
    --exclude='limpiar_*.php'
    --exclude='verificar_*.php'
)

if [ "$MODE" = "check" ]; then
    echo -e "${BLUE}Verificando diferencias con el servidor...${NC}"
    echo ""
    
    rsync -avzn --delete \
        -e "ssh -p $SSH_PORT" \
        "${EXCLUDE_OPTS[@]}" \
        --itemize-changes \
        ./ \
        $SSH_USER@$SSH_HOST:$REMOTE_PATH/ | grep -E '^[<>ch]' | while read line; do
        
        # Interpretar los códigos de rsync
        code="${line:0:11}"
        file="${line:12}"
        
        if [[ $code == *">"* ]]; then
            echo -e "${GREEN}[NUEVO]${NC} $file"
        elif [[ $code == *"c"* ]]; then
            echo -e "${YELLOW}[MODIFICADO]${NC} $file"
        elif [[ $code == *"<"* ]]; then
            echo -e "${RED}[ELIMINADO]${NC} $file"
        fi
    done
    
    echo ""
    echo -e "${BLUE}Para aplicar estos cambios, ejecuta:${NC}"
    echo -e "${GREEN}./sync_to_server.sh --sync${NC}"
    echo ""
    echo -e "${BLUE}Para simular primero (recomendado):${NC}"
    echo -e "${GREEN}./sync_to_server.sh --dry-run${NC}"
    
elif [ "$MODE" = "sync" ] || [ "$MODE" = "full" ]; then
    
    if [ -n "$DRY_RUN" ]; then
        echo -e "${YELLOW}SIMULACIÓN - No se harán cambios reales${NC}"
        echo ""
    else
        echo -e "${RED}¿Estás seguro de sincronizar archivos al servidor?${NC}"
        echo -e "${YELLOW}Esto sobrescribirá archivos en producción${NC}"
        read -p "Escribe 'SI' para continuar: " confirm
        
        if [ "$confirm" != "SI" ]; then
            echo -e "${RED}Operación cancelada${NC}"
            exit 0
        fi
        echo ""
    fi
    
    echo -e "${BLUE}Sincronizando archivos...${NC}"
    echo ""
    
    # Opciones de rsync
    RSYNC_OPTS="-avz --progress"
    
    if [ "$MODE" = "sync" ]; then
        RSYNC_OPTS="$RSYNC_OPTS --update"  # Solo actualizar archivos más nuevos
    fi
    
    if [ -n "$DRY_RUN" ]; then
        RSYNC_OPTS="$RSYNC_OPTS --dry-run"
    fi
    
    rsync $RSYNC_OPTS \
        -e "ssh -p $SSH_PORT" \
        "${EXCLUDE_OPTS[@]}" \
        ./ \
        $SSH_USER@$SSH_HOST:$REMOTE_PATH/
    
    if [ $? -eq 0 ]; then
        echo ""
        if [ -z "$DRY_RUN" ]; then
            echo -e "${GREEN}✓ Sincronización completada${NC}"
            
            # Configurar permisos
            echo ""
            echo -e "${BLUE}Configurando permisos...${NC}"
            ssh -p $SSH_PORT $SSH_USER@$SSH_HOST << 'ENDSSH'
cd ~/chilechocados.cl
chmod -R 755 app public includes
chmod -R 777 logs public/uploads
chmod 644 .htaccess public/.htaccess 2>/dev/null
echo "✓ Permisos configurados"
ENDSSH
            
            echo ""
            echo -e "${GREEN}============================================${NC}"
            echo -e "${GREEN}Sincronización exitosa!${NC}"
            echo -e "${GREEN}============================================${NC}"
        else
            echo ""
            echo -e "${YELLOW}Simulación completada. Usa --sync para aplicar cambios.${NC}"
        fi
    else
        echo ""
        echo -e "${RED}✗ Error en la sincronización${NC}"
        exit 1
    fi
fi

echo ""
