#!/bin/bash
# Script para resetear la contraseña de root de MySQL

echo "=== Reset de contraseña root de MySQL ==="
echo ""
echo "Este script te ayudará a resetear la contraseña de root"
echo ""

# Detener MySQL
echo "1. Deteniendo MySQL..."
brew services stop mysql
sleep 3

# Crear archivo temporal con la nueva contraseña
echo "2. Creando archivo temporal..."
TEMP_FILE=$(mktemp)
cat > "$TEMP_FILE" << 'EOF'
ALTER USER 'root'@'localhost' IDENTIFIED BY 'Root@2025!';
FLUSH PRIVILEGES;
EOF

echo "3. Iniciando MySQL en modo seguro..."
mysqld --init-file="$TEMP_FILE" &
MYSQL_PID=$!

echo "4. Esperando a que MySQL procese el cambio..."
sleep 10

# Detener el proceso
echo "5. Deteniendo MySQL temporal..."
kill $MYSQL_PID 2>/dev/null
sleep 3

# Limpiar
rm -f "$TEMP_FILE"

# Reiniciar MySQL normalmente
echo "6. Reiniciando MySQL normalmente..."
brew services start mysql
sleep 3

echo ""
echo "=== Proceso completado ==="
echo ""
echo "Nueva contraseña de root: Root@2025!"
echo ""
echo "Prueba con: mysql -u root -p'Root@2025!'"
echo ""
