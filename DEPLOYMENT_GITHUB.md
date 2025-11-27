# Deployment via GitHub dan VPS

## Setup Awal

### 1. Persiapan Repository GitHub

**Di Local Computer:**

```bash
# Jika belum init git
git init

# Tambahkan remote repository
git remote add origin https://github.com/username/repo-name.git

# Tambahkan semua file
git add .

# Commit
git commit -m "Initial commit - Staff Performance System"

# Push ke GitHub
git push -u origin main
```

### 2. Setup VPS (Pertama Kali)

**Login ke VPS via SSH:**

```bash
ssh user@your-vps-ip
```

**Install Dependencies:**

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install Nginx
sudo apt install nginx -y

# Install PHP 8.1 dan extensions
sudo apt install php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl -y

# Install MySQL
sudo apt install mysql-server -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js & NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

### 3. Clone Repository di VPS

```bash
# Masuk ke folder web
cd /var/www

# Clone repository
sudo git clone https://github.com/username/repo-name.git
sudo mv repo-name staff-performance

# Set permissions
sudo chown -R www-data:www-data staff-performance
sudo chmod -R 755 staff-performance
```

### 4. Setup Database di VPS

```bash
# Login ke MySQL
sudo mysql

# Buat database dan user
CREATE DATABASE staff_performance;
CREATE USER 'staff_user'@'localhost' IDENTIFIED BY 'password_anda';
GRANT ALL PRIVILEGES ON staff_performance.* TO 'staff_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Configure Environment

```bash
cd /var/www/staff-performance

# Copy .env dari example
cp .env.example .env

# Edit .env
nano .env
```

**Update .env dengan credentials VPS:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=staff_performance
DB_USERNAME=staff_user
DB_PASSWORD=password_anda
```

### 6. Install Dependencies & Build

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
cd frontend
npm install

# Build frontend untuk production
npm run build

# Kembali ke root
cd ..

# Set permissions untuk storage
chmod -R 775 storage bootstrap/cache
```

### 7. Run Database Migrations

```bash
php artisan migrate --force
```

### 8. Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/staff-performance
```

**Paste konfigurasi ini:**

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/staff-performance/public;

    index index.php index.html;

    # Logs
    access_log /var/log/nginx/staff-performance-access.log;
    error_log /var/log/nginx/staff-performance-error.log;

    # Serve static files
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

**Aktifkan site:**

```bash
# Buat symbolic link
sudo ln -s /etc/nginx/sites-available/staff-performance /etc/nginx/sites-enabled/

# Test konfigurasi
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### 9. Setup SSL (Opsional tapi Disarankan)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Generate SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Certbot akan otomatis update konfigurasi Nginx
```

---

## Update/Deploy Setelah Push Baru

Setiap kali ada perubahan code di local dan sudah di-push ke GitHub:

**Di Local Computer:**

```bash
# Commit perubahan
git add .
git commit -m "Deskripsi perubahan"
git push
```

**Di VPS (via SSH):**

```bash
# Masuk ke folder project
cd /var/www/staff-performance

# Pull perubahan terbaru
sudo git pull origin main

# Update dependencies jika ada perubahan composer.json
composer install --no-dev --optimize-autoloader

# Rebuild frontend jika ada perubahan
cd frontend
npm install
npm run build
cd ..

# Clear cache (jika perlu)
php artisan cache:clear

# Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

---

## Script Otomatis untuk Update

Buat script `deploy.sh` di VPS untuk memudahkan:

```bash
nano /var/www/staff-performance/deploy.sh
```

**Isi file:**

```bash
#!/bin/bash

echo "🚀 Starting deployment..."

# Pull latest code
echo "📥 Pulling latest code from GitHub..."
git pull origin main

# Update composer dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Build frontend
echo "🎨 Building frontend..."
cd frontend
npm install
npm run build
cd ..

# Clear cache
echo "🧹 Clearing cache..."
php artisan cache:clear

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Restart PHP-FPM
echo "🔄 Restarting PHP-FPM..."
sudo systemctl restart php8.1-fpm

echo "✅ Deployment completed successfully!"
```

**Buat executable:**

```bash
chmod +x /var/www/staff-performance/deploy.sh
```

**Cara pakai:**

```bash
cd /var/www/staff-performance
./deploy.sh
```

---

## Troubleshooting

### Error: Permission Denied saat git pull

```bash
sudo chown -R $USER:$USER /var/www/staff-performance
```

### Error: 502 Bad Gateway

```bash
# Check PHP-FPM status
sudo systemctl status php8.1-fpm

# Restart jika tidak running
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

### Error: Database Connection Failed

```bash
# Cek MySQL running
sudo systemctl status mysql

# Test koneksi database
mysql -u staff_user -p staff_performance
```

### Frontend tidak muncul setelah update

```bash
cd /var/www/staff-performance/frontend
npm run build
```

---

## Checklist Deployment

-   [ ] Repository sudah di-push ke GitHub
-   [ ] VPS sudah terinstall PHP, Nginx, MySQL, Composer, Node.js
-   [ ] Database sudah dibuat di VPS
-   [ ] Repository sudah di-clone ke /var/www/
-   [ ] File .env sudah dikonfigurasi dengan benar
-   [ ] Dependencies sudah terinstall (composer & npm)
-   [ ] Frontend sudah di-build (npm run build)
-   [ ] Migrations sudah dijalankan
-   [ ] Nginx sudah dikonfigurasi dan restart
-   [ ] SSL certificate sudah terinstall (opsional)
-   [ ] Domain sudah pointing ke IP VPS
-   [ ] Permissions storage/ sudah 775
-   [ ] Test login: 081234567890 / Admin132!
-   [ ] Ganti password default setelah deploy

---

## Default Credentials

**PENTING:** Ganti password ini setelah deployment pertama!

-   **Phone:** 081234567890
-   **Password:** Admin132!

---

## Monitoring & Maintenance

### Cek Logs Error

```bash
# Nginx error log
sudo tail -f /var/log/nginx/staff-performance-error.log

# PHP error log
sudo tail -f /var/log/php8.1-fpm.log

# Laravel log
tail -f /var/www/staff-performance/storage/logs/lumen.log
```

### Backup Database

```bash
# Backup manual
mysqldump -u staff_user -p staff_performance > backup-$(date +%Y%m%d).sql

# Restore
mysql -u staff_user -p staff_performance < backup-20250127.sql
```

### Auto Backup (Cronjob)

```bash
# Edit crontab
crontab -e

# Tambahkan baris ini untuk backup setiap hari jam 2 pagi
0 2 * * * mysqldump -u staff_user -p'password_anda' staff_performance > /var/backups/db-$(date +\%Y\%m\%d).sql
```

---

## Tips

1. **Gunakan SSH Key** untuk git pull tanpa password:

    ```bash
    ssh-keygen -t ed25519 -C "your_email@example.com"
    cat ~/.ssh/id_ed25519.pub
    # Copy output dan tambahkan ke GitHub Settings > SSH Keys
    ```

2. **Git Pull Otomatis via Webhook** (Advanced):
   Setup GitHub webhook untuk auto-deploy saat ada push

3. **Environment Variables**:
   Jangan pernah push file `.env` ke GitHub (sudah ada di .gitignore)

4. **Testing**:
   Selalu test di browser setelah deploy: `https://yourdomain.com/admin.html`

---

## Support

Jika ada masalah:

1. Cek nginx error log
2. Cek PHP-FPM status
3. Pastikan database credentials benar di .env
4. Pastikan permissions storage/ sudah 775
5. Restart semua service: nginx, php-fpm, mysql
