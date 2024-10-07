@echo off
REM Check if the script is already running
tasklist /FI "IMAGENAME eq php.exe" | find /I "php.exe" >nul
if not errorlevel 1 (
    echo PHP script is already running.
    exit /b
)

REM Log the start time
echo %date% %time% - Starting send_reminders.php >> C:\xampp\htdocs\WBHR_MS\send_reminders_log.txt

REM Execute the PHP script
"C:\xampp\php\php.exe" "C:\xampp\htdocs\WBHR_MS\send_reminders.php" >> "C:\xampp\htdocs\WBHR_MS\send_reminders_log.txt" 2>&1

REM Log the end time
echo %date% %time% - Finished send_reminders.php >> C:\xampp\htdocs\WBHR_MS\send_reminders_log.txt

REM Pause to view any messages (optional, can be removed for Task Scheduler)
pause
