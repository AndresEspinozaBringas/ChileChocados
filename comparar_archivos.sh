#!/bin/bash

HOST="iad1-shared-b8-24.dreamhost.com"
USER="lgongo"
PORT="22"

echo "=========================================="
echo "COMPARAR ARCHIVOS SERVIDOR VS LOCAL"
echo "=========================================="
echo ""

# Archivos críticos a comparar
FILES=(
    "public/index.php"
    "app/config/config.php"
    "app/core/Database.php"
    ".env"
    ".htaccess"
    "public/.htaccess"
)

for file in "${FILES[@]}"; do
    echo "=== $file ==="
    
    # Descargar archivo del servidor
    scp -P $PORT $USER@$HOST:~/chilechocados.cl/$file /tmp/server_$file 2>/dev/null
    
    if [ -f "/tmp/server_$file" ]; then
        if [ -f "$file" ]; then
            # Comparar
            if diff -q "$file" "/tmp/server_$file" > /dev/null; then
                echo "✓ IGUAL"
            else
                echo "✗ DIFERENTE"
                echo "Diferencias:"
                diff "$file" "/tmp/server_$file" | head -20
            fi
        else
            echo "⚠ No existe localmente"
        fi
        rm "/tmp/server_$file"
    else
        echo "⚠ No se pudo descargar del servidor"
    fi
    echo ""
done
