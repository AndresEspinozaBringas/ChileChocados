#!/bin/bash

# ============================================================================
# Script de Deploy a DreamHost - ChileChocados
# ============================================================================

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuración del servidor
SSH_HOST="iad1-shared-b8-24.dreamhost.com"
SSH_USER="lgongo"
SSH_PORT="22"
REMOTE_PATH="/home/lgongo/chilechocados.cl"

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Deploy ChileChocados a DreamHost${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -f "composer.json" ]; then
    echo -e "${RED}Error: No estás en el directorio raíz del proyecto${NC}"
    exit 1
fi

echo -e "${YELLOW}Preparando archivos para deploy...${NC}"

# Crear directorio temporal para el deploy
DEPLOY_DIR="deploy_temp"
rm -rf $DEPLOY_DIR
mkdir -p $DEPLOY_DIR

# Copiar archivos necesarios (excluyendo archivos innecesarios)
echo "Copiando archivos..."
rsync -av --progress \
    --exclude='.git' \
    --exclude='.vscode' \
    --exclude='node_modules' \
    --exclude='_archive' \
    --exclude='wireframes_reference' \
    --exclude='*.html' \
    --exclude='deploy_temp' \
    --exclude='*.md' \
    --exclude='*.sh' \
    --exclude='.DS_Store' \
    --exclude='logs/*.log' \
    --exclude='.env' \
    ./ $DEPLOY_DIR/

echo ""
echo -e "${YELLOW}Subiendo archivos al servidor...${NC}"
echo "Host: $SSH_HOST"
echo "Usuario: $SSH_USER"
echo "Ruta remota: $REMOTE_PATH"
echo ""

# Subir archivos usando rsync sobre SSH
rsync -avz --progress \
    -e "ssh -p $SSH_PORT" \
    $DEPLOY_DIR/ \
    $SSH_USER@$SSH_HOST:$REMOTE_PATH/

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✓ Archivos subidos exitosamente${NC}"
    echo ""
    echo -e "${YELLOW}Configurando permisos en el servidor...${NC}"
    
    # Configurar permisos
    ssh -p $SSH_PORT $SSH_USER@$SSH_HOST << 'ENDSSH'
cd ~/chilechocados.cl
chmod -R 755 .
chmod -R 777 logs
chmod -R 777 public/uploads
chmod 644 .htaccess
chmod 644 public/.htaccess
echo "Permisos configurados"
ENDSSH

    echo ""
    echo -e "${GREEN}============================================${NC}"
    echo -e "${GREEN}Deploy completado exitosamente!${NC}"
    echo -e "${GREEN}============================================${NC}"
    echo ""
    echo -e "${YELLOW}Próximos pasos:${NC}"
    echo "1. Crear archivo .env en el servidor con las credenciales de BD"
    echo "2. Ejecutar el script SQL en phpMyAdmin"
    echo "3. Verificar que el sitio funcione correctamente"
    echo ""
    echo -e "${YELLOW}Comandos útiles:${NC}"
    echo "Conectar por SSH: ssh -p $SSH_PORT $SSH_USER@$SSH_HOST"
    echo "Ver logs: ssh -p $SSH_PORT $SSH_USER@$SSH_HOST 'tail -f ~/chilechocados.cl/logs/php_errors.log'"
else
    echo ""
    echo -e "${RED}✗ Error al subir archivos${NC}"
    exit 1
fi

# Limpiar directorio temporal
rm -rf $DEPLOY_DIR

echo ""
echo -e "${GREEN}Proceso finalizado${NC}"
