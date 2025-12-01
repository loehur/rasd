# Debugging Steps untuk VPS

## 1. Clear Route Cache (jika menggunakan cache)
```bash
cd /path/to/sd_pro
php artisan route:clear
# atau jika menggunakan Lumen tanpa artisan
rm -f bootstrap/cache/routes.php
```

## 2. Restart Apache/Nginx
```bash
# Untuk Apache
sudo service apache2 restart

# Untuk Nginx
sudo service nginx restart
sudo service php7.4-fpm restart  # sesuaikan versi PHP
```

## 3. Check Logs
```bash
# Cek Laravel/Lumen logs
tail -f storage/logs/lumen.log

# Cek Apache error logs
tail -f /var/log/apache2/error.log

# Cek Nginx error logs
tail -f /var/log/nginx/error.log
```

## 4. Test Endpoint Langsung di VPS
```bash
curl -X POST https://rasd.nalju.com/api/team-leaders/resign \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "X-Role: admin" \
  -d '{"resigning_tl_id":"TL001","replacement_tl_id":"TL002"}'
```

## 5. Verify Route Exists
```bash
# Check if route is registered
grep -r "team-leaders/resign" routes/web.php
```

## 6. File Permissions
```bash
# Make sure storage is writable
chmod -R 775 storage
chown -R www-data:www-data storage  # for Apache
# or
chown -R nginx:nginx storage  # for Nginx
```

## 7. Check .htaccess (untuk Apache)
Pastikan file `.htaccess` ada di folder `public/`:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## Expected Responses

### Success Response:
```json
{
  "success": true,
  "message": "Successfully transferred X staff member(s) from TL A to TL B",
  "transferred_count": X
}
```

### Error Response:
```json
{
  "success": false,
  "message": "Error description",
  "error_details": {
    "file": "filename.php",
    "line": 123
  }
}
```

## Upload Files yang Diubah ke VPS

File yang perlu di-upload:
1. `app/Http/Controllers/TeamLeaderController.php` (sudah diperbaiki dengan try-catch)
2. `routes/web.php` (pastikan route resign ada)

Setelah upload, restart server!
