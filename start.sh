#!/usr/bin/env bash
# Lance tout le projet : ./start.sh   (option --no-blazor, --no-db)
cd "$(dirname "$0")"; mkdir -p logs
export DB_PORT="${DB_PORT:-3307}"
if [[ " $* " != *" --no-db "* ]] && ! (exec 3<>/dev/tcp/127.0.0.1/$DB_PORT) 2>/dev/null; then
  docker compose up -d --wait db || { echo "Lance Docker ou ouvre ton tunnel SSH (--no-db)"; exit 1; }
fi
trap 'kill 0' EXIT
PHP_CLI_SERVER_WORKERS=4 php -S 0.0.0.0:8880 -t Api/public >logs/api.out.log 2>&1 &
PHP_CLI_SERVER_WORKERS=4 php -S 0.0.0.0:9990 router.php >logs/site.out.log 2>&1 &
[[ " $* " != *" --no-blazor "* ]] && command -v dotnet >/dev/null && dotnet run --project Blazor/BlazorAdminRentPark --urls http://localhost:5019 >logs/blazor.out.log 2>&1 &
echo "Site http://localhost:9990 | API http://localhost:8880 | Blazor http://localhost:5019 | Ctrl+C pour arrêter"
wait
