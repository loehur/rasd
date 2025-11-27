# Staff Performance System - Deployment Guide

## Prerequisites

-   PHP 7.4+ with required extensions (mbstring, pdo_mysql, openssl)
-   MySQL 5.7+ or MariaDB
-   Composer
-   Node.js & NPM (for building frontend)
-   Web server (Apache/Nginx)

## Deployment Steps

### 1. Upload Files to Server

Upload these folders to your web hosting:

```
your-domain.com/
├── public/           (Laravel public folder - point your domain here)
├── app/
├── bootstrap/
├── database/
├── routes/
├── vendor/
├── assets/
├── .env
└── composer.json
```

**IMPORTANT:** Your domain should point to the `public/` folder, NOT the root!

### 2. Configure Database

Create a database and user in cPanel/phpMyAdmin, then update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 3. Install Dependencies

SSH to your server and run:

```bash
cd /path/to/your/project
composer install --no-dev --optimize-autoloader
```

### 4. Set Permissions

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 5. Run Database Migrations

```bash
php artisan migrate --force
```

### 6. Build and Deploy Frontend

On your local machine:

```bash
cd frontend
npm install
npm run build
```

This creates a `dist/` folder. Upload the contents of `dist/` to your server's `public/` folder:

-   `dist/index.html` → `public/index.html`
-   `dist/public/*.html` → `public/*.html`
-   `dist/assets/*` → `public/assets/*`

### 7. Configure Web Server

#### Apache (.htaccess already included in public/)

Make sure mod_rewrite is enabled.

#### Nginx

Add this to your nginx config:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/your/project/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8. Update API Endpoints in Frontend

Before building, update all API URLs in Vue components from:

-   `http://localhost:8000/api/...`
    to:
-   `https://yourdomain.com/api/...`

Files to update:

-   `frontend/src/components/LoginApp.vue`
-   `frontend/src/components/ImportStaff.vue`
-   `frontend/src/components/StaffList.vue`
-   `frontend/src/components/AccountPage.vue`
-   `frontend/src/components/ChangePasswordPage.vue`

### 9. Security Checklist

-   [ ] Set `APP_DEBUG=false` in `.env`
-   [ ] Set `APP_ENV=production` in `.env`
-   [ ] Remove `.env.example` from production
-   [ ] Ensure `.htaccess` prevents access to sensitive files
-   [ ] Use HTTPS (SSL certificate)
-   [ ] Change default admin password
-   [ ] Set proper file permissions (755 for folders, 644 for files)

## Post-Deployment

### Access the Application

-   Homepage: `https://yourdomain.com`
-   Admin Login: `https://yourdomain.com/admin.html`

### Default Credentials

-   Phone: 081234567890
-   Password: Admin132!

**Change immediately after first login!**

## Troubleshooting

### 500 Internal Server Error

-   Check storage and cache permissions
-   Check .htaccess file exists in public/
-   Enable error logging in php.ini
-   Check Apache/Nginx error logs

### API Not Working

-   Verify .htaccess mod_rewrite is enabled
-   Check that domain points to public/ folder
-   Verify database credentials in .env
-   Check PHP error logs

### Frontend Not Loading

-   Clear browser cache
-   Check that assets/ folder is uploaded correctly
-   Verify all .html files are in public/
-   Check browser console for errors

## Maintenance

### Update Staff Data

Upload CSV via Import Staff page

### Backup Database

```bash
mysqldump -u username -p database_name > backup.sql
```

### View Logs

Check `storage/logs/lumen.log` for errors
