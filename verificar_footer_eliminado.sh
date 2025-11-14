#!/bin/bash

# Script de verificación: Footer eliminado de páginas admin y usuario
# Fecha: 9 de noviembre de 2025

echo "=========================================="
echo "Verificación de Footer Eliminado"
echo "=========================================="
echo ""

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Contador
total=0
passed=0
failed=0

# Función para verificar archivo
check_file() {
    local file=$1
    local name=$2
    
    total=$((total + 1))
    
    echo -n "Verificando $name... "
    
    # Verificar que NO tenga footer
    if grep -q "layout('footer')\|require.*footer\.php" "$file" 2>/dev/null; then
        echo -e "${RED}✗ FALLO${NC} - Todavía tiene footer"
        failed=$((failed + 1))
        return 1
    fi
    
    # Verificar que tenga cierre HTML correcto
    if tail -5 "$file" | grep -q "</body>\|</html>" 2>/dev/null; then
        echo -e "${GREEN}✓ OK${NC}"
        passed=$((passed + 1))
        return 0
    else
        echo -e "${YELLOW}⚠ ADVERTENCIA${NC} - No tiene cierre HTML"
        failed=$((failed + 1))
        return 1
    fi
}

echo "Páginas Admin:"
echo "----------------------------------------"
check_file "app/views/pages/admin/index.php" "Dashboard Admin"
check_file "app/views/pages/admin/mensajes.php" "Mensajes Admin"
check_file "app/views/pages/admin/usuarios.php" "Usuarios Admin"
check_file "app/views/pages/admin/reportes.php" "Reportes Admin"
check_file "app/views/pages/admin/publicaciones.php" "Publicaciones Admin"

echo ""
echo "Páginas de Usuario:"
echo "----------------------------------------"
check_file "app/views/pages/usuarios/profile.php" "Perfil Usuario"
check_file "app/views/pages/usuarios/mis-publicaciones.php" "Mis Publicaciones"

echo ""
echo "=========================================="
echo "Resumen:"
echo "----------------------------------------"
echo -e "Total de archivos: $total"
echo -e "${GREEN}Pasaron: $passed${NC}"
echo -e "${RED}Fallaron: $failed${NC}"
echo "=========================================="

if [ $failed -eq 0 ]; then
    echo -e "${GREEN}✓ Todas las verificaciones pasaron correctamente${NC}"
    exit 0
else
    echo -e "${RED}✗ Algunas verificaciones fallaron${NC}"
    exit 1
fi
