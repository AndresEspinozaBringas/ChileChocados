#!/bin/bash

# ============================================================================
# Script de Limpieza del Proyecto ChileChocados
# ============================================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Limpieza del Proyecto ChileChocados${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""

# Contador de archivos eliminados
COUNT=0

echo -e "${YELLOW}1. Eliminando archivos HTML migrados a PHP...${NC}"

# Archivos HTML en raíz (wireframes antiguos)
HTML_FILES=(
    "index.html"
    "login.html"
    "register.html"
    "profile.html"
    "sell.html"
    "publish.html"
    "publish-category.html"
    "list.html"
    "detail.html"
    "favorites.html"
    "messages.html"
    "cart.html"
    "share.html"
    "categories.html"
    "post-approval.html"
)

for file in "${HTML_FILES[@]}"; do
    if [ -f "$file" ]; then
        rm "$file"
        echo "  ✓ Eliminado: $file"
        ((COUNT++))
    fi
done

# Archivos HTML en admin/
if [ -d "admin" ]; then
    rm -rf admin/
    echo "  ✓ Eliminado directorio: admin/"
    ((COUNT++))
fi

echo ""
echo -e "${YELLOW}2. Eliminando wireframes duplicados...${NC}"

# Wireframes en public/admin_wireframes/
if [ -d "public/admin_wireframes" ]; then
    rm -rf public/admin_wireframes/
    echo "  ✓ Eliminado: public/admin_wireframes/"
    ((COUNT++))
fi

# Test de diseño
if [ -f "public/test-design-system.html" ]; then
    rm "public/test-design-system.html"
    echo "  ✓ Eliminado: public/test-design-system.html"
    ((COUNT++))
fi

echo ""
echo -e "${YELLOW}3. Eliminando archivos de archivo...${NC}"

if [ -d "_archive" ]; then
    rm -rf _archive/
    echo "  ✓ Eliminado: _archive/"
    ((COUNT++))
fi

if [ -d "wireframes_reference" ]; then
    rm -rf wireframes_reference/
    echo "  ✓ Eliminado: wireframes_reference/"
    ((COUNT++))
fi

echo ""
echo -e "${YELLOW}4. Eliminando imágenes de prueba (uploads)...${NC}"

if [ -d "public/uploads/publicaciones" ]; then
    rm -rf public/uploads/publicaciones/*
    echo "  ✓ Eliminado: public/uploads/publicaciones/* (imágenes de prueba)"
    ((COUNT++))
fi

echo ""
echo -e "${YELLOW}5. Eliminando documentación redundante...${NC}"

# Documentos MD que ya no son necesarios
MD_TO_DELETE=(
    "MIGRACION_HTML_A_PHP.md"
    "CONFIRMACION_SISTEMA_FUNCIONANDO.md"
    "CORRECCION_DETALLE_Y_CONTEO_2025-10-26.md"
    "CORRECCION_FOTOS_PUBLICACIONES.md"
    "ERRORES_CORREGIDOS_2025-10-26.md"
    "FIX_LOGIN_ERROR_2025-10-26.md"
    "RESUMEN_ADMIN_CORREGIDO.md"
    "RESUMEN_FINAL_SESION_2025-10-26.md"
    "RESUMEN_REVISION_FOTOS.md"
)

for file in "${MD_TO_DELETE[@]}"; do
    if [ -f "$file" ]; then
        rm "$file"
        echo "  ✓ Eliminado: $file"
        ((COUNT++))
    fi
done

echo ""
echo -e "${YELLOW}6. Eliminando scripts de prueba...${NC}"

TEST_FILES=(
    "test_db_connection.php"
    "test_email.php"
    "test_foto_guardado.php"
    "test_login_page.php"
    "reset_mysql_root.sh"
    "configurar_git.sh"
    "httpd-vhosts-new.conf"
)

for file in "${TEST_FILES[@]}"; do
    if [ -f "$file" ]; then
        rm "$file"
        echo "  ✓ Eliminado: $file"
        ((COUNT++))
    fi
done

echo ""
echo -e "${YELLOW}7. Limpiando logs...${NC}"

if [ -f "logs/php_errors.log" ]; then
    > logs/php_errors.log
    echo "  ✓ Vaciado: logs/php_errors.log"
fi

if [ -f "logs/email.log" ]; then
    > logs/email.log
    echo "  ✓ Vaciado: logs/email.log"
fi

if [ -f "public/logs/debug.txt" ]; then
    rm "public/logs/debug.txt"
    echo "  ✓ Eliminado: public/logs/debug.txt"
    ((COUNT++))
fi

echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Limpieza completada!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "Total de archivos/directorios eliminados: ${GREEN}$COUNT${NC}"
echo ""
echo -e "${YELLOW}Próximo paso:${NC}"
echo "git add ."
echo "git commit -m 'chore: Limpieza del proyecto - eliminados wireframes y archivos obsoletos'"
echo "git push origin main"
