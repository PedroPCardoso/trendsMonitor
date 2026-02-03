# Trends Monitor

Monitor real-time trending topics across **TikTok**, **Instagram**, **YouTube**, and **Google**.

## 🚀 Features

- **Multi-Platform:** Aggregates trends from major social networks.
- **Real-Time Updates:** Automated scrapers and API integrations run hourly.
- **Modern UI:** Built with React, Inertia.js, and Tailwind CSS.
- **Modular Architecture:** Laravel DDD (Domain-Driven Design) for maintainable code.
- **Dockerized:** Ready for easy deployment with Docker Compose.

## 🛠 Tech Stack

- **Backend:** Laravel 12 (PHP 8.2)
- **Frontend:** React + TypeScript + Inertia.js
- **Scrapers:** Python 3 (Playwright, PyTrends)
- **Database:** MySQL 8
- **Cache/Queue:** Redis

## 🐳 Docker Setup (Recommended)

Quickly start the application with Docker.

### 1. Prerequisites
- Docker & Docker Compose installed.

### 2. Configuration
Copy the environment file and configure your API Keys:
```bash
cp .env.example .env
```
Edit `.env` and set:
- `DB_HOST=db`
- `REDIS_HOST=redis`
- `YOUTUBE_API_KEY=your_key`

### 3. Build & Run
```bash
# Build and start containers
docker-compose up -d --build

# Install dependencies (first time only)
docker-compose exec app composer install
docker-compose exec app npm install
docker-compose exec app npm run build
docker-compose exec app php artisan migrate

# Generate App Key
docker-compose exec app php artisan key:generate
```

### 4. Access
- **App:** [http://localhost:8000](http://localhost:8000)

### 5. Start Workers
For trends to update, you must run the queue worker:
```bash
docker-compose exec app php artisan queue:work
```
Or the scheduler:
```bash
docker-compose exec app php artisan schedule:work
```

## 🧪 Testing Scrapers
To verify if Python scrapers are working correctly inside Docker:

```bash
# Test TikTok Scraper
docker-compose exec app python3 python/tiktok_trends.py

# Test Google Trends
docker-compose exec app python3 python/google_trends.py
```

## 🏗 Architecture
The application is structured using modules in `app/Modules`:
- **Shared:** Core infrastructure.
- **YouTube:** API v3 integration.
- **Google:** Python PyTrends script integration.
- **TikTok:** Python Playwright scraper integration.
- **Instagram:** Python Playwright hashtags scraper.
