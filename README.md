# Blog MVC - PHP 8.2

Blog completo desarrollado con PHP 8.2 siguiendo el patrón MVC, con Docker, sistema de autenticación, panel de administración, módulo RAG con IA y webhooks n8n.

## Características

- **Autenticación**: Registro, login, logout con sesiones seguras y bcrypt
- **CRUD de Posts**: Crear, editar, eliminar posts con imágenes, categorías y estados (borrador/publicado)
- **Comentarios**: Sistema de comentarios vinculados a posts y usuarios
- **Panel Admin**: Gestión de posts, usuarios, categorías y comentarios
- **Módulo RAG**: Búsqueda inteligente con IA usando Ollama (llama3.2:3b)
- **Webhooks n8n**: Notificaciones automáticas al publicar posts
- **Seguridad**: CSRF tokens, sesiones seguras, variables de entorno, validación de imágenes server-side

## Requisitos

- Docker y Docker Compose
- (Opcional) [Ollama](https://ollama.com/) para el módulo RAG
- (Opcional) [n8n](https://n8n.io/) para webhooks

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

Esto levanta 3 servicios:
| Servicio     | URL                          | Descripción          |
|-------------|------------------------------|----------------------|
| App (PHP)   | http://localhost:8080         | Aplicación web       |
| phpMyAdmin  | http://localhost:8081         | Gestión de BD        |
| MySQL       | localhost:3306                | Base de datos        |

### 3. Ejecutar seeds (datos de prueba)

```bash
docker exec -it mvc_app php scripts/create_seed_users.php
docker exec -it mvc_app php scripts/create_seed_posts.php
docker exec -it mvc_app php scripts/create_seed_comments.php
```

### 4. (Opcional) Configurar Ollama para RAG

```bash
# Instalar Ollama desde https://ollama.com/
ollama pull llama3.2:3b
ollama serve
```

El módulo RAG se conecta automáticamente a `http://host.docker.internal:11434`.

### 5. (Opcional) Configurar n8n para webhooks

1. Instalar n8n: `npm install -g n8n` o Docker
2. Crear workflow: **Webhook Trigger** → **Set** → **Telegram/Email**
3. Copiar la URL del webhook y añadirla en `.env`:
   ```
   N8N_WEBHOOK_URL=http://host.docker.internal:5678/webhook/nuevo-post
   ```

## Credenciales por defecto (seeds)

| Usuario              | Contraseña   | Rol    |
|---------------------|-------------|--------|
| admin@blog.com      | Admin123!   | admin  |
| carlos@blog.com     | Admin123!   | admin  |
| maria@blog.com      | Password1!  | writer |
| otros usuarios...   | Password1!  | writer |

## Estructura del proyecto

```
project-root/
├── index.php               # Front controller (enrutador)
├── .env                    # Variables de entorno (NO subir a Git)
├── docker-compose.yml      # Orquestación Docker
├── database.sql            # Esquema + datos iniciales BD
├── config/
│   ├── Database.php        # Conexión PDO a MySQL
│   └── Environment.php     # Carga de variables .env
├── controllers/
│   ├── AdminController.php # Panel de administración
│   ├── CommentController.php
│   ├── PostController.php  # CRUD + webhook al publicar
│   ├── RagController.php   # Módulo RAG (búsqueda IA)
│   └── UserController.php  # Auth (login/registro)
├── models/
│   ├── Category.php
│   ├── Comment.php
│   ├── Post.php
│   ├── Retriever.php       # Búsqueda FULLTEXT para RAG
│   └── User.php
├── utils/
│   ├── Auth.php            # Helper de autenticación
│   ├── Csrf.php            # Protección CSRF
│   ├── Flash.php           # Mensajes flash
│   ├── HttpClient.php      # Cliente HTTP (webhooks/Ollama)
│   ├── LLM.php             # Integración con Ollama
│   └── Validator.php       # Validaciones (texto, imagen...)
├── views/                  # Vistas PHP con Tailwind CSS
│   ├── admin/              # Panel admin
│   ├── auth/               # Login y registro
│   ├── posts/              # CRUD posts
│   ├── rag/                # Módulo RAG (ask/answer)
│   ├── comments/
│   └── layout/             # Header y footer
├── scripts/                # Seeds de datos de prueba
├── docker/                 # Dockerfile + php.ini
├── public/                 # Assets públicos (CSS/JS)
└── uploads/                # Imágenes subidas por usuarios
```

## Arquitectura

```
[Cliente] → index.php (Router) → Controller → Model → MySQL
                                     ↓
                                   View (PHP + Tailwind)
                                     ↓
                              [Ollama / n8n webhooks]
```

- **Front Controller**: `index.php` recibe todas las peticiones vía `?action=`
- **Controllers**: Lógica de negocio, validación, llamadas a modelos
- **Models**: Acceso a datos con PDO y prepared statements
- **Views**: Templates PHP con Tailwind CSS (modo oscuro incluido)

## Seguridad implementada

| Medida                    | Implementación                              |
|--------------------------|---------------------------------------------|
| CSRF                     | Token por sesión + hash_equals()            |
| Inyección SQL            | PDO prepared statements en todos los modelos|
| XSS                      | htmlspecialchars() en todas las vistas       |
| Sesiones seguras         | httponly, samesite=Strict, use_strict_mode   |
| Regeneración de sesión   | session_regenerate_id(true) tras login       |
| Contraseñas              | password_hash() con bcrypt                  |
| Variables sensibles      | .env + .gitignore                           |
| Validación de imágenes   | getimagesize() server-side                  |

## Tecnologías

- PHP 8.2 + Apache
- MySQL 8.0
- Docker + Docker Compose
- Tailwind CSS (CDN)
- Ollama (llama3.2:3b)
- n8n (webhooks)

## Licencia

Proyecto académico – Centre d'Estudis Monlau – 2DAW Entorno Servidor
