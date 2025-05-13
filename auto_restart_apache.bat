@echo off
setlocal

REM 检查 80 端口是否被占用并全部结束
for /f "tokens=5" %%a in ('netstat -ano ^| find ":80" ^| find "LISTENING"') do (
    echo 发现占用 80 端口的进程，PID: %%a
    taskkill /PID %%a /F
)

REM 重启 Apache 服务
echo 正在重启 Apache 服务...
cd /d C:\xampp
apache_stop.bat
timeout /t 2
apache_start.bat

echo.
echo 操作完成，请检查 XAMPP 控制面板。
pause
endlocal
