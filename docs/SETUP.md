# Setup local e banco

## 1. Criar um projeto novo

Este repositório já contém `api` e `web`. Os comandos abaixo servem
apenas para reproduzir a estrutura do zero em outra pasta.

No PowerShell:

```powershell
composer create-project laravel/laravel:^11.0 escola-api
Set-Location .\escola-api
php artisan install:api
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

Set-Location ..
npx create-next-app@latest escola-web --typescript --tailwind --eslint --app --src-dir --import-alias "@/*"
Set-Location .\escola-web
npx shadcn@latest init
npm install lucide-react
```

O comando `php artisan install:api` instala o Sanctum e cria a migration de
`personal_access_tokens` nas versões atuais do Laravel 11. Se a instalação
escolhida não oferecer esse comando, use os dois comandos explícitos abaixo:

```powershell
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

## 2. PostgreSQL

Crie um banco vazio e garanta que a imagem/instalação do PostgreSQL tenha
`pgvector` disponível. Exemplo com Docker:

```powershell
docker run --name escola-postgres `
  -e POSTGRES_DB=escola `
  -e POSTGRES_USER=escola `
  -e POSTGRES_PASSWORD=altere-esta-senha `
  -p 5432:5432 `
  -d pgvector/pgvector:pg16
```

Neste repositório, as migrations e Models finais já estão em
`api/database/migrations` e `api/app/Models`.

## 3. Variáveis de ambiente da API

No arquivo `api\.env`:

```dotenv
APP_NAME=Escola
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=escola
DB_USERNAME=escola
DB_PASSWORD=altere-esta-senha

SANCTUM_STATEFUL_DOMAINS=localhost:3000
FRONTEND_URL=http://localhost:3000
```

No arquivo `web\.env.local`:

```dotenv
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

## 4. Criar e validar o banco

```powershell
Set-Location .\api
php artisan migrate
php artisan about
```

As migrations criam `anonymous_reports` sem `user_id`, sem
foreign key para usuários e sem qualquer relação Eloquent com identidade.
