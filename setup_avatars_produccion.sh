#!/bin/bash

# ============================================
# SCRIPT: Configurar carpetas de avatares en producción
# ============================================
# Ejecutar: bash setup_avatars_produccion.sh
# O: chmod +x setup_avatars_produccion.sh && ./setup_avatars_produccion.sh

echo "============================================"
echo "  CONFIGURACIÓN DE AVATARES - PRODUCCIÓN"
echo "============================================"
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Detectar directorio del proyecto
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
echo "📁 Directorio del script: $SCRIPT_DIR"
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -d "$SCRIPT_DIR/app" ] || [ ! -d "$SCRIPT_DIR/public" ]; then
    echo -e "${RED}❌ Error: No se detectó la estructura del proyecto${NC}"
    echo "Este script debe ejecutarse desde la raíz del proyecto ChileChocados"
    echo "Directorio actual: $SCRIPT_DIR"
    exit 1
fi

echo -e "${GREEN}✓ Estructura del proyecto detectada${NC}"
echo ""

# Crear carpetas
echo "============================================"
echo "  CREANDO CARPETAS"
echo "============================================"

# Carpeta uploads principal
if [ ! -d "$SCRIPT_DIR/public/uploads" ]; then
    mkdir -p "$SCRIPT_DIR/public/uploads"
    echo -e "${GREEN}✓ Creada: public/uploads${NC}"
else
    echo -e "${YELLOW}⚠ Ya existe: public/uploads${NC}"
fi

# Carpeta avatars
if [ ! -d "$SCRIPT_DIR/public/uploads/avatars" ]; then
    mkdir -p "$SCRIPT_DIR/public/uploads/avatars"
    echo -e "${GREEN}✓ Creada: public/uploads/avatars${NC}"
else
    echo -e "${YELLOW}⚠ Ya existe: public/uploads/avatars${NC}"
fi

# Carpeta publicaciones
if [ ! -d "$SCRIPT_DIR/public/uploads/publicaciones" ]; then
    mkdir -p "$SCRIPT_DIR/public/uploads/publicaciones"
    echo -e "${GREEN}✓ Creada: public/uploads/publicaciones${NC}"
else
    echo -e "${YELLOW}⚠ Ya existe: public/uploads/publicaciones${NC}"
fi

echo ""

# Configurar permisos
echo "============================================"
echo "  CONFIGURANDO PERMISOS"
echo "============================================"

chmod 777 "$SCRIPT_DIR/public/uploads"
echo -e "${GREEN}✓ Permisos 777: public/uploads${NC}"

chmod 777 "$SCRIPT_DIR/public/uploads/avatars"
echo -e "${GREEN}✓ Permisos 777: public/uploads/avatars${NC}"

chmod 777 "$SCRIPT_DIR/public/uploads/publicaciones"
echo -e "${GREEN}✓ Permisos 777: public/uploads/publicaciones${NC}"

echo ""

# Crear .htaccess para avatars
echo "============================================"
echo "  CREANDO ARCHIVOS DE SEGURIDAD"
echo "============================================"

HTACCESS_FILE="$SCRIPT_DIR/public/uploads/avatars/.htaccess"
if [ ! -f "$HTACCESS_FILE" ]; then
    cat > "$HTACCESS_FILE" << 'EOF'
# Permitir acceso a imágenes de avatares
<IfModule mod_rewrite.c>
    RewriteEngine Off
</IfModule>

# Permitir acceso a archivos de imagen
<FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Denegar acceso a otros tipos de archivos
<FilesMatch "\.php$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Headers de caché para imágenes
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
</IfModule>
EOF
    echo -e "${GREEN}✓ Creado: .htaccess en avatars${NC}"
else
    echo -e "${YELLOW}⚠ Ya existe: .htaccess en avatars${NC}"
fi

# Crear .gitkeep
GITKEEP_FILE="$SCRIPT_DIR/public/uploads/avatars/.gitkeep"
if [ ! -f "$GITKEEP_FILE" ]; then
    echo "# Carpeta de avatares" > "$GITKEEP_FILE"
    echo -e "${GREEN}✓ Creado: .gitkeep en avatars${NC}"
else
    echo -e "${YELLOW}⚠ Ya existe: .gitkeep en avatars${NC}"
fi

echo ""

# Verificar estructura
echo "============================================"
echo "  VERIFICACIÓN"
echo "============================================"

echo ""
echo "Estructura de carpetas:"
ls -la "$SCRIPT_DIR/public/uploads/"

echo ""
echo "Contenido de avatars:"
ls -la "$SCRIPT_DIR/public/uploads/avatars/"

echo ""

# Prueba de escritura
echo "============================================"
echo "  PRUEBA DE ESCRITURA"
echo "============================================"

TEST_FILE="$SCRIPT_DIR/public/uploads/avatars/test_write.txt"
if echo "Test $(date)" > "$TEST_FILE" 2>/dev/null; then
    echo -e "${GREEN}✓ Se puede escribir en la carpeta avatars${NC}"
    rm "$TEST_FILE"
else
    echo -e "${RED}✗ No se puede escribir en la carpeta avatars${NC}"
    echo "Verifica los permisos manualmente"
fi

echo ""

# Información del sistema
echo "============================================"
echo "  INFORMACIÓN DEL SISTEMA"
echo "============================================"

echo "Usuario actual: $(whoami)"
echo "Directorio: $SCRIPT_DIR"
echo "PHP Version: $(php -v | head -n 1)"

echo ""

# Resumen
echo "============================================"
echo "  RESUMEN"
echo "============================================"

if [ -d "$SCRIPT_DIR/public/uploads/avatars" ] && [ -w "$SCRIPT_DIR/public/uploads/avatars" ]; then
    echo -e "${GREEN}✅ CONFIGURACIÓN COMPLETADA EXITOSAMENTE${NC}"
    echo ""
    echo "Carpetas creadas:"
    echo "  ✓ public/uploads/avatars (777)"
    echo "  ✓ public/uploads/publicaciones (777)"
    echo ""
    echo "Archivos de seguridad:"
    echo "  ✓ .htaccess"
    echo "  ✓ .gitkeep"
    echo ""
    echo "Ahora puedes:"
    echo "  1. Subir avatares desde el perfil de usuario"
    echo "  2. Los avatares se guardarán en: public/uploads/avatars/"
    echo "  3. Serán accesibles en: https://chilechocados.cl/uploads/avatars/"
else
    echo -e "${RED}❌ CONFIGURACIÓN INCOMPLETA${NC}"
    echo ""
    echo "Verifica manualmente:"
    echo "  1. Que las carpetas existan"
    echo "  2. Que tengan permisos de escritura"
    echo "  3. Que el usuario de PHP pueda escribir"
fi

echo ""
echo "============================================"
