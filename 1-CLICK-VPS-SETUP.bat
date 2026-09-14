@echo off
title 1-Click Earth Graduation Projects VPS Setup
color 0b
echo ==========================================================
echo   Running Earth Graduation Projects 1-Click VPS Setup...
echo ==========================================================
echo.
powershell -ExecutionPolicy Bypass -File "%~dp01-CLICK-VPS-SETUP.ps1"
echo.
pause
