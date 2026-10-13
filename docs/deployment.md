# Production Deployment Guide — House Price Predictor

This guide covers complete production deployment of the **House Price Predictor** platform on a standard Linux (Ubuntu 22.04/24.04 LTS) server with Nginx, PHP 8.3-FPM, MySQL 8.0, and Supervisor.

---

## 1. System Requirements

* **Operating System**: Ubuntu 22.04 or 24.04 LTS
* **Web Server**: Nginx
* **PHP**: 8.3+ with `php8.3-fpm`, `php8.3-mysql`, `php8.3-sqlite3`, `php8.3-curl`, `php8.3-mbstring`, `php8.3-xml`, `php8.3-zip`, `php8.3-gd`
* **Composer**: 2.7+
* **Node.js**: Node 18+ & NPM
* **Python**: 3.11+ & `python3-venv`, `python3-pip`
* **Process Manager**: Supervisor
* **Database**: MySQL 8.0+ or SQLite 3

---

## 2. Server Installation

```bash
# Update package lists
sudo apt update && sudo apt upgrade -y

# Install Nginx, Git, Supervisor, Unzip
sudo apt install -y nginx git supervisor unzip curl software-properties-common

# Add PHP PPA and install PHP 8.3 & extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-sqlite3 php8.3-curl \
    php8.3-mbstring php8.3-xml php8.3-zip php8.3-gd php8.3-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Python 3 and venv
sudo apt install -y python3 python3-pip python3-venv
```

---

## 3. Application Setup

```bash
# Clone repository to web root
sudo mkdir -p /var/www/houseprice
sudo chown -R $USER:$USER /var/www/houseprice
git clone <repository_url> /var/www/houseprice

# Setup Laravel Backend
cd /var/www/houseprice/laravel-app
composer install --no-dev --optimize-autoloader
cp .env.example .env

# Configure .env with production settings
nano .env
# Set APP_ENV=production, APP_DEBUG=false, DB_CONNECTION=mysql (or sqlite)
# Set ML_SERVICE_URL=http://127.0.0.1:8001

php artisan key:generate
php artisan migrate --force --seed
npm install
npm run build

# Cache configuration, routes, and views for maximum performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/houseprice/laravel-app/storage /var/www/houseprice/laravel-app/bootstrap/cache
sudo chmod -R 775 /var/www/houseprice/laravel-app/storage /var/www/houseprice/laravel-app/bootstrap/cache
```

---

## 4. Python ML Microservice Setup

```bash
cd /var/www/houseprice/ml-service
python3 -m venv venv
source venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt

# Run training pipeline to verify model creation
python training/train.py

# Ensure models directory is writable
sudo chown -R www-data:www-data /var/www/houseprice/ml-service
```

---

## 5. Supervisor Configuration for ML Service

Create `/etc/supervisor/conf.d/houseprice-ml.conf`:
```ini
[program:houseprice-ml]
command=/var/www/houseprice/ml-service/venv/bin/uvicorn app.main:app --host 127.0.0.1 --port 8001 --workers 4
directory=/var/www/houseprice/ml-service
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/houseprice-ml.log
environment=PATH="/var/www/houseprice/ml-service/venv/bin"
```

Activate Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

---

## 6. Nginx Virtual Host Configuration

Create `/etc/nginx/sites-available/houseprice.conf`:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/houseprice/laravel-app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/houseprice.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 7. SSL Certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

---

## 8. Health Check Verification

* Check Laravel: `curl -I https://your-domain.com`
* Check API: `curl https://your-domain.com/api/health`
* Check ML Service: `curl http://127.0.0.1:8001/health`
