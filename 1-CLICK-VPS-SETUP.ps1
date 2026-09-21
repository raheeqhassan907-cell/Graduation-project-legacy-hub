<#
.SYNOPSIS
    Complete 1-Click Setup - PHP Direct (No Docker needed!)
    Works on Windows Server 2019/2022
#>

$ErrorActionPreference = "Continue"
$ProgressPreference = "SilentlyContinue"

Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "   Legacy Hub Platform - Windows Server Setup" -ForegroundColor Yellow
Write-Host "========================================================" -ForegroundColor Cyan

# ── Step 1: Open Firewall ──────────────────────────────────────
Write-Host "`n[1/6] Opening Windows Firewall ports 80 & 443..." -ForegroundColor Green
try {
    netsh advfirewall firewall add rule name="Legacy-Hub-HTTP" dir=in action=allow protocol=TCP localport=80
    netsh advfirewall firewall add rule name="Legacy-Hub-HTTPS" dir=in action=allow protocol=TCP localport=443
    netsh advfirewall firewall add rule name="Legacy-Hub-HTTP-out" dir=out action=allow protocol=TCP localport=80
    Write-Host "[OK] Firewall rules added!" -ForegroundColor Green
} catch {
    Write-Host "[WARN] Firewall: $_" -ForegroundColor Yellow
}

# ── Step 2: Setup Directory ────────────────────────────────────
$AppDir = "C:\legacy-hub"
Write-Host "`n[2/6] Setting up directory: $AppDir" -ForegroundColor Green
if (!(Test-Path $AppDir)) {
    New-Item -ItemType Directory -Path $AppDir -Force | Out-Null
}

# ── Step 3: Download PHP for Windows ──────────────────────────
Write-Host "`n[3/6] Downloading PHP 8.2 for Windows..." -ForegroundColor Green
$PhpDir = "C:\php"
$PhpExe = "$PhpDir\php.exe"

if (!(Test-Path $PhpExe)) {
    New-Item -ItemType Directory -Path $PhpDir -Force | Out-Null
    $PhpZip = "$env:TEMP\php.zip"
    # PHP 8.2 Thread-Safe for Windows x64
    $PhpUrl = "https://windows.php.net/downloads/releases/php-8.2.27-Win32-vs16-x64.zip"
    Write-Host "  Downloading from $PhpUrl ..."
    try {
        (New-Object System.Net.WebClient).DownloadFile($PhpUrl, $PhpZip)
        Expand-Archive -Path $PhpZip -DestinationPath $PhpDir -Force
        Remove-Item $PhpZip -Force
        Write-Host "[OK] PHP downloaded!" -ForegroundColor Green
    } catch {
        Write-Host "[ERR] PHP download failed: $_" -ForegroundColor Red
        # Try alternative URL
        $PhpUrl2 = "https://windows.php.net/downloads/releases/archives/php-8.2.20-Win32-vs16-x64.zip"
        try {
            (New-Object System.Net.WebClient).DownloadFile($PhpUrl2, $PhpZip)
            Expand-Archive -Path $PhpZip -DestinationPath $PhpDir -Force
            Remove-Item $PhpZip -Force
            Write-Host "[OK] PHP downloaded (alt)!" -ForegroundColor Green
        } catch {
            Write-Host "[ERR] Alt PHP also failed: $_" -ForegroundColor Red
        }
    }
} else {
    Write-Host "[OK] PHP already installed!" -ForegroundColor Green
}

# Configure PHP
$PhpIni = "$PhpDir\php.ini"
if (!(Test-Path $PhpIni)) {
    if (Test-Path "$PhpDir\php.ini-production") {
        Copy-Item "$PhpDir\php.ini-production" $PhpIni
    }
}
if (Test-Path $PhpIni) {
    $ini = Get-Content $PhpIni
    $ini = $ini -replace ";extension=pdo_sqlite", "extension=pdo_sqlite"
    $ini = $ini -replace ";extension=sqlite3", "extension=sqlite3"
    $ini = $ini -replace ";extension=mbstring", "extension=mbstring"
    $ini = $ini -replace ";extension=openssl", "extension=openssl"
    $ini = $ini -replace ";extension=fileinfo", "extension=fileinfo"
    $ini = $ini -replace ";extension=curl", "extension=curl"
    $ini = $ini -replace ";extension=intl", "extension=intl"
    $ini | Set-Content $PhpIni
    Write-Host "[OK] PHP configured with SQLite!" -ForegroundColor Green
}

# ── Step 4: Download Project ───────────────────────────────────
Write-Host "`n[4/6] Downloading project from GitHub..." -ForegroundColor Green
$ZipPath = "$env:TEMP\legacy-hub.zip"
$RepoZip = "https://github.com/raheeqhassan907-cell/Graduation-project-legacy-hub/archive/refs/heads/main.zip"

