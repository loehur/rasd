@echo off
echo ================================================
echo Staff Performance System - Pre-Deployment Check
echo ================================================
echo.

echo [1/8] Checking if .env exists...
if exist .env (
    echo ✓ .env file found
) else (
    echo ✗ ERROR: .env file not found!
    echo   Copy .env.example to .env and configure it
    pause
    exit /b 1
)

echo.
echo [2/8] Checking if vendor folder exists...
if exist vendor (
    echo ✓ Vendor folder found
) else (
    echo ✗ ERROR: Vendor folder not found!
    echo   Run: composer install --no-dev --optimize-autoloader
    pause
    exit /b 1
)

echo.
echo [3/8] Building frontend for production...
cd frontend
call npm run build
if %errorlevel% neq 0 (
    echo ✗ ERROR: Frontend build failed!
    pause
    exit /b 1
)
echo ✓ Frontend build successful

echo.
echo [4/8] Creating deployment package...
cd ..
if exist deployment rmdir /s /q deployment
mkdir deployment
mkdir deployment\public

echo.
echo [5/8] Copying backend files...
xcopy /E /I /Y app deployment\app > nul
xcopy /E /I /Y bootstrap deployment\bootstrap > nul
xcopy /E /I /Y database deployment\database > nul
xcopy /E /I /Y routes deployment\routes > nul
xcopy /E /I /Y vendor deployment\vendor > nul
xcopy /E /I /Y assets deployment\assets > nul
copy /Y .env deployment\.env > nul
copy /Y composer.json deployment\composer.json > nul
copy /Y artisan deployment\artisan > nul

echo.
echo [6/8] Copying frontend build files...
xcopy /E /I /Y frontend\dist\* deployment\public > nul

echo.
echo [7/8] Copying public folder files...
copy /Y public\.htaccess deployment\public\.htaccess > nul
copy /Y public\index.php deployment\public\index.php > nul

echo.
echo [8/8] Creating deployment archive...
powershell Compress-Archive -Path deployment\* -DestinationPath staff-performance-system.zip -Force

echo.
echo ================================================
echo ✓ Deployment package created successfully!
echo ================================================
echo.
echo File: staff-performance-system.zip
echo.
echo Next steps:
echo 1. Upload staff-performance-system.zip to your server
echo 2. Extract to your domain folder
echo 3. Point your domain to the 'public' folder
echo 4. Update .env with production database credentials
echo 5. Run: php artisan migrate --force
echo 6. Update production URL in .env: APP_URL=https://yourdomain.com
echo.
echo See DEPLOYMENT.md for detailed instructions
echo.
pause
