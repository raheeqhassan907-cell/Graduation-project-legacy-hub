@echo off
echo ===================================================
echo   Starting Earth Graduation Projects in Docker...
echo ===================================================
docker compose down
docker compose up -d --build
echo.
echo Application is running at: http://localhost:80
echo Or at your Server IP: http://141.140.0.187:80
echo ===================================================
pause
