# Timebank - Community Time Exchange Platform

A Laravel-based community platform for exchanging services based on time rather than money.

## 📚 Documentation

- **[Quick Start - Synology](QUICKSTART-SYNOLOGY.md)** - Get started in 3 steps! (🇩🇪 Schnellstart in 3 Schritten)
- **[Setup Guide](SETUP.md)** - Complete installation and configuration instructions
- **[Synology Deployment](SYNOLOGY-DEPLOYMENT.md)** - Deploy to Synology NAS with Docker (🇩🇪 Deutsche Anleitung verfügbar)

## 🚀 Quick Start

### Option 1: Synology NAS (Recommended for German users / Empfohlen für deutsche Benutzer)

```bash
# Clone repository / Repository klonen
git clone https://github.com/Aznlink168/Timebank.git
cd Timebank

# Configure and run sync script / Sync-Skript konfigurieren und ausführen
nano sync-to-synology.sh  # Edit SYNOLOGY_USER and SYNOLOGY_IP
./sync-to-synology.sh
```

See [SYNOLOGY-DEPLOYMENT.md](SYNOLOGY-DEPLOYMENT.md) for detailed instructions.

### Option 2: Docker Compose

```bash
git clone https://github.com/Aznlink168/Timebank.git
cd Timebank
docker-compose up -d --build
```

### Option 3: Traditional Setup

```bash
git clone https://github.com/Aznlink168/Timebank.git
cd Timebank/timebank
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm run build
php artisan serve
```

## ✨ Features

- 🔐 User authentication and profiles
- 📝 Service request creation and management
- 🎯 Skill-based matching system
- 📅 Availability scheduling
- 🔔 Notification system
- 📱 QR code service verification
- 👥 Admin management panel
- 🌐 Multi-language support preparation

## 🛠️ Technology Stack

- **Backend**: Laravel 10, PHP 8.1+
- **Frontend**: Livewire, Tailwind CSS, Alpine.js
- **Database**: SQLite (default), MySQL, PostgreSQL
- **Authentication**: Laravel Jetstream
- **Real-time**: Livewire components
- **Deployment**: Docker, Docker Compose

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- SQLite/MySQL/PostgreSQL

## 🐳 Docker Deployment

The project includes Docker configuration for easy deployment:

```bash
docker-compose up -d --build
```

Access the application at `http://localhost:8080`

## 🔧 Development

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run dev  # Development with hot reload
npm run build  # Production build

# Start development server
php artisan serve
```

## 📖 Available Routes

- `/` - Welcome page
- `/login` - User login
- `/register` - User registration
- `/dashboard` - User dashboard
- `/service-requests` - Browse service requests
- `/admin` - Admin panel (requires admin role)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 🆘 Support

For issues and questions:
- Check the [Setup Guide](SETUP.md)
- Check the [Synology Deployment Guide](SYNOLOGY-DEPLOYMENT.md)
- Open an issue on GitHub

---

## 🇩🇪 Deutsch

### Schnellstart für Synology NAS

Detaillierte Anleitung in [SYNOLOGY-DEPLOYMENT.md](SYNOLOGY-DEPLOYMENT.md)

```bash
# Repository klonen
git clone https://github.com/Aznlink168/Timebank.git
cd Timebank

# Sync-Skript konfigurieren
nano sync-to-synology.sh  # SYNOLOGY_USER und SYNOLOGY_IP bearbeiten

# Synchronisieren
./sync-to-synology.sh
```

### Docker auf Synology

1. Docker in Synology Paket-Zentrum installieren
2. SSH aktivieren
3. Repository klonen oder hochladen
4. `docker-compose up -d --build` ausführen

Siehe [SYNOLOGY-DEPLOYMENT.md](SYNOLOGY-DEPLOYMENT.md) für vollständige Anleitung.
