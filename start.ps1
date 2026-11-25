# Lance TOUT le projet en une commande :  .\start.ps1   (ou double-clic sur start.bat)
#   Site     http://localhost:9990        (comptes : admin/admin123, employe/employe123, client/client123)
#   API      http://localhost:8880
#   Blazor   http://localhost:5019        (admin / admin123)
# Options : -NoBlazor (sans l'admin Blazor), -NoDb (base déjà dispo : tunnel SSH, MySQL local...)
param([switch]$NoBlazor, [switch]$NoDb, [switch]$NoBrowser)
$ErrorActionPreference = 'Stop'
$root = $PSScriptRoot
Set-Location $root
New-Item -ItemType Directory -Force logs | Out-Null

function Test-Port($port) { [bool](Test-NetConnection 127.0.0.1 -Port $port -WarningAction SilentlyContinue -InformationLevel Quiet) }

if (-not $env:DB_PORT) { $env:DB_PORT = '3307' }
if (-not $NoDb -and -not (Test-Port ([int]$env:DB_PORT))) {
    if (-not (Get-Command docker -ErrorAction SilentlyContinue)) { throw "Aucune base sur le port $($env:DB_PORT) et Docker absent. Installe Docker Desktop, ou ouvre ton tunnel SSH puis relance avec -NoDb." }
    Write-Host 'Démarrage de la base MySQL (Docker)...'
    docker compose up -d --wait db
    if ($LASTEXITCODE -ne 0) { throw 'Docker ne répond pas : lance Docker Desktop puis relance.' }
}

$procs = @()
function Start-Svc($name, $file, $svcArgs) {
    $p = Start-Process $file -ArgumentList $svcArgs -WorkingDirectory $root -WindowStyle Hidden -PassThru `
        -RedirectStandardOutput "logs/$name.out.log" -RedirectStandardError "logs/$name.err.log"
    $script:procs += $p
}

try {
    Start-Svc 'api'  'php' '-S 0.0.0.0:8880 -t Api/public'
    Start-Svc 'site' 'php' '-S 0.0.0.0:9990 router.php'
    if (-not $NoBlazor) {
        if (-not (Get-Command dotnet -ErrorAction SilentlyContinue)) { Write-Warning 'dotnet (SDK 9) absent : Blazor ignoré.' }
        else { Start-Svc 'blazor' 'dotnet' 'run --project Blazor/BlazorAdminRentPark --urls http://localhost:5019' }
    }
    Start-Sleep 3
    Write-Host "`nSite   : http://localhost:9990`nAPI    : http://localhost:8880/voitures`nBlazor : http://localhost:5019`nLogs   : $root\logs`n`nCtrl+C pour tout arrêter."
    if (-not $NoBrowser) { Start-Process 'http://localhost:9990' }
    while ($true) { Start-Sleep 2; if ($procs[0].HasExited) { throw "L'API s'est arrêtée, voir logs/api.err.log" } }
}
finally {
    foreach ($p in $procs) { if (-not $p.HasExited) { & taskkill /PID $p.Id /T /F | Out-Null } }
}
