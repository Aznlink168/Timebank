#!/bin/bash

# Timebank Synology Sync Script
# Quick synchronization to Synology NAS
# Schnelle Synchronisation zu Synology NAS

# ====================================
# CONFIGURATION / KONFIGURATION
# ====================================

# Edit these values / Diese Werte bearbeiten:
SYNOLOGY_USER="your-username"           # Your Synology username / Ihr Synology-Benutzername
SYNOLOGY_IP="192.168.1.100"             # Your Synology IP address / Ihre Synology-IP-Adresse
SYNOLOGY_PATH="/volume1/docker/Timebank" # Destination path on Synology / Zielpfad auf Synology
LOCAL_PATH="."                           # Local project path / Lokaler Projektpfad

# ====================================
# SCRIPT - DO NOT EDIT BELOW
# ====================================

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   Timebank → Synology Sync Script         ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════╝${NC}"
echo ""

# Check if configuration has been updated
if [ "$SYNOLOGY_USER" = "your-username" ] || [ "$SYNOLOGY_IP" = "192.168.1.100" ]; then
    echo -e "${RED}⚠️  Please configure this script first!${NC}"
    echo -e "${YELLOW}Edit the SYNOLOGY_USER and SYNOLOGY_IP variables at the top of this file.${NC}"
    echo ""
    echo -e "${YELLOW}Bitte konfigurieren Sie dieses Skript zuerst!${NC}"
    echo -e "${YELLOW}Bearbeiten Sie die Variablen SYNOLOGY_USER und SYNOLOGY_IP am Anfang dieser Datei.${NC}"
    exit 1
fi

# Check if source directory exists
if [ ! -d "$LOCAL_PATH/timebank" ]; then
    echo -e "${RED}Error: timebank directory not found!${NC}"
    echo -e "${RED}Fehler: timebank Verzeichnis nicht gefunden!${NC}"
    exit 1
fi

echo -e "${BLUE}📋 Configuration / Konfiguration:${NC}"
echo -e "   Source / Quelle:      $LOCAL_PATH"
echo -e "   Destination / Ziel:   $SYNOLOGY_USER@$SYNOLOGY_IP:$SYNOLOGY_PATH"
echo ""

# Test SSH connection
echo -e "${BLUE}🔍 Testing SSH connection / SSH-Verbindung testen...${NC}"
if ! ssh -o ConnectTimeout=5 -o BatchMode=yes $SYNOLOGY_USER@$SYNOLOGY_IP "echo 2>&1" &>/dev/null; then
    echo -e "${YELLOW}⚠️  SSH connection test failed. You may need to enter your password.${NC}"
    echo -e "${YELLOW}⚠️  SSH-Verbindungstest fehlgeschlagen. Möglicherweise müssen Sie Ihr Passwort eingeben.${NC}"
    echo ""
else
    echo -e "${GREEN}✓ SSH connection successful${NC}"
    echo ""
fi

# Confirm before syncing
echo -e "${YELLOW}Ready to sync. Continue? (y/n) / Bereit zum Synchronisieren. Fortfahren? (j/n)${NC}"
read -r response
if [[ ! "$response" =~ ^([yYjJ])$ ]]; then
    echo -e "${RED}Sync cancelled / Synchronisation abgebrochen${NC}"
    exit 0
fi

echo ""
echo -e "${BLUE}🚀 Starting sync / Synchronisation wird gestartet...${NC}"
echo ""

# Sync files with rsync
rsync -avz --progress \
  --exclude 'node_modules' \
  --exclude 'vendor' \
  --exclude '.git' \
  --exclude '.env' \
  --exclude 'storage/logs/*' \
  --exclude 'storage/framework/cache/data/*' \
  --exclude 'storage/framework/sessions/*' \
  --exclude 'storage/framework/views/*' \
  --exclude '.phpunit.result.cache' \
  --exclude 'database/database.sqlite' \
  --exclude 'public/build' \
  --exclude 'public/hot' \
  $LOCAL_PATH/ $SYNOLOGY_USER@$SYNOLOGY_IP:$SYNOLOGY_PATH/

# Check if sync was successful
if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✓ Sync completed successfully! / Synchronisation erfolgreich abgeschlossen!${NC}"
    echo ""
    
    # Ask if user wants to install dependencies
    echo -e "${YELLOW}Install dependencies on Synology? (y/n) / Abhängigkeiten auf Synology installieren? (j/n)${NC}"
    read -r install_deps
    
    if [[ "$install_deps" =~ ^([yYjJ])$ ]]; then
        echo ""
        echo -e "${BLUE}📦 Installing PHP dependencies / PHP-Abhängigkeiten installieren...${NC}"
        ssh $SYNOLOGY_USER@$SYNOLOGY_IP "cd $SYNOLOGY_PATH/timebank && composer install --no-dev --optimize-autoloader"
        
        echo ""
        echo -e "${BLUE}📦 Installing Node dependencies / Node-Abhängigkeiten installieren...${NC}"
        ssh $SYNOLOGY_USER@$SYNOLOGY_IP "cd $SYNOLOGY_PATH/timebank && npm install"
        
        echo ""
        echo -e "${BLUE}🔨 Building assets / Assets erstellen...${NC}"
        ssh $SYNOLOGY_USER@$SYNOLOGY_IP "cd $SYNOLOGY_PATH/timebank && npm run build"
        
        echo ""
        echo -e "${GREEN}✓ Dependencies installed! / Abhängigkeiten installiert!${NC}"
    fi
    
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║   🎉 All done! / Alles erledigt!          ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${BLUE}Next steps / Nächste Schritte:${NC}"
    echo -e "1. SSH to Synology: ${YELLOW}ssh $SYNOLOGY_USER@$SYNOLOGY_IP${NC}"
    echo -e "2. Go to project: ${YELLOW}cd $SYNOLOGY_PATH${NC}"
    echo -e "3. Start Docker: ${YELLOW}docker-compose up -d${NC}"
    echo ""
    
else
    echo ""
    echo -e "${RED}✗ Sync failed! / Synchronisation fehlgeschlagen!${NC}"
    echo -e "${YELLOW}Check your network connection and SSH credentials.${NC}"
    echo -e "${YELLOW}Überprüfen Sie Ihre Netzwerkverbindung und SSH-Anmeldedaten.${NC}"
    exit 1
fi
