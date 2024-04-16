@echo off
cd %~dp0
php artisan serve --port=8002
start http://127.0.0.1:8002/
