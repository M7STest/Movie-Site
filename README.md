# Movie Site

A full-stack movie search application with Laravel API backend and Vue.js frontend, fully containerized with Docker.

## Features

- **Laravel API** - RESTful API with JWT authentication
- **Vue.js Frontend** - Modern SPA with Vite
- **Dragonfly** - Redis-compatible in-memory cache
- **Docker** - Complete containerized setup with Nginx

## Tech Stack

- Laravel 12 (PHP 8.2)
- Vue.js 3 + Vite
- Dragonfly (Redis)
- Nginx
- PHP-FPM
- Docker & Docker Compose

## Prerequisites

- Docker
- Docker Compose

## Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/M7STest/Movie-Site.git
cd Movie-Site
```

### 2. Configure Environment

**Windows (PowerShell):**
```powershell
Copy-Item .env.example .env
```

**Linux/Mac:**
```bash
cp .env.example .env
```

Edit `.env` and set your configuration:

```env
# Laravel application key (any random 32-character string)
APP_KEY=base64:your-random-32-char-string-here

# Redis settings (optional)
REDIS_PASSWORD=

#JWT settings
JWT_SECRET=your-random-string

#OMDB settings
OMDB_API_KEY=your-omdb-api-key
```

### 3. Start Docker Containers

```bash
docker compose up -d --build
```

### 4. Access the Application

- **Frontend**: http://localhost:6161
- **API**: http://localhost:8000
- **Dragonfly**: localhost:6380

### Default Credentials

The default authentication credentials are configured in `docker-compose.yml`:

```yaml
AUTH_USERNAME: demo@demo.com
AUTH_PASSWORD: password
```

To change these, edit the `api` service environment in `docker-compose.yml`:

```yaml
services:
  api:
    environment:
      AUTH_USERNAME: your-email@example.com
      AUTH_PASSWORD: your-secure-password
```

Then restart the containers:

```bash
docker compose down
docker compose up -d
```

## Docker Commands

### View Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f api
docker compose logs -f frontend
```

### Stop Containers

```bash
docker compose down
```

### Rebuild Containers

```bash
docker compose up -d --build
```

### Execute Commands in Containers

```bash
# Access API container shell
docker compose exec api bash

docker compose exec api php artisan cache:clear

# Access Frontend container shell
docker compose exec frontend sh
```

## Project Structure

```
Movie-Site/
├── api/                    # Laravel API
│   ├── app/
│   ├── config/
│   ├── routes/
│   ├── docker/            # Nginx & Supervisor configs
│   ├── Dockerfile
│   └── .dockerignore
├── front-end/             # Vue.js Frontend
│   ├── src/
│   ├── public/
│   ├── Dockerfile
│   └── nginx.conf
├── docker-compose.yml     # Docker orchestration
├── .env                   # Environment variables
└── README.md
```

## Services

### API (Laravel)
- **Port**: 8000
- **Stack**: PHP 8.2-FPM + Nginx + Supervisor
- **Features**: JWT auth, Redis caching, OMDB integration

### Frontend (Vue.js)
- **Port**: 6161
- **Stack**: Node 20 + Nginx
- **Build**: Production optimized Vite build

### Dragonfly
- **Port**: 6380 (mapped from 6379 internally)
- **Purpose**: Redis-compatible cache for sessions and data

## Troubleshooting

### Port Already in Use

If you get a port conflict error, edit `docker-compose.yml` and change the external port mapping:

```yaml
ports:
  - "8001:80"  # Change 8000 to 8001 or any available port
```

### Permission Issues

```bash
docker compose exec api chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
```

### View Container Status

```bash
docker compose ps
```

## License

This project is open-source and available under the MIT License.
