<#
.SYNOPSIS
    1-Click Automated Setup for Earth Graduation Projects Platform on Windows VPS
#>

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "   🚀 Earth Graduation Projects - Automated VPS Setup    " -ForegroundColor Yellow
Write-Host "==========================================================" -ForegroundColor Cyan

# 1. Open Windows Firewall for HTTP (Port 80) and HTTPS (Port 443)
Write-Host "`n[1/5] Configuring Windows Firewall for Port 80 & 443..." -ForegroundColor Green
New-NetFirewallRule -DisplayName "HTTP-Inbound-80" -Direction Inbound -LocalPort 80 -Protocol TCP -Action Allow -ErrorAction SilentlyContinue | Out-Null
New-NetFirewallRule -DisplayName "HTTPS-Inbound-443" -Direction Inbound -LocalPort 443 -Protocol TCP -Action Allow -ErrorAction SilentlyContinue | Out-Null
Write-Host "✔ Firewall rules created successfully!" -ForegroundColor Green

# 2. Setup Target Directory
$TargetDir = "C:\earth-grad-projects"
Write-Host "`n[2/5] Setting up project directory at $TargetDir..." -ForegroundColor Green
if (!(Test-Path $TargetDir)) {
    New-Item -ItemType Directory -Path $TargetDir -Force | Out-Null
}

# 3. Check for Git / Clone Repository
Write-Host "`n[3/5] Downloading latest project code from GitHub..." -ForegroundColor Green
$RepoUrl = "https://github.com/raheeqhassan907-cell/Graduation-project-legacy-hub.git"

if (Get-Command git -ErrorAction SilentlyContinue) {
    if (Test-Path "$TargetDir\.git") {
        Set-Location $TargetDir
        git pull origin main
    } else {
        git clone $RepoUrl $TargetDir
        Set-Location $TargetDir
    }
} else {
    Write-Host "Installing Git & PHP via winget/direct download..." -ForegroundColor Yellow
    # Download zip if git is not installed
    $ZipUrl = "https://github.com/raheeqhassan907-cell/Graduation-project-legacy-hub/archive/refs/heads/main.zip"
    $TempZip = "$env:TEMP\repo.zip"
    Invoke-WebRequest -Uri $ZipUrl -OutFile $TempZip
    Expand-Archive -Path $TempZip -DestinationPath "C:\temp_repo" -Force
    Copy-Item -Path "C:\temp_repo\Graduation-project-legacy-hub-main\*" -Destination $TargetDir -Recurse -Force
    Remove-Item "C:\temp_repo" -Recurse -Force -ErrorAction SilentlyContinue
    Set-Location $TargetDir
}

# 4. Check or Install PHP 8.3 & Composer if not present
Write-Host "`n[4/5] Checking PHP 8.3 runtime..." -ForegroundColor Green
if (!(Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "Downloading portable PHP 8.3 for Windows..." -ForegroundColor Yellow
    $PhpZip = "$env:TEMP\php8.zip"
    $PhpDir = "C:\php"
    if (!(Test-Path $PhpDir)) { New-Item -ItemType Directory -Path $PhpDir -Force | Out-Null }
    Invoke-WebRequest -Uri "https://windows.php.net/downloads/releases/archives/php-8.3.0-Win32-vs16-x64.zip" -OutFile $PhpZip -ErrorAction SilentlyContinue
    if (Test-Path $PhpZip) {
        Expand-Archive -Path $PhpZip -DestinationPath $PhpDir -Force
        Copy-Item "$PhpDir\php.ini-production" "$PhpDir\php.ini" -Force
        Add-Content "$PhpDir\php.ini" "`nextension_dir = 'C:\php\ext'`nextension=pdo_sqlite`nextension=sqlite3`nextension=mbstring`nextension=openssl`nextension=curl`nextension=fileinfo`nextension=gd`n"
        [Environment]::SetEnvironmentVariable("Path", $env:Path + ";$PhpDir", [EnvironmentVariableTarget]::Machine)
        $env:Path += ";$PhpDir"
    }
}

# 5. Launch Laravel Web Server as a 24/7 Background Service
Write-Host "`n[5/5] Starting Earth Graduation Projects Web Server on Port 80..." -ForegroundColor Green

# Create startup batch script
$StartScript = "$TargetDir\run-server.bat"
@"
@echo off
cd /d "$TargetDir"
title Earth Graduation Projects - Live Server
:loop
echo Starting Laravel Server on port 80...
php artisan serve --host=0.0.0.0 --port=80
echo Server stopped. Restarting in 3 seconds...
timeout /t 3
goto loop
"@ | Out-File -FilePath $StartScript -Encoding ASCII

# Register Task in Windows Task Scheduler to start automatically on system boot
$Action = New-ScheduledTaskAction -Execute $StartScript
$Trigger = New-ScheduledTaskTrigger -AtStartup
$Principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
Register-ScheduledTask -TaskName "EarthGradProjectsServer" -Action $Action -Trigger $Trigger -Principal $Principal -Force -ErrorAction SilentlyContinue | Out-Null

# Start immediately in a background window
Start-Process "cmd.exe" -ArgumentList "/c `"$StartScript`"" -WindowStyle Minimized

Write-Host "`n==========================================================" -ForegroundColor Cyan
Write-Host "  🎉 SUCCESS! The Platform is now LIVE online!          " -ForegroundColor Green
Write-Host "  👉 Access URL: http://141.140.0.187                   " -ForegroundColor Yellow
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "You can now disconnect from RDP and the site will stay running 24/7." -ForegroundColor White
