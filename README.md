# Nexin Blog MVC - PHP 8.2

Blog completo desarrollado con PHP 8.2 siguiendo el patrón MVC, con Docker, sistema de autenticación seguro, panel de administración, módulo RAG con IA y webhooks n8n.

## Características

- **Autenticación**: Registro, login, logout con sesiones seguras, bcrypt y rate limiting (5 intentos / 15 min)
- **CRUD de Posts**: Crear, editar, eliminar posts con imágenes, categorías y estados (borrador/publicado)
- **Comentarios**: Sistema de comentarios vinculados a posts y usuarios
- **Panel Admin**: Gestión completa de posts, usuarios, categorías y comentarios con estadísticas
- **Módulo RAG**: Búsqueda inteligente con IA usando Ollama (llama3.2:3b) — FULLTEXT + fallback LIKE
- **Webhooks n8n**: Notificaciones automáticas al publicar posts
- **Seguridad**: CSRF tokens, PDO prepared statements, XSS escaping, validación de imágenes server-side, extensión de archivo forzada por MIME type

## Requisitos

- Docker y Docker Compose
- (Opcional) [Ollama](https://ollama.com/) para el módulo RAG
- (Opcional) n8n para webhooks (incluido en docker-compose)

## Instalación

### 1. Clonar y configurar entorno

```bash
git clone <repo-url>
cd project-root
cp .env.example .env
```

Edita `.env` con tus valores (las credenciales por defecto funcionan con Docker).

### 2. Levantar contenedores

```bash
docker-compose up -d --build
```

Servicios disponibles:

| Servicio    | URL                   | Descripción      |
|-------------|-----------------------|------------------|
| App (PHP)   | http://localhost:8080 | Aplicación web   |
| phpMyAdmin  | http://localhost:8081 | Gestión de BD    |
| n8n         | http://localhost:5678 | Webhooks         |

> La base de datos NO expone el puerto 3306 al host — solo accesible internamente entre contenedores.

### 3. (Opcional) Configurar Ollama para RAG

```bash
# Instalar Ollama desde https://ollama.com/
ollama pull llama3.2:3b
ollama serve
```

El módulo RAG se conecta automáticamente a `http://host.docker.internal:11434`.

### 5. (Opcional) Configurar n8n para webhooks

n8n ya está incluido en docker-compose. Para activar las notificaciones:

1. Accede a http://localhost:5678 y crea un workflow: **Webhook Trigger** → **Set** → **Telegram/Email**
2. Copia la URL del webhook y añádela en `.env`:
   ```
   N8N_WEBHOOK_URL=http://n8n:5678/webhook/nuevo-post
   ```

## Credenciales por defecto

| Usuario          | Contraseña  | Rol    |
|------------------|-------------|--------|
| admin@blog.com   | Admin123!   | admin  |
| carlos@blog.com  | Admin123!   | admin  |
| maria@blog.com   | Password1!  | writer |
| otros usuarios   | Password1!  | writer |

## Estructura del proyecto

```
project-root/
├── index.php               # Front controller (enrutador único)
├── .env                    # Variables de entorno (NO subir a Git)
├── .env.example            # Plantilla de variables de entorno
├── docker-compose.yml      # Orquestación de servicios Docker
├── database.sql            # Esquema + datos iniciales de la BD
├── config/
│   ├── Database.php        # Conexión PDO a MySQL
│   └── Environment.php     # Carga de variables .env
├── controllers/
│   ├── AdminController.php # Panel de administración completo
│   ├── CommentController.php
│   ├── PostController.php  # CRUD posts + webhook al publicar
│   ├── RagController.php   # Módulo RAG (búsqueda con IA)
│   └── UserController.php  # Auth: login, registro, logout
├── models/
│   ├── Category.php
│   ├── Comment.php
│   ├── Post.php
│   ├── Retriever.php       # Motor de búsqueda FULLTEXT para RAG
│   └── User.php
├── utils/
│   ├── Auth.php            # Helpers de autenticación y autorización
│   ├── Csrf.php            # Protección CSRF (token + hash_equals)
│   ├── Flash.php           # Mensajes flash por sesión
│   ├── HttpClient.php      # Cliente HTTP cURL (webhooks / Ollama)
│   ├── LLM.php             # Integración con Ollama
│   └── Validator.php       # Validaciones de texto e imagen
├── views/
│   ├── admin/              # Vistas del panel de administración
│   ├── auth/               # Login y registro
│   ├── posts/              # CRUD de posts
│   ├── rag/                # Módulo RAG (ask / answer)
│   ├── comments/
│   └── layout/             # Header y footer globales
├── scripts/                # Scripts de utilidad (reset password, test webhooks)
├── docker/                 # Dockerfile + php.ini personalizado
├── public/                 # Assets estáticos (CSS, imágenes)
└── uploads/                # Imágenes subidas por usuarios
```

## Arquitectura

```
[Cliente] → index.php (Front Controller)
                 ↓
            Router (?action=)
                 ↓
           Controller (validación, lógica)
                 ↓
            Model (PDO / MySQL)
                 ↓
             View (PHP + CSS)
                 ↓
        [Ollama RAG / n8n webhooks]
```

- **Front Controller**: `index.php` enruta todas las peticiones vía `?action=`
- **Controllers**: Lógica de negocio, validación de entrada, llamadas a modelos
- **Models**: Acceso a datos exclusivamente mediante PDO prepared statements
- **Views**: Templates PHP con CSS personalizado y soporte dark mode

## Seguridad implementada

| Medida                        | Implementación                                        |
|-------------------------------|-------------------------------------------------------|
| CSRF                          | Token por sesión + `hash_equals()` en cada POST       |
| Inyección SQL                 | PDO prepared statements en todos los modelos          |
| XSS                           | `htmlspecialchars()` en todas las salidas de las vistas |
| Sesiones seguras              | `httponly`, `samesite=Strict`, `use_strict_mode`      |
| Regeneración de sesión        | `session_regenerate_id(true)` tras login              |
| Contraseñas                   | `password_hash()` bcrypt + `password_verify()`        |
| Variables sensibles           | `.env` + `.gitignore`                                 |
| Validación de imágenes        | `getimagesize()` server-side + extensión por MIME type |
| Uploads seguros               | Extensión forzada desde MIME, no desde el nombre original |
| Rate limiting login           | 5 intentos máx. por sesión / ventana de 15 minutos    |
| Scripts de administración     | Bloqueados vía `.htaccess` (`Require all denied`)     |
| BD no expuesta                | Puerto MySQL solo accesible en red interna Docker     |

## Rutas disponibles

| Ruta (`?action=`)              | Acceso        | Descripción                        |
|-------------------------------|---------------|------------------------------------|
| `posts`                       | Público       | Lista de posts publicados          |
| `show_post&id=N`              | Público       | Detalle de un post                 |
| `login` / `register`          | Público       | Autenticación                      |
| `create_post` / `store_post`  | Autenticado   | Crear post                         |
| `edit_post` / `update_post`   | Autor / Admin | Editar post                        |
| `delete_post`                 | Autor / Admin | Eliminar post (requiere POST)      |
| `store_comment`               | Autenticado   | Publicar comentario                |
| `delete_comment`              | Autor / Admin | Eliminar comentario (requiere POST)|
| `rag_ask` / `rag_answer`      | Autenticado   | Búsqueda con IA                    |
| `admin_dashboard`             | Admin         | Panel de estadísticas              |
| `admin_posts` / `admin_users` | Admin         | Gestión de contenido               |

## Tecnologías

- PHP 8.2 + Apache
- MySQL 8.0
- Docker + Docker Compose
- CSS personalizado (dark mode)
- Ollama (llama3.2:3b)
- n8n (webhooks)

## Licencia

Proyecto académico — Centre d'Estudis Monlau — 2DAW Entorno Servidor
