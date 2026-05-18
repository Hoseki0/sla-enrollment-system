@echo off
echo =========================================
echo       GitHub Auto Update Script
echo =========================================
echo.

:: Stage all changes
git add .
echo [1/3] All changes staged for commit.

:: Prompt for a commit message
set /p commitMsg="Enter commit message (Press Enter to use 'Auto update'): "
if "%commitMsg%"=="" set commitMsg=Auto update

:: Commit the changes
git commit -m "%commitMsg%"
echo [2/3] Changes committed with message: "%commitMsg%"
echo.

:: Push to the repository
echo [3/3] Pushing changes to GitHub...
git push

echo.
echo =========================================
echo               Done!
echo =========================================
pause
