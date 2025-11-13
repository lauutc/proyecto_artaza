@echo off
echo Iniciando servidor PHP en http://localhost:8000
echo.
echo Abriendo lauty_login.html como pagina principal...
echo.
cd /d "%~dp0"
php -S localhost:8000 router.php
pause

