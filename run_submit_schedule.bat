@echo off
set LOGFILE=C:\path\to\your\log\send_sms_log.txt

echo Running submit_schedule.php at %date% %time% >> %LOGFILE%
"C:\xampp\php\php.exe" "C:\xampp\htdocs\wbhr_ms\submit_schedule.php" >> %LOGFILE% 2>&1

echo Finished running submit_schedule.php at %date% %time% >> %LOGFILE%
