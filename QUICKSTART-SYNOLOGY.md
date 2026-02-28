# Quick Start Guide - Synology Deployment
# Schnellstart-Anleitung - Synology Bereitstellung

This is a visual walkthrough for deploying Timebank to Synology NAS.
Dies ist eine visuelle Anleitung zur Bereitstellung von Timebank auf Synology NAS.

## 🎯 Deployment Flow / Bereitstellungsablauf

```
┌─────────────────────────────────────────────────────────────────┐
│  1. Local Machine / Lokaler Computer                            │
│     └─ Clone Repository                                          │
│        git clone https://github.com/Aznlink168/Timebank.git     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  2. Transfer to Synology / Übertragung zu Synology              │
│     Choose one / Eine wählen:                                    │
│     ├─ Option A: rsync (Fast / Schnell)                         │
│     │  ./sync-to-synology.sh                                    │
│     ├─ Option B: Git (Direct / Direkt)                          │
│     │  ssh + git clone on Synology                              │
│     └─ Option C: File Station (GUI)                             │
│        Upload via DSM web interface                              │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  3. Synology NAS                                                 │
│     ├─ Configure .env file                                       │
│     ├─ docker-compose up -d --build                             │
│     └─ docker exec ... php artisan migrate                      │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  ✅ Application Running / Anwendung läuft                       │
│     http://synology-ip:8080                                      │
└─────────────────────────────────────────────────────────────────┘
```

## 🎯 Three Easy Steps / Drei einfache Schritte

### Step 1: Clone Repository / Repository klonen

```bash
git clone https://github.com/Aznlink168/Timebank.git
cd Timebank
```

### Step 2: Choose Your Method / Methode wählen

#### 🐳 Option A: Docker (Recommended / Empfohlen)

**On your local machine / Auf Ihrem lokalen Computer:**

1. Configure sync script:
```bash
nano sync-to-synology.sh
# Edit these lines / Diese Zeilen bearbeiten:
SYNOLOGY_USER="your-username"
SYNOLOGY_IP="192.168.1.100"
```

2. Sync to Synology:
```bash
./sync-to-synology.sh
```

3. SSH to Synology and start Docker:
```bash
ssh your-username@synology-ip
cd /volume1/docker/Timebank
docker-compose up -d --build
```

**Expected output / Erwartete Ausgabe:**
```
Creating network "timebank-network"
Building timebank
Successfully built abc123def456
Creating timebank-app ... done
```

#### 📦 Option B: Direct Git on Synology

**SSH to your Synology / SSH zu Ihrer Synology:**

```bash
ssh your-username@synology-ip

# Navigate to docker folder
cd /volume1/docker

# Clone repository
git clone https://github.com/Aznlink168/Timebank.git
cd Timebank

# Configure environment
cd timebank
cp .env.example .env
nano .env  # Edit configuration

# Return to root and start Docker
cd ..
docker-compose up -d --build
```

### Step 3: Access Application / Anwendung aufrufen

Open browser / Browser öffnen:
```
http://your-synology-ip:8080
```

## 📋 Post-Installation / Nach der Installation

### Run Migrations / Migrationen ausführen

```bash
docker exec -it timebank-app php artisan migrate --force
```

### Create Admin User / Admin-Benutzer erstellen

```bash
docker exec -it timebank-app php artisan tinker
```

Then in tinker / Dann in tinker:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = Hash::make('your-secure-password');
$user->is_admin = true;
$user->save();
```

### Check Logs / Logs prüfen

```bash
# Docker logs
docker logs timebank-app

# Laravel logs
docker exec -it timebank-app tail -f storage/logs/laravel.log
```

## 🔧 Common Tasks / Häufige Aufgaben

### Update Application / Anwendung aktualisieren

```bash
cd /volume1/docker/Timebank
git pull
docker-compose down
docker-compose up -d --build
docker exec -it timebank-app php artisan migrate --force
```

### Restart Containers / Container neu starten

```bash
docker-compose restart
```

### Stop Application / Anwendung stoppen

```bash
docker-compose down
```

### View Running Containers / Laufende Container anzeigen

```bash
docker ps
```

## 🎨 Customization / Anpassung

### Change Port / Port ändern

Edit `docker-compose.yml`:
```yaml
ports:
  - "8081:80"  # Change 8080 to your preferred port
```

### Use MySQL Instead of SQLite

1. Uncomment MySQL service in `docker-compose.yml`
2. Update `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=timebank
DB_USERNAME=timebank
DB_PASSWORD=changeme
```

## 🆘 Troubleshooting / Fehlerbehebung

### Container Won't Start / Container startet nicht

```bash
# Check logs
docker logs timebank-app

# Rebuild without cache
docker-compose build --no-cache
docker-compose up -d
```

### Permission Errors / Berechtigungsfehler

```bash
docker exec -it timebank-app chown -R www-data:www-data /var/www/html/storage
docker exec -it timebank-app chmod -R 755 /var/www/html/storage
```

### Port Already in Use / Port bereits verwendet

```bash
# Check what's using the port
sudo netstat -tulpn | grep 8080

# Or change the port in docker-compose.yml
```

## 📚 Additional Resources / Zusätzliche Ressourcen

- Full Documentation: [SYNOLOGY-DEPLOYMENT.md](SYNOLOGY-DEPLOYMENT.md)
- Setup Guide: [SETUP.md](SETUP.md)
- GitHub Issues: Report problems at https://github.com/Aznlink168/Timebank/issues

## ✅ Verification Checklist / Überprüfungs-Checkliste

After deployment, verify / Nach der Bereitstellung überprüfen:

- [ ] Application accessible at http://synology-ip:8080
- [ ] Can register new user
- [ ] Can login successfully
- [ ] Database migrations completed
- [ ] Assets loading correctly (CSS/JS)
- [ ] Docker container running: `docker ps | grep timebank-app`

## 🎉 Success! / Erfolg!

You now have Timebank running on your Synology NAS!
Sie haben jetzt Timebank auf Ihrer Synology NAS laufen!

Access it at: `http://your-synology-ip:8080`
Zugriff unter: `http://ihre-synology-ip:8080`
