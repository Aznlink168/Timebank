# Synology NAS Deployment Guide / Synology NAS Bereitstellungsanleitung

This guide explains how to quickly deploy and synchronize the Timebank project on a Synology NAS device.

Dieser Leitfaden erklärt, wie das Timebank-Projekt schnell auf einem Synology NAS-Gerät bereitgestellt und synchronisiert werden kann.

---

## English Version

## Prerequisites

- Synology NAS with DSM 7.0 or higher
- Docker package installed on Synology (via Package Center)
- Git Server package (optional, for git-based sync)
- SSH access enabled
- Basic knowledge of Docker

## Method 1: Docker Deployment (Recommended)

This is the easiest and most reliable method for running Timebank on Synology.

### Step 1: Enable SSH and Docker

1. Open DSM (Synology DiskStation Manager)
2. Go to **Package Center**
3. Install **Docker** if not already installed
4. Go to **Control Panel** → **Terminal & SNMP**
5. Enable **SSH service**

### Step 2: Transfer Files to Synology

#### Option A: Using Git (Recommended)

```bash
# SSH into your Synology
ssh your-username@synology-ip

# Navigate to docker directory
cd /volume1/docker

# Clone the repository
git clone https://github.com/Aznlink168/Timebank.git
cd Timebank
```

#### Option B: Using File Station

1. Open **File Station** in DSM
2. Navigate to `/docker` folder (create if doesn't exist)
3. Upload the Timebank project folder
4. Or use **rsync** from your local machine:

```bash
# From your local machine
rsync -avz --progress /path/to/Timebank/ your-username@synology-ip:/volume1/docker/Timebank/
```

### Step 3: Prepare the Application

```bash
# SSH into Synology
ssh your-username@synology-ip
cd /volume1/docker/Timebank

# Create environment file
cd timebank
cp .env.example .env

# Edit .env file with your settings
nano .env
```

Update these settings in `.env`:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-synology-ip:8080

DB_CONNECTION=sqlite
```

### Step 4: Build and Run with Docker Compose

```bash
# From /volume1/docker/Timebank directory
cd /volume1/docker/Timebank

# Build and start the containers
docker-compose up -d --build

# Wait for build to complete, then run migrations
docker exec -it timebank-app php artisan migrate --force

# Generate application key if not already set
docker exec -it timebank-app php artisan key:generate
```

### Step 5: Access Your Application

Open your web browser and navigate to:
```
http://your-synology-ip:8080
```

### Step 6: Configure Automatic Updates

Create a scheduled task in DSM to automatically pull updates:

1. Go to **Control Panel** → **Task Scheduler**
2. Create → **Scheduled Task** → **User-defined script**
3. General: Name it "Update Timebank"
4. Schedule: Set your preferred update frequency
5. Task Settings → User-defined script:

```bash
cd /volume1/docker/Timebank
git pull
docker-compose down
docker-compose up -d --build
docker exec -it timebank-app php artisan migrate --force
```

---

## Method 2: Quick Sync with rsync

For development or quick file synchronization without Docker:

### Create Sync Script

Save this as `sync-to-synology.sh` on your local machine:

```bash
#!/bin/bash

# Configuration
SYNOLOGY_USER="your-username"
SYNOLOGY_IP="your-synology-ip"
SYNOLOGY_PATH="/volume1/web/timebank"
LOCAL_PATH="./timebank"

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}Starting sync to Synology NAS...${NC}"

# Sync files (excluding unnecessary directories)
rsync -avz --progress \
  --exclude 'node_modules' \
  --exclude 'vendor' \
  --exclude '.git' \
  --exclude 'storage/logs/*' \
  --exclude 'storage/framework/cache/*' \
  --exclude 'storage/framework/sessions/*' \
  --exclude 'storage/framework/views/*' \
  --exclude '.env' \
  $LOCAL_PATH/ $SYNOLOGY_USER@$SYNOLOGY_IP:$SYNOLOGY_PATH/

if [ $? -eq 0 ]; then
    echo -e "${GREEN}Sync completed successfully!${NC}"
    
    # Optional: Run composer and npm install on Synology
    echo -e "${BLUE}Running composer install on Synology...${NC}"
    ssh $SYNOLOGY_USER@$SYNOLOGY_IP "cd $SYNOLOGY_PATH && composer install --no-dev --optimize-autoloader"
    
    echo -e "${BLUE}Building assets on Synology...${NC}"
    ssh $SYNOLOGY_USER@$SYNOLOGY_IP "cd $SYNOLOGY_PATH && npm install && npm run build"
    
    echo -e "${GREEN}All done!${NC}"
else
    echo "Sync failed!"
    exit 1
fi
```

Make it executable:
```bash
chmod +x sync-to-synology.sh
```

Run it:
```bash
./sync-to-synology.sh
```

---

## Method 3: Using Synology Web Station

If you prefer to use Synology's built-in web server:

### Step 1: Install Web Station

1. Open **Package Center**
2. Install **Web Station**
3. Install **PHP 8.1** or higher
4. Install **phpMyAdmin** (optional, for database management)

### Step 2: Configure Web Station

1. Open **Web Station**
2. Go to **PHP Settings**
3. Enable required extensions:
   - pdo_sqlite or pdo_mysql
   - openssl
   - mbstring
   - tokenizer
   - zip
   - gd

### Step 3: Deploy Application

1. Upload files to `/volume1/web/timebank`
2. Configure virtual host in Web Station
3. Point to `/volume1/web/timebank/public` as document root
4. Follow standard Laravel setup (composer install, npm build, etc.)

---

## Troubleshooting

### Port Already in Use

If port 8080 is already in use, edit `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Change 8080 to any available port
```

### Permission Issues

```bash
# Fix permissions
docker exec -it timebank-app chown -R www-data:www-data /var/www/html/storage
docker exec -it timebank-app chmod -R 755 /var/www/html/storage
```

### Database Issues

```bash
# Reset database
docker exec -it timebank-app php artisan migrate:fresh --force
```

### View Logs

```bash
# Docker logs
docker logs timebank-app

