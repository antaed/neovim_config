New-PSDrive -Name HKCR -PSProvider Registry -Root HKEY_CLASSES_ROOT;

$keyPath = "HKCR:\*\shell\Nvim_WSL";
$commandPath = "$keyPath\command";

if (-not (Test-Path -LiteralPath $keyPath)) {
    New-Item -Path $keyPath;
}
if (-not (Test-Path -LiteralPath $commandPath)) {
    New-Item -Path $commandPath;
}

New-ItemProperty -LiteralPath $keyPath -Name "(Default)" -Value "Edit file with Neovim on &WSL" -PropertyType String;
New-ItemProperty -LiteralPath $keyPath -Name "Icon" -Value "%USERPROFILE%\neovim.ico" -PropertyType String;
New-ItemProperty -LiteralPath $commandPath -Name "(Default)" -Value "`"%USERPROFILE%\AppData\Local\Microsoft\WindowsApps\ubuntu.exe`" -c `"nvim \`"`$(echo '%1' | sed 's/\\/\//g' | sed 's/C:/\/mnt\/c/g')\`"`"" -PropertyType ExpandString;
# nvim $(echo '%1' | sed 's/\\/\//g' | sed 's/C:/\/mnt\/c/g')
