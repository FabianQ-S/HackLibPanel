#!/usr/bin/env bash

# ==============================================================================
# HackLib Panel — Script de Inicialización y Diagnóstico para Fedora Linux
# ==============================================================================

set -e

# Códigos de colores ANSI para la consola cyberpunk
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color
BOLD='\033[1m'

echo -e "${MAGENTA}"
cat << "EOF"
  ██░ ██  ▄▄▄       ▄████▄   ██ ▄█▀ ██▓     ██▓ ▄▄▄▄   
 ▓██░ ██▒▒████▄    ▒██▀ ▀█   ██▄█▒ ▓██▒    ▓██▒▓█████▄ 
 ▒██▀▀██░▒██  ▀█▄  ▒▓█    ▄ ▓███▄░ ▒██░    ▒██▒▒██▒ ▄██
 ░▓█ ░██ ░██▄▄▄▄██ ▒▓▓▄ ▄██▒▓██ █▄ ▒██░    ░██░▒██░█▀  
 ░▓█▒░██▓ ▓█   ▓██▒▒ ▓███▀ ░▒██▒ █▄░██████▒░██░░▓█  ▀█▓
  ▒ ░░▒░▒ ▒▒   ▓▒█░░ ░▒ ▒  ░▒ ▒▒ ▓▒░ ▒░▓  ░░▓  ░▒ ▒  ▒ 
  ▒ ░▒░ ░  ▒   ▒▒ ░  ░  ▒   ░ ░▒ ▒░░ ░ ▒  ░ ▒ ░ ░░ ▒  ▒ 
  ░  ░░ ░  ░   ▒   ░        ░ ░░ ░   ░ ░    ▒ ░  ░ ░  ░ 
  ░  ░  ░      ░  ░░ ░      ░  ░       ░  ░ ░      ░    
EOF
echo -e "${CYAN}>> FEDORA ENVIRONMENT LAUNCHER // CYBERPUNK EDITION <<${NC}\n"

# Obtener directorio del proyecto
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

# ------------------------------------------------------------------------------
# 1. Comprobación del Sistema Operativo
# ------------------------------------------------------------------------------
echo -e "${CYAN}[*] Verificando entorno del sistema...${NC}"
if [ -f /etc/os-release ]; then
    . /etc/os-release
    echo -e "    Sistema detectado: ${GREEN}${NAME} ${VERSION_ID}${NC}"
    if [[ "$ID" != "fedora" && "$ID_LIKE" != *"fedora"* ]]; then
        echo -e "    ${YELLOW}Aviso: No se detectó Fedora directamente, pero se continuará con la ejecución.${NC}"
    fi
else
    echo -e "    ${YELLOW}No se pudo determinar la distribución. Continuando...${NC}"
fi

# ------------------------------------------------------------------------------
# 2. Comprobación de PHP y versión
# ------------------------------------------------------------------------------
echo -e "${CYAN}[*] Verificando instalación de PHP...${NC}"
if ! command -v php &> /dev/null; then
    echo -e "    ${RED}[✗] PHP no está instalado en el sistema.${NC}"
    echo -e "    ${YELLOW}Puedes instalarlo en Fedora ejecutando:${NC}"
    echo -e "    ${BOLD}sudo dnf install -y php-cli php-pdo php-sqlite3 composer curl${NC}"
    exit 1
fi

PHP_VER=$(php -r 'echo PHP_VERSION;')
PHP_MAJOR=$(php -r 'echo PHP_MAJOR_VERSION;')
PHP_MINOR=$(php -r 'echo PHP_MINOR_VERSION;')
echo -e "    Versión de PHP detectada: ${GREEN}${PHP_VER}${NC}"

if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 1 ]); then
    echo -e "    ${RED}[✗] Se requiere PHP 8.1 o superior (actual: ${PHP_VER}).${NC}"
    exit 1
fi

# ------------------------------------------------------------------------------
# 3. Comprobación de extensiones de PHP requeridas
# ------------------------------------------------------------------------------
echo -e "${CYAN}[*] Verificando extensiones de PHP (PDO y SQLite3)...${NC}"
MISSING_EXTS=()

if ! php -m | grep -qi "pdo_sqlite"; then
    MISSING_EXTS+=("php-pdo" "php-sqlite3")
fi

if ! php -m | grep -qi "sqlite3"; then
    MISSING_EXTS+=("php-sqlite3")
fi