# Laravel logs
docker exec -it timebank-app tail -f storage/logs/laravel.log
```

---

## Deutsche Version

## Voraussetzungen

- Synology NAS mit DSM 7.0 oder höher
- Docker-Paket installiert auf Synology (über Paket-Zentrum)
- Git Server Paket (optional, für Git-basierte Synchronisation)
- SSH-Zugriff aktiviert
- Grundkenntnisse in Docker

## Methode 1: Docker-Bereitstellung (Empfohlen)

Dies ist die einfachste und zuverlässigste Methode, um Timebank auf Synology auszuführen.

### Schritt 1: SSH und Docker aktivieren

1. Öffnen Sie DSM (Synology DiskStation Manager)
2. Gehen Sie zum **Paket-Zentrum**
3. Installieren Sie **Docker**, falls noch nicht installiert
4. Gehen Sie zu **Systemsteuerung** → **Terminal & SNMP**
5. Aktivieren Sie den **SSH-Dienst**

### Schritt 2: Dateien auf Synology übertragen

#### Option A: Git verwenden (Empfohlen)

```bash
# SSH-Verbindung zu Ihrer Synology
ssh ihr-benutzername@synology-ip

# Zum Docker-Verzeichnis navigieren
cd /volume1/docker

# Repository klonen
git clone https://github.com/Aznlink168/Timebank.git
cd Timebank
```

#### Option B: File Station verwenden

1. Öffnen Sie **File Station** in DSM
2. Navigieren Sie zum `/docker` Ordner (erstellen, falls nicht vorhanden)
3. Laden Sie den Timebank-Projektordner hoch
4. Oder verwenden Sie **rsync** von Ihrem lokalen Computer:

```bash
# Von Ihrem lokalen Computer
rsync -avz --progress /pfad/zu/Timebank/ ihr-benutzername@synology-ip:/volume1/docker/Timebank/
```

### Schritt 3: Anwendung vorbereiten

```bash
# SSH-Verbindung zu Synology
ssh ihr-benutzername@synology-ip
cd /volume1/docker/Timebank

# Umgebungsdatei erstellen
cd timebank
cp .env.example .env

# .env-Datei mit Ihren Einstellungen bearbeiten
nano .env
```

Aktualisieren Sie diese Einstellungen in `.env`:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=http://ihre-synology-ip:8080

DB_CONNECTION=sqlite
```

### Schritt 4: Mit Docker Compose erstellen und ausführen

```bash
# Vom /volume1/docker/Timebank Verzeichnis
cd /volume1/docker/Timebank

# Container erstellen und starten
docker-compose up -d --build

# Warten Sie, bis der Build abgeschlossen ist, dann Migrationen ausführen
docker exec -it timebank-app php artisan migrate --force

# Anwendungsschlüssel generieren, falls noch nicht gesetzt
docker exec -it timebank-app php artisan key:generate
```

### Schritt 5: Auf Ihre Anwendung zugreifen

Öffnen Sie Ihren Webbrowser und navigieren Sie zu:
```
http://ihre-synology-ip:8080
```

### Schritt 6: Automatische Updates konfigurieren

Erstellen Sie eine geplante Aufgabe in DSM, um Updates automatisch abzurufen:

1. Gehen Sie zu **Systemsteuerung** → **Aufgabenplanung**
2. Erstellen → **Geplante Aufgabe** → **Benutzerdefiniertes Script**
3. Allgemein: Nennen Sie es "Timebank aktualisieren"
4. Zeitplan: Legen Sie Ihre bevorzugte Aktualisierungshäufigkeit fest
5. Aufgabeneinstellungen → Benutzerdefiniertes Script:

```bash
cd /volume1/docker/Timebank
git pull
docker-compose down
docker-compose up -d --build
docker exec -it timebank-app php artisan migrate --force
```

---

## Schnelle Synchronisation

Für schnelle Dateisynchronisation erstellen Sie ein Skript `sync-synology.sh`:

```bash
#!/bin/bash
rsync -avz --progress \
  --exclude 'node_modules' \
  --exclude 'vendor' \
  ./timebank/ benutzername@synology-ip:/volume1/docker/Timebank/timebank/
```

Ausführbar machen und ausführen:
```bash
chmod +x sync-synology.sh
./sync-synology.sh
```

---

## Fehlerbehebung

### Port bereits verwendet

Wenn Port 8080 bereits verwendet wird, bearbeiten Sie `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Ändern Sie 8080 zu einem verfügbaren Port
```

### Berechtigungsprobleme

```bash
# Berechtigungen korrigieren
docker exec -it timebank-app chown -R www-data:www-data /var/www/html/storage
docker exec -it timebank-app chmod -R 755 /var/www/html/storage
```

### Logs anzeigen

```bash
# Docker-Logs
docker logs timebank-app

# Laravel-Logs
docker exec -it timebank-app tail -f storage/logs/laravel.log
```

---

## Support

Bei Problemen:
1. Überprüfen Sie die Docker-Logs
2. Stellen Sie sicher, dass alle Ports verfügbar sind
3. Überprüfen Sie die .env-Konfiguration
4. Stellen Sie sicher, dass Docker auf Synology läuft
