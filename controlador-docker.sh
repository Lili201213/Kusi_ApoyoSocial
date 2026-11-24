#!/bin/bash
# KUSI - Controlador de Docker (Linux/macOS/WSL)

while true; do
    clear
    echo "=================================================================="
    echo "        KUSI - CONTROLADOR DE CONTENEDORES DOCKER"
    echo "        Sistema de Gestión de Apoyo Social (IESTP)"
    echo "=================================================================="
    echo ""
    echo "  [1] Iniciar contenedores (docker compose up -d)"
    echo "  [2] Detener contenedores (docker compose down)"
    echo "  [3] Reiniciar contenedores (docker compose restart)"
    echo "  [4] Ver estado de los contenedores (docker compose ps)"
    echo "  [5] Ejecutar migraciones y datos base (php artisan migrate --seed)"
    echo "  [6] Instalar/Actualizar Composer dentro del contenedor"
    echo "  [7] Abrir terminal bash en el contenedor KUSI"
    echo "  [8] Ver logs de los contenedores en tiempo real"
    echo "  [9] Reconstruir imágenes Docker (docker compose build)"
    echo "  [0] Salir"
    echo ""
    echo "=================================================================="
    read -p "Seleccione una opción [0-9]: " opcion

    case $opcion in
        1)
            docker compose up -d
            echo ""
            echo "Acceso a KUSI:       http://localhost:8000"
            echo "Acceso a phpMyAdmin: http://localhost:8081"
            echo "Reverb WebSockets:   http://localhost:8080"
            read -p "Presione Enter para continuar..."
            ;;
        2)
            docker compose down
            read -p "Presione Enter para continuar..."
            ;;
        3)
            docker compose restart
            read -p "Presione Enter para continuar..."
            ;;
        4)
            docker compose ps
            read -p "Presione Enter para continuar..."
            ;;
        5)
            docker compose exec app php artisan migrate --seed --force
            read -p "Presione Enter para continuar..."
            ;;
        6)
            docker compose exec app composer install
            read -p "Presione Enter para continuar..."
            ;;
        7)
            docker compose exec app bash
            ;;
        8)
            docker compose logs -f
            ;;
        9)
            docker compose build --no-cache
            read -p "Presione Enter para continuar..."
            ;;
        0)
            echo "Saliendo del controlador..."
            exit 0
            ;;
        *)
            echo "Opción no válida."
            sleep 1
            ;;
    esac
done
