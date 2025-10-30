#!/bin/bash
# Script para aumentar los límites de subida de archivos en PHP

PHP_INI="/opt/homebrew/etc/php/8.3/php.ini"

echo "Haciendo backup del php.ini..."
cp "$PHP_INI" "$PHP_INI.backup.$(date +%Y%m%d_%H%M%S)"

echo "Actualizando límites de subida..."

# Cambiar upload_max_filesize de 2M a 10M
sed -i '' 's/^upload_max_filesize = 2M/upload_max_filesize = 10M/' "$PHP_INI"

# Cambiar post_max_size de 8M a 20M
sed -i '' 's/^post_max_size = 8M/post_max_size = 20M/' "$PHP_INI"

echo "Verificando cambios..."
grep -E "upload_max_filesize|post_max_size" "$PHP_INI" | grep -v "^;"

echo ""
echo "✓ Cambios aplicados:"
echo "  - upload_max_filesize: 2M → 10M"
echo "  - post_max_size: 8M → 20M"
echo ""
echo "⚠️  IMPORTANTE: Debes reiniciar tu servidor web para que los cambios tengan efecto"
echo ""
echo "Si usas PHP built-in server, simplemente detén y vuelve a iniciar el servidor."
echo "Si usas Apache/Nginx, ejecuta: brew services restart php@8.3"