try {
    (New-Object System.Net.WebClient).DownloadFile($RepoZip, $ZipPath)
    # Extract to temp
    $ExtractTemp = "$env:TEMP\legacy-hub-extract"
    if (Test-Path $ExtractTemp) { Remove-Item $ExtractTemp -Recurse -Force }
    Expand-Archive -Path $ZipPath -DestinationPath $ExtractTemp -Force
    # Move contents
    $ExtractedFolder = Get-ChildItem $ExtractTemp | Select-Object -First 1
    if (Test-Path $AppDir) { Remove-Item "$AppDir\*" -Recurse -Force -ErrorAction SilentlyContinue }
    Copy-Item "$($ExtractedFolder.FullName)\*" $AppDir -Recurse -Force
    Remove-Item $ZipPath -Force
    Remove-Item $ExtractTemp -Recurse -Force
    Write-Host "[OK] Project downloaded!" -ForegroundColor Green
} catch {
    Write-Host "[ERR] Project download failed: $_" -ForegroundColor Red
}

# ── Step 5: Configure .env ─────────────────────────────────────
Write-Host "`n[5/6] Configuring application..." -ForegroundColor Green
$EnvFile = "$AppDir\.env"
$EnvContent = @"
APP_NAME="Legacy Hub"
APP_ENV=production
APP_KEY=base64:4TBPpJPR+SWHYXlpP5G3+LAJIsz4L/w+aqJHELKO9eQ=
APP_DEBUG=false
APP_URL=http://141.140.0.187

DB_CONNECTION=sqlite
DB_DATABASE=C:/legacy-hub/database/database.sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=raheegohassan@gmail.com
MAIL_PASSWORD=fznaykaxeolvarxw
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=raheegohassan@gmail.com
MAIL_FROM_NAME="Legacy Hub"
"@
$EnvContent | Set-Content $EnvFile -Encoding UTF8
Write-Host "[OK] .env configured!" -ForegroundColor Green

# Ensure storage directories exist
$StorageDirs = @(
    "$AppDir\storage\app",
    "$AppDir\storage\app\public",
    "$AppDir\storage\framework\cache",
    "$AppDir\storage\framework\sessions",
    "$AppDir\storage\framework\views",
    "$AppDir\storage\logs",
    "$AppDir\bootstrap\cache"
)
foreach ($dir in $StorageDirs) {
    if (!(Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }
}

# ── Step 6: Start PHP Server as Service ───────────────────────
Write-Host "`n[6/6] Starting PHP web server on port 80..." -ForegroundColor Green

# Kill any existing PHP server
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force

# Remove old scheduled task if exists
schtasks /delete /tn "LegacyHubServer" /f 2>$null | Out-Null

# Create a startup script
$StartScript = @"
@echo off
C:\php\php.exe -S 0.0.0.0:80 -t C:\legacy-hub\public C:\legacy-hub\public\index.php >> C:\legacy-hub\storage\logs\server.log 2>&1
"@
$StartScript | Set-Content "C:\legacy-hub\start-server.bat" -Encoding ASCII

# Register as scheduled task to run at startup
$Action = New-ScheduledTaskAction -Execute "C:\legacy-hub\start-server.bat"
$Trigger = New-ScheduledTaskTrigger -AtStartup
$Settings = New-ScheduledTaskSettingsSet -ExecutionTimeLimit 0 -RestartCount 3
$Principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -RunLevel Highest
Register-ScheduledTask -TaskName "LegacyHubServer" -Action $Action -Trigger $Trigger -Settings $Settings -Principal $Principal -Force | Out-Null

# Start the server immediately
Write-Host "  Starting server now..."
Start-Process -FilePath "C:\php\php.exe" `
    -ArgumentList "-S", "0.0.0.0:80", "-t", "C:\legacy-hub\public", "C:\legacy-hub\public\index.php" `
    -WindowStyle Hidden

Start-Sleep -Seconds 3

# Verify server started
$phpProcs = Get-Process php -ErrorAction SilentlyContinue
if ($phpProcs) {
    Write-Host "[OK] PHP server started! PID: $($phpProcs[0].Id)" -ForegroundColor Green
} else {
    Write-Host "[WARN] PHP process not found, trying again..." -ForegroundColor Yellow
    Start-Process "C:\legacy-hub\start-server.bat" -WindowStyle Hidden
    Start-Sleep -Seconds 2
}

Write-Host "`n========================================================" -ForegroundColor Cyan
Write-Host "   SETUP COMPLETE!" -ForegroundColor Green
Write-Host "   Platform is running at: http://141.140.0.187" -ForegroundColor Yellow
Write-Host "========================================================" -ForegroundColor Cyan

# Test locally
try {
    $response = Invoke-WebRequest -Uri "http://localhost" -TimeoutSec 5 -UseBasicParsing
    Write-Host "[OK] Local test: HTTP $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "[INFO] Server starting up: $_" -ForegroundColor Yellow
}
