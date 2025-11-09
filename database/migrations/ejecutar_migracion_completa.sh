#!/bin/bash

# ============================================
# SCRIPT DE MIGRACIÓN COMPLETA
# ============================================
# Ejecuta todos los scripts de migración en orden
# Uso: bash ejecutar_migracion_completa.sh

echo "============================================"
echo "  MIGRACIÓN COMPLETA - MARCAS Y MODELOS"
echo "============================================"
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Solicitar credenciales
echo "Ingresa las credenciales de MySQL:"
read -p "Usuario: " DB_USER
read -sp "Contraseña: " DB_PASS
echo ""
read -p "Base de datos: " DB_NAME
read -p "Host (default: localhost): " DB_HOST
DB_HOST=${DB_HOST:-localhost}

echo ""
echo "============================================"
echo "  PASO 1: CREAR BACKUP"
echo "============================================"

BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
echo "Creando backup en: $BACKUP_FILE"

mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Backup creado exitosamente${NC}"
else
    echo -e "${RED}✗ Error al crear backup${NC}"
    echo "Verifica las credenciales y vuelve a intentar"
    exit 1
fi

echo ""
echo "============================================"
echo "  PASO 2: CREAR TABLAS"
echo "============================================"

mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < PRODUCCION_1_crear_tablas.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Tablas creadas exitosamente${NC}"
else
    echo -e "${YELLOW}⚠ Advertencia: Algunas tablas pueden ya existir${NC}"
fi

echo ""
echo "============================================"
echo "  PASO 3: MODIFICAR TABLA PUBLICACIONES"
echo "============================================"

mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < PRODUCCION_2_modificar_publicaciones.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Tabla publicaciones modificada${NC}"
else
    echo -e "${YELLOW}⚠ Advertencia: Algunas columnas pueden ya existir${NC}"
fi

echo ""
echo "============================================"
echo "  PASO 4: MIGRAR DATOS EXISTENTES"
echo "============================================"

mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < PRODUCCION_3_migrar_datos_existentes.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Datos migrados exitosamente${NC}"
else
    echo -e "${RED}✗ Error al migrar datos${NC}"
fi

echo ""
echo "============================================"
echo "  PASO 5: IMPORTAR MARCAS Y MODELOS"
echo "============================================"
echo "Importando 27 marcas y 544 modelos..."

mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < PRODUCCION_5_inserts_marcas_modelos.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Marcas y modelos importados${NC}"
else
    echo -e "${RED}✗ Error al importar marcas y modelos${NC}"
fi

echo ""
echo "============================================"
echo "  VERIFICACIÓN"
echo "============================================"

# Verificar resultados
RESULT=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -N -e "
SELECT CONCAT(
    'Marcas: ', (SELECT COUNT(*) FROM marcas), ' | ',
    'Modelos: ', (SELECT COUNT(*) FROM modelos), ' | ',
    'Publicaciones actualizadas: ', (SELECT COUNT(*) FROM publicaciones WHERE marca_modelo_aprobado = 1)
);" 2>/dev/null)

echo "$RESULT"

echo ""
echo "============================================"
echo "  MIGRACIÓN COMPLETADA"
echo "============================================"
echo -e "${GREEN}✓ Todos los scripts ejecutados${NC}"
echo ""
echo "Backup guardado en: $BACKUP_FILE"
echo ""
echo "Próximos pasos:"
echo "1. Sube los archivos PHP actualizados"
echo "2. Limpia la caché del servidor"
echo "3. Prueba crear una nueva publicación"
echo ""
echo "Si algo salió mal, restaura el backup:"
echo "  mysql -u $DB_USER -p $DB_NAME < $BACKUP_FILE"
echo ""
