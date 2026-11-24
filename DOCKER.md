# 🐳 Guía de Docker y Contenedores — KUSI

**KUSI – Sistema de Gestión de Apoyo Social**  
*IESTP «César A. Guardia Mayorga» – Coracora*  
*Carrera: Arquitectura de Plataformas y Servicios de Tecnologías de la Información*

---

## 1. Arquitectura de Contenedores

El sistema KUSI está totalmente contenedorizado mediante una arquitectura modular de servicios independientes interconectados por una red interna (`kusi-network`):

| Servicio | Contenedor | Imagen base | Puerto Host | Función principal |
| :--- | :--- | :--- | :--- | :--- |
| **app** | `kusi-app` | `php:8.2-fpm` (Custom) | 9000 (interno) | Backend Laravel 12 con PHP 8.2, Composer y extensiones completas (`pdo_mysql`, `gd`, `intl`, `sockets`, `bcmath`). |
| **web** | `kusi-web` | `nginx:alpine` | **8000** | Servidor web Nginx optimizado con redirecciones para Laravel y assets estáticos. |
| **db** | `kusi-db` | `mysql:8.0` | **3307** | Motor de base de datos MySQL con volumen persistente (`kusi-db-data`). Puerto 3307 en host para no colisionar con XAMPP. |
| **reverb** | `kusi-reverb` | `php:8.2-fpm` (Custom) | **8080** | Servidor de WebSockets (Laravel Reverb) para actualizaciones en vivo en el Portal de Transparencia. |
| **phpmyadmin** | `kusi-phpmyadmin` | `phpmyadmin:latest` | **8081** | Interfaz web gráfica para administrar y consultar la base de datos `bd_kusi`. |

---

## 2. Controlador de Docker (Script Interactivo)

Para facilitar la administración sin necesidad de memorizar comandos de consola, se incluye un **controlador interactivo**:

### En Windows (Doble clic o desde CMD / PowerShell):
```cmd
controlador-docker.bat
```

### En Linux / macOS / WSL:
```bash
chmod +x controlador-docker.sh
./controlador-docker.sh
```

El controlador presenta un menú con opciones automáticas para:
1. Iniciar todos los contenedores en segundo plano (`docker compose up -d`).
2. Detener todos los contenedores (`docker compose down`).
3. Reiniciar contenedores.
4. Ver el estado de salud de cada contenedor (`docker compose ps`).
5. Ejecutar migraciones y datos iniciales (`php artisan migrate --seed`).
6. Instalar o actualizar librerías con Composer.
7. Abrir una consola interactiva Bash dentro del contenedor de la aplicación.
8. Monitorear logs en vivo de todos los servicios.
9. Abrir KUSI en el navegador predeterminado.
10. Abrir phpMyAdmin.
11. Reconstruir imágenes sin caché.

---

## 3. Puesta en Marcha Manual (Comandos Rápidos)

Si prefiere utilizar la terminal directamente con `docker compose`:

### Paso 1: Levantar los contenedores
Asegúrese de tener **Docker Desktop** iniciado y ejecute:
```bash
docker compose up -d
```

### Paso 2: Ejecutar migraciones y datos de prueba
```bash
docker compose exec app php artisan migrate --seed --force
```

### Paso 3: Acceder al sistema
- **Sistema KUSI (Portal público y Login):** [http://localhost:8000](http://localhost:8000)
- **phpMyAdmin (Base de datos):** [http://localhost:8081](http://localhost:8081)  
  *Usuario:* `root` | *Contraseña:* `root_secret` (o usuario `kusi_user` con clave `kusi_secret`).
- **WebSockets Reverb:** [http://localhost:8080](http://localhost:8080)

---

## 4. Estructura de Archivos Docker

```
kusi/
├── Dockerfile                  # Construcción de la imagen PHP 8.2-FPM personalizada
├── docker-compose.yml          # Orquestación de los 5 contenedores
├── .dockerignore               # Optimización de build (ignora vendor, node_modules, etc.)
├── .env.docker                 # Variables de entorno preconfiguradas para Docker
├── controlador-docker.bat      # Script controlador interactivo para Windows
├── controlador-docker.sh       # Script controlador interactivo para Linux/macOS
└── docker/
    ├── nginx/
    │   └── default.conf        # Configuración del virtualhost Nginx
    ├── php/
    │   └── local.ini           # Directivas PHP (límites de memoria, subida de archivos)
    └── entrypoint.sh           # Script de inicio con permisos automáticos y .env
```

---

## 5. Preguntas Frecuentes

### ¿Puedo tener XAMPP y Docker abiertos al mismo tiempo?
Sí. Para evitar conflictos de puertos:
- El servidor web de Docker usa el puerto **8000** (mientras XAMPP usa el 80).
- La base de datos MySQL de Docker expone hacia afuera el puerto **3307** (mientras XAMPP usa el 3306). Dentro de la red de Docker se comunican transparentemente por el 3306.
- phpMyAdmin de Docker usa el puerto **8081**.

### ¿Cómo detengo los contenedores?
Desde el menú interactivo elija la opción `[2]` o ejecute:
```bash
docker compose down
```
Los datos de la base de datos se conservan de forma segura en el volumen `kusi-db-data`.
