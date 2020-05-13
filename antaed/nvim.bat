start %USERPROFILE%\AppData\Local\Microsoft\WindowsApps\ubuntu.exe -c "nvim $(echo '%1' | sed 's/\\/\//g' | sed 's/C:/\/mnt\/c/g')"
