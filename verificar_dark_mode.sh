#!/bin/bash

# Script de verificación: Dark Mode en páginas sin footer
# Fecha: 9 de noviembre de 2025

echo "=========================================="
echo "Verificación de Dark Mode"
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
check_dark_mode() {
    local file=$1
    local name=$2
    
    total=$((total + 1))
    
    echo -n "Verificando $name... "
    
    # Verificar que tenga el script de tema
    if ! grep -q "Script de tema" "$file" 2>/dev/null; then
        echo -e "${RED}✗ FALLO${NC} - No tiene script de tema"
        failed=$((failed + 1))
        return 1
    fi
    
    # Verificar que tenga theme-toggle
    if ! grep -q "theme-toggle" "$file" 2>/dev/null; then
        echo -e "${YELLOW}⚠ ADVERTENCIA${NC} - No referencia theme-toggle"
        failed=$((failed + 1))
        return 1
    fi
    
    # Verificar que tenga localStorage
    if ! grep -q "localStorage" "$file" 2>/dev/null; then
        echo -e "${RED}✗ FALLO${NC} - No usa localStorage"
        failed=$((failed + 1))
        return 1
    fi
    
    # Verificar que tenga lucide.createIcons
    if ! grep -q "lucide.createIcons" "$file" 2>/dev/null; then
        echo -e "${YELLOW}⚠ ADVERTENCIA${NC} - No inicializa Lucide"
        failed=$((failed + 1))
        return 1
    fi
    
    echo -e "${GREEN}✓ OK${NC}"
    passed=$((passed + 1))
    return 0
}

echo "Páginas Admin:"
echo "----------------------------------------"
check_dark_mode "app/views/pages/admin/index.php" "Dashboard Admin"
check_dark_mode "app/views/pages/admin/mensajes.php" "Mensajes Admin"
check_dark_mode "app/views/pages/admin/usuarios.php" "Usuarios Admin"
check_dark_mode "app/views/pages/admin/reportes.php" "Reportes Admin"
check_dark_mode "app/views/pages/admin/publicaciones.php" "Publicaciones Admin"

echo ""
echo "Páginas de Usuario:"
echo "----------------------------------------"
check_dark_mode "app/views/pages/usuarios/profile.php" "Perfil Usuario"
check_dark_mode "app/views/pages/usuarios/mis-publicaciones.php" "Mis Publicaciones"

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
    echo ""
    echo "El dark mode debería funcionar en todas las páginas."
    echo "Prueba manualmente:"
    echo "  1. Abre cada página en el navegador"
    echo "  2. Click en el botón de tema (sol/luna)"
    echo "  3. Verifica que el tema cambie"
    echo "  4. Recarga la página y verifica que el tema persista"
    exit 0
else
    echo -e "${RED}✗ Algunas verificaciones fallaron${NC}"
    exit 1
fi
