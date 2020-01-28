@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0vendor\bin\phpcs.bat
phpcbf "%BIN_TARGET%" E:\www\api.elonica.local\app %*