#!/bin/bash

# ============================================================================
# Script para Comparar Archivos Locales vs Servidor
# ============================================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

SSH_HOST="iad1-shared-b8-24.dreamhost.com"
SSH_USER="lgongo"
REMOTE_PATH="/home/lgongo/chilechocados.cl"

echo -e "${BLUE}============================================${NC}"
echo -e "${BLUE}Comparación Local vs Servidor${NC}"
echo -e "${BLUE}============================================${NC}"
echo ""

# Función para comparar un archivo
compare_file() {
    local file=$1
    local local_file=$file
    local remote_file="$REMOTE_PATH/$file"
    
    # Obtener hash MD5 local
    if [ -f "$local_file" ]; then
        local_md5=$(md5 -q "$local_file" 2>/dev/null || md5sum "$local_file" 2>/dev/null | cut -d' ' -f1)
    else
        echo -e "${RED}[NO EXISTE LOCAL]${NC} $file"
        return
    fi
    
    # Obtener hash MD5 remoto
    remote_md5=$(ssh $SSH_USER@$SSH_HOST "md5sum $remote_file 2>/dev/null | cut -d' ' -f1")
    
    if [ -z "$remote_md5" ]; then
        echo -e "${GREEN}[NUEVO]${NC} $file"
    elif [ "$local_md5" != "$remote_md5" ]; then
        echo -e "${YELLOW}[MODIFICADO]${NC} $file"
        
        # Mostrar fechas de modificación
        local_date=$(stat -f "%Sm" -t "%Y-%m-%d %H:%M:%S" "$local_file" 2>/dev/null || stat -c "%y" "$local_file" 2>/dev/null)
        remote_date=$(ssh $SSH_USER@$SSH_HOST "stat -c '%y' $remote_file 2>/dev/null")
        
        echo -e "  ${BLUE}Local:${NC}    $local_date"
        echo -e "  ${BLUE}Servidor:${NC} $remote_date"
    fi
}

# Archivos importantes a comparar
echo -e "${YELLOW}Comparando archivos críticos...${NC}"
echo ""

# Controladores
echo -e "${BLUE}Controladores:${NC}"
for file in app/controllers/*.php; do
    [ -f "$file" ] && compare_file "$file"
done
echo ""

# Modelos
echo -e "${BLUE}Modelos:${NC}"
for file in app/models/*.php; do
    [ -f "$file" ] && compare_file "$file"
done
echo ""

# Helpers
echo -e "${BLUE}Helpers:${NC}"
for file in app/helpers/*.php; do
    [ -f "$file" ] && compare_file "$file"
done
echo ""

# Configuración
echo -e "${BLUE}Configuración:${NC}"
compare_file "app/config/config.php"
compare_file "public/index.php"
compare_file ".htaccess"
compare_file "includes/helpers.php"
echo ""

# Vistas principales
echo -e "${BLUE}Vistas Admin:${NC}"
for file in app/views/pages/admin/*.php; do
    [ -f "$file" ] && compare_file "$file"
done
echo ""

echo -e "${GREEN}Comparación completada${NC}"
echo ""
echo -e "${YELLOW}Para subir archivos modificados:${NC}"
echo -e "${GREEN}./sync_to_server.sh --sync${NC}"
