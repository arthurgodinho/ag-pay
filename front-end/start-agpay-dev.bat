@echo off
title AG PAY - Painel e Banco Digital
color 0A
echo ==============================================
echo        INICIANDO AMBIENTE DE DESENVOLVIMENTO
echo                   AG PAY (PWA)
echo ==============================================
echo.
echo Cancelando processos que possam estar travando a porta 3000...
for /f "tokens=5" %%a in ('netstat -aon ^| find ":3000" ^| find "LISTENING"') do taskkill /f /pid %%a 2>nul
echo.
echo Iniciando servidor Vite na rede local para testes pelo celular...
npm run dev
pause