# Eliminar duplicados
if [ ${#MISSING_EXTS[@]} -gt 0 ]; then
    UNIQUE_MISSING=($(echo "${MISSING_EXTS[@]}" | tr ' ' '\n' | sort -u | tr '\n' ' '))
    echo -e "    ${RED}[✗] Faltan extensiones críticas de SQLite en PHP.${NC}"
    echo -e "    ${YELLOW}Instálalas en Fedora con:${NC}"
    echo -e "    ${BOLD}sudo dnf install -y ${UNIQUE_MISSING[*]}${NC}"
    exit 1
else
    echo -e "    Extensiones PDO y SQLite: ${GREEN}Instaladas y listas.${NC}"
fi

# ------------------------------------------------------------------------------
# 4. Comprobación de Composer
# ------------------------------------------------------------------------------
echo -e "${CYAN}[*] Verificando gestor de dependencias Composer...${NC}"
if ! command -v composer &> /dev/null; then
    echo -e "    ${YELLOW}[!] Composer no está instalado globalmente.${NC}"
    if [ -f "./composer.phar" ]; then
        COMPOSER_BIN="php ./composer.phar"
        echo -e "    ${GREEN}[+] Se utilizará composer.phar local.${NC}"
    else
        echo -e "    ${YELLOW}Descargando composer.phar automáticamente...${NC}"
        curl -sS https://getcomposer.org/installer | php
        COMPOSER_BIN="php ./composer.phar"
        echo -e "    ${GREEN}[+] composer.phar descargado exitosamente.${NC}"
    fi
else
    COMPOSER_BIN="composer"
    echo -e "    Composer detectado: ${GREEN}$(composer --version | head -n1)${NC}"
fi

# Instalar dependencias si no existe vendor/
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo -e "${CYAN}[*] Instalando dependencias de Composer...${NC}"
    $COMPOSER_BIN install --no-interaction
    echo -e "    ${GREEN}[+] Dependencias de Composer instaladas.${NC}"
fi

# ------------------------------------------------------------------------------
# 5. Inicialización de la Base de Datos SQLite
# ------------------------------------------------------------------------------
echo -e "${CYAN}[*] Verificando base de datos SQLite...${NC}"
mkdir -p storage
if [ ! -f "storage/database.sqlite" ]; then
    echo -e "    ${YELLOW}[!] Base de datos no encontrada. Inicializando esquema y datos semilla...${NC}"
    php database/init.php
    echo -e "    ${GREEN}[+] Base de datos creada con 21 libros y Super Admin maestro.${NC}"
else
    echo -e "    Base de datos SQLite: ${GREEN}storage/database.sqlite encontrada.${NC}"
fi

# ------------------------------------------------------------------------------
# 6. Preparación de Tailwind CSS (CLI Standalone)
# ------------------------------------------------------------------------------
echo -e "${CYAN}[*] Verificando compilador Tailwind CSS Standalone...${NC}"
if [ ! -f "./tailwindcss" ]; then
    echo -e "    ${YELLOW}[!] Binario de Tailwind CLI no encontrado.${NC}"
    echo -e "    ${YELLOW}Descargando Tailwind Standalone para Linux x64...${NC}"
    curl -sLO https://github.com/tailwindlabs/tailwindcss/releases/download/v3.4.17/tailwindcss-linux-x64
    chmod +x tailwindcss-linux-x64
    mv tailwindcss-linux-x64 tailwindcss
    echo -e "    ${GREEN}[+] Tailwind CLI listo para su uso.${NC}"
else
    chmod +x tailwindcss
    echo -e "    Compilador Tailwind: ${GREEN}Presente y ejecutable.${NC}"
fi

# Compilar CSS inicial
echo -e "${CYAN}[*] Compilando hoja de estilos Tailwind CSS (Cyberpunk Theme)...${NC}"
mkdir -p public/css
./tailwindcss -i tailwind/input.css -o public/css/app.css --minify 2>/dev/null
echo -e "    ${GREEN}[+] public/css/app.css compilado con éxito.${NC}"

# ------------------------------------------------------------------------------
# 7. Asignación de Puerto
# ------------------------------------------------------------------------------
PORT=${PORT:-8000}
while lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null 2>&1; do
    echo -e "    ${YELLOW}[!] Puerto $PORT ocupado. Probando siguiente puerto...${NC}"
    PORT=$((PORT+1))
done

# ------------------------------------------------------------------------------
# 8. Lanzamiento de Servicios y Servidor PHP
# ------------------------------------------------------------------------------
# Iniciar watcher de Tailwind en segundo plano para compilación en vivo
./tailwindcss -i tailwind/input.css -o public/css/app.css --watch > /dev/null 2>&1 &
TAILWIND_PID=$!

# Control de interrupción (Ctrl+C)
cleanup() {
    echo -e "\n${YELLOW}[*] Deteniendo servicios y observador de estilos...${NC}"
    if kill -0 "$TAILWIND_PID" 2>/dev/null; then
        kill "$TAILWIND_PID" 2>/dev/null || true
    fi
    echo -e "${GREEN}[✓] HackLib Panel detenido con éxito.${NC}"
    exit 0
}
trap cleanup SIGINT SIGTERM EXIT

echo -e "\n${MAGENTA}══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "  ${BOLD}${GREEN}⚡ HACKLIB PANEL INICIADO EXITOSAMENTE${NC}"
echo -e "${MAGENTA}══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "  ${BOLD}URL Local:${NC}           ${CYAN}http://localhost:${PORT}${NC}"
echo -e "  ${BOLD}Super Admin:${NC}         ${YELLOW}admin${NC} / ${YELLOW}admin123${NC}"
echo -e "  ${BOLD}Entorno:${NC}             Fedora Linux // PHP $(php -r 'echo PHP_VERSION;') // SQLite"
echo -e "  ${BOLD}Tailwind Watcher:${NC}    PID: ${TAILWIND_PID} (compilando en caliente)"
echo -e "${MAGENTA}══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "  ${CYAN}Presiona Ctrl+C para detener el servidor.${NC}\n"

# Iniciar servidor PHP integrado
php -S "0.0.0.0:${PORT}" -t public/
