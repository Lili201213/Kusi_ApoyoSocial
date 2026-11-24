@echo off
chcp 65001 >nul
title KUSI - Controlador de Docker
color 5F

:: Detectar si se usa 'docker compose' o 'docker-compose'
set DOCKER_CMD=docker compose
docker compose version >nul 2>&1
if %errorlevel% neq 0 (
    set DOCKER_CMD=docker-compose
)

:MENU
cls
echo ==================================================================
echo         KUSI - CONTROLADOR DE CONTENEDORES DOCKER
echo         Sistema de Gestion de Apoyo Social (IESTP)
echo ==================================================================
echo.
echo   [1] Iniciar contenedores (%DOCKER_CMD% up -d)
echo   [2] Detener contenedores (%DOCKER_CMD% down)
echo   [3] Reiniciar contenedores (%DOCKER_CMD% restart)
echo   [4] Ver estado de los contenedores (%DOCKER_CMD% ps)
echo   [5] Ejecutar migraciones y datos base (php artisan migrate --seed)
echo   [6] Instalar/Actualizar Composer dentro del contenedor
echo   [7] Abrir terminal bash en el contenedor KUSI
echo   [8] Ver logs de los contenedores en tiempo real
echo   [9] Abrir KUSI en el navegador (http://localhost:8000)
echo   [10] Abrir phpMyAdmin en el navegador (http://localhost:8081)
echo   [11] Reconstruir imagenes Docker (%DOCKER_CMD% build)
echo   [0] Salir
echo.
echo ==================================================================
set /p opcion="Seleccione una opcion [0-11]: "

if "%opcion%"=="1" goto UP
if "%opcion%"=="2" goto DOWN
if "%opcion%"=="3" goto RESTART
if "%opcion%"=="4" goto STATUS
if "%opcion%"=="5" goto MIGRATE
if "%opcion%"=="6" goto COMPOSER
if "%opcion%"=="7" goto BASH
if "%opcion%"=="8" goto LOGS
if "%opcion%"=="9" goto BROWSER
if "%opcion%"=="10" goto PHPMYADMIN
if "%opcion%"=="11" goto BUILD
if "%opcion%"=="0" goto EXIT

echo Opcion no valida.
timeout /t 2 >nul
goto MENU

:UP
cls
echo [INFO] Levantando contenedores KUSI en segundo plano...
%DOCKER_CMD% up -d
echo.
echo [OK] Contenedores iniciados.
echo Acceso a KUSI:       http://localhost:8000
echo Acceso a phpMyAdmin: http://localhost:8081
echo Reverb WebSockets:   http://localhost:8080
echo.
pause
goto MENU

:DOWN
cls
echo [INFO] Deteniendo contenedores KUSI...
%DOCKER_CMD% down
echo.
echo [OK] Contenedores detenidos correctamente.
echo.
pause
goto MENU

:RESTART
cls
echo [INFO] Reiniciando contenedores...
%DOCKER_CMD% restart
echo.
echo [OK] Contenedores reiniciados.
echo.
pause
goto MENU

:STATUS
cls
echo [INFO] Estado actual de los contenedores:
echo.
%DOCKER_CMD% ps
echo.
pause
goto MENU

:MIGRATE
cls
echo [INFO] Ejecutando migraciones y seeders en el contenedor...
%DOCKER_CMD% exec app php artisan migrate --seed --force
echo.
echo [OK] Migraciones completadas.
echo.
pause
goto MENU

:COMPOSER
cls
echo [INFO] Ejecutando composer install en el contenedor...
%DOCKER_CMD% exec app composer install
echo.
echo [OK] Dependencias instaladas.
echo.
pause
goto MENU

:BASH
cls
echo [INFO] Conectando a la consola interactiva de KUSI (escriba 'exit' para salir)...
%DOCKER_CMD% exec app bash
goto MENU

:LOGS
cls
echo [INFO] Mostrando logs en tiempo real (Presione Ctrl+C para volver al menu)...
%DOCKER_CMD% logs -f
pause
goto MENU

:BROWSER
start http://localhost:8000
goto MENU

:PHPMYADMIN
start http://localhost:8081
goto MENU

:BUILD
cls
echo [INFO] Reconstruyendo imagenes de Docker...
%DOCKER_CMD% build --no-cache
echo.
echo [OK] Construccion finalizada.
echo.
pause
goto MENU

:EXIT
echo Saliendo del controlador KUSI Docker.
timeout /t 1 >nul
exit /b 0
