@echo off
chcp 65001 >nul
setlocal
title Push - Sekolahin

set GIT_USER=Harry Prasetyo
set GIT_EMAIL=zaphkiela56@gmail.com

git config user.name "%GIT_USER%"
git config user.email "%GIT_EMAIL%"

git add -A
echo.
echo ==== File yang akan di-commit ====
git status --short
echo.

git diff --cached --quiet
if %errorlevel% equ 0 (
    echo Tidak ada perubahan untuk di-commit.
    pause
    exit /b 0
)

set /p MSG=Commit message (Enter untuk default "Update"): 
if "%MSG%"=="" set MSG=Update

git commit --author="%GIT_USER% <%GIT_EMAIL%>" -m "%MSG%"
if %errorlevel% neq 0 goto :error

git push origin master
if %errorlevel% neq 0 goto :error

echo.
echo ==== Push berhasil ke https://github.com/rry69/Sekolahin.git ====
pause
exit /b 0

:error
echo.
echo ==== Terjadi error, cek pesan di atas ====
pause
exit /b 1
