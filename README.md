# Registro de Retrospectivas Ágiles (Microservicios)

Aplicación web desacoplada para documentar y hacer seguimiento a las reuniones de **retrospectiva Scrum** por sprint: logros, impedimentos, acciones/compromisos, comentarios y evaluación del cumplimiento de las acciones del sprint anterior.

## Tecnologías y Arquitectura

* **Backend (`back/retros_ms/`):** PHP 8+, **Slim Framework 4**, **Slim PSR-7**, **Eloquent ORM (`illuminate/database`)**, middleware **CORS** y arquitectura por capas aplicando principios **SOLID** (interfaz `RetrosControllerInterface`, validador `RetroValidator`, controladores, repositorios HTTP y routers separados).
* **Frontend (`front/`):** HTML5, CSS3 y JavaScript vanilla consumiendo la API REST con `fetch()` (`front/js/api.js`).
* **Base de datos (`database/registro_retro_db.sql`):** MySQL (`registro_retro_db`) con tablas `sprints` y `retro_items`.

## Estructura del Proyecto

```text
scrum_microservices/
├── back/
│   └── retros_ms/
│       ├── app/
│       │   ├── controllers/
│       │   │   ├── contracts/
│       │   │   │   └── RetrosControllerInterface.php
│       │   │   ├── RetrosController.php
│       │   │   └── SprintsController.php
│       │   ├── global/
│       │   │   └── database_config.php
│       │   ├── middlewares/
│       │   │   └── cors.php
│       │   ├── models/
│       │   │   ├── Retro.php
│       │   │   └── Sprint.php
│       │   ├── presentation/
│       │   │   ├── repositories/
│       │   │   │   ├── RetroRepository.php
│       │   │   │   └── SprintRepository.php
│       │   │   └── routers/
│       │   │       ├── endpoints.php
│       │   │       └── retros_endpoints.php
│       │   └── validators/
│       │       └── RetroValidator.php
│       ├── public/
│       │   └── index.php
│       ├── composer.json
│       └── endpoints.http
├── database/
│   └── registro_retro_db.sql
└── front/
    ├── css/
    │   └── estilos.css
    ├── js/
    │   ├── api.js
    │   ├── item_form.js
    │   ├── retrospectiva.js
    │   ├── sprint_form.js
    │   └── sprints.js
    ├── index.html
    ├── item_form.html
    ├── retrospectiva.html
    └── sprint_form.html
```

## Endpoints de la API (`http://127.0.0.1:8000`)

### Sprints
| Método | Endpoint | Descripción |
| :--- | :--- | :--- |
| `GET` | `/sprints` | Listar todos los sprints |
| `GET` | `/sprints/{id}` | Obtener un sprint por ID |
| `GET` | `/sprints/{id}/anterior` | Obtener el sprint inmediatamente anterior |
| `POST` | `/sprints` | Crear un sprint (`nombre`, `fecha_inicio`, `fecha_fin`) |
| `PUT` | `/sprints/{id}` | Actualizar un sprint |
| `DELETE` | `/sprints/{id}` | Eliminar un sprint (y sus ítems en cascada) |

### Retro Items
| Método | Endpoint | Descripción |
| :--- | :--- | :--- |
| `GET` | `/retros` | Listar todos los ítems de retrospectiva |
| `GET` | `/sprints/{sprint_id}/retros` | Listar todos los ítems de un sprint |
| `GET` | `/sprints/{sprint_id}/acciones` | Listar solo las acciones (`categoria = "accion"`) de un sprint |
| `GET` | `/retros/{id}` | Obtener un ítem por ID |
| `POST` | `/retros` | Crear un ítem (`sprint_id`, `categoria`, `descripcion`, `fecha_revision?`) |
| `PUT` | `/retros/{id}` | Actualizar un ítem |
| `PATCH` | `/retros/{id}/evaluar` | Evaluar cumplimiento de una acción (`cumplida`, `fecha_revision?`) |
| `DELETE` | `/retros/{id}` | Eliminar un ítem |

Categorías permitidas: `logro`, `impedimento`, `accion`, `comentario`, `otro`.

## Instalación y Ejecución

### 1. Base de datos
1. Inicia **MySQL** en XAMPP.
2. Importa el archivo `database/registro_retro_db.sql` desde phpMyAdmin o consola MySQL.
3. Si tus credenciales de MySQL son distintas a `root` sin contraseña, ajústalas en `back/retros_ms/app/global/database_config.php`.

### 2. Backend (API Slim 4)
```powershell
cd back/retros_ms
composer install
php -S 127.0.0.1:8000 -t public
```
> **Nota:** Si usas PHP 8.0, ejecuta `composer update` en lugar de `composer install`. Si PHP indica que falta la extensión `zip`, habilita `extension=zip` en `php.ini` o ejecuta Composer con `php -d extension=zip`.

### 3. Frontend
En otra terminal (o usando la extensión **Live Server** de VS Code sobre `front/index.html`):
```powershell
cd front
php -S 127.0.0.1:5500
```
Abre en el navegador: `http://127.0.0.1:5500/index.html`.

### 4. Pruebas de API
Puedes probar todos los endpoints directamente desde VS Code (con la extensión **REST Client**) abriendo `back/retros_ms/endpoints.http`.

## Equipo
Grupo 1 DNJ
