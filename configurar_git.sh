#!/bin/bash
# Script: configurar_git.sh
# Descripción: Configurar branches y hacer commit inicial de Fase 0
# Proyecto: ChileChocados
# Fecha: 23 Octubre 2025

echo "🔧 Configuración de Git para ChileChocados"
echo "==========================================="
echo ""

# Verificar que estamos en el directorio correcto
if [ ! -d ".git" ]; then
    echo "❌ Error: No estás en el directorio raíz del repositorio Git"
    echo "   Ejecuta este script desde: /Users/andresespinozabringas/projects/chilechocados"
    exit 1
fi

echo "✓ Repositorio Git detectado"
echo ""

# Mostrar estado actual
echo "📊 Estado actual del repositorio:"
git status --short
echo ""

# Verificar branch actual
CURRENT_BRANCH=$(git branch --show-current)
echo "📍 Branch actual: $CURRENT_BRANCH"
echo ""

# Preguntar si desea continuar
read -p "¿Deseas continuar con la configuración? (s/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo "❌ Operación cancelada"
    exit 0
fi

echo ""
echo "==================================="
echo "PASO 1: Agregar archivos al stage"
echo "==================================="

# Agregar archivos nuevos/modificados
echo "📦 Agregando archivos..."
git add .gitignore
git add CHANGELOG.md
git add README.md
git add _archive/

# Mostrar lo que se va a commitear
echo ""
echo "📋 Archivos a commitear:"
git status --short
echo ""

read -p "¿Continuar con el commit? (s/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo "❌ Commit cancelado"
    exit 0
fi

echo ""
echo "==================================="
echo "PASO 2: Commit de limpieza Fase 0"
echo "==================================="

git commit -m "Fase 0: Limpieza y preparación del proyecto

✅ Completado:
- Eliminada carpeta duplicada public/public/
- Archivos HTML movidos a _archive/wireframes/ (17 archivos)
- .gitignore actualizado con exclusiones
- CHANGELOG.md creado con historial de versiones
- README.md actualizado con documentación completa

📁 Estructura:
- _archive/wireframes/ con 17 HTML de referencia
- Documentación completa del proyecto
- Configuración de desarrollo lista

🎯 Siguiente fase: Rediseño de interfaz (Fase 1)"

if [ $? -eq 0 ]; then
    echo "✅ Commit realizado exitosamente"
else
    echo "❌ Error al hacer commit"
    exit 1
fi

echo ""
echo "==================================="
echo "PASO 3: Crear branch develop"
echo "==================================="

# Verificar si develop ya existe
if git show-ref --verify --quiet refs/heads/develop; then
    echo "⚠️  Branch 'develop' ya existe"
    read -p "¿Deseas recrearlo? (se perderán cambios no mergeados) (s/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        git branch -D develop
        echo "🗑️  Branch 'develop' eliminado"
    else
        echo "ℹ️  Manteniendo branch 'develop' existente"
    fi
fi

if ! git show-ref --verify --quiet refs/heads/develop; then
    git checkout -b develop
    echo "✅ Branch 'develop' creado y activado"
else
    git checkout develop
    echo "✅ Cambiado a branch 'develop'"
fi

echo ""
echo "==================================="
echo "PASO 4: Crear branch feature/fase1-rediseno"
echo "==================================="

# Verificar si el feature branch ya existe
if git show-ref --verify --quiet refs/heads/feature/fase1-rediseno; then
    echo "⚠️  Branch 'feature/fase1-rediseno' ya existe"
    read -p "¿Deseas recrearlo? (s/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        git branch -D feature/fase1-rediseno
        echo "🗑️  Branch 'feature/fase1-rediseno' eliminado"
    else
        echo "ℹ️  Manteniendo branch 'feature/fase1-rediseno' existente"
        git checkout feature/fase1-rediseno
        echo "✅ Cambiado a branch 'feature/fase1-rediseno'"
        echo ""
        echo "==================================="
        echo "✅ CONFIGURACIÓN COMPLETADA"
        echo "==================================="
        git branch -a
        exit 0
    fi
fi

if ! git show-ref --verify --quiet refs/heads/feature/fase1-rediseno; then
    git checkout -b feature/fase1-rediseno
    echo "✅ Branch 'feature/fase1-rediseno' creado y activado"
fi

echo ""
echo "==================================="
echo "PASO 5: Resumen de branches"
echo "==================================="

echo "📊 Branches disponibles:"
git branch -a

echo ""
echo "==================================="
echo "✅ CONFIGURACIÓN COMPLETADA"
echo "==================================="
echo ""
echo "📌 Branches creados:"
echo "   • main (producción)"
echo "   • develop (desarrollo)"
echo "   • feature/fase1-rediseno (siguiente fase)"
echo ""
echo "📍 Branch actual:"
git branch --show-current
echo ""
echo "🎯 Siguiente paso: Comenzar Fase 1 (Rediseño de Interfaz)"
echo ""
echo "💡 Comandos útiles:"
echo "   git checkout develop              → Cambiar a develop"
echo "   git checkout feature/fase1-rediseno → Cambiar a feature"
echo "   git checkout main                 → Volver a main"
echo "   git branch -a                     → Ver todos los branches"
echo ""
echo "✅ ¡Listo para comenzar el desarrollo!"
