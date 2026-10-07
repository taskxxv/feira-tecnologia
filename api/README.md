# API — Plataforma Escola

API Laravel 11+ com Sanctum, PostgreSQL e `pgvector`. O endpoint de relatos
(`POST /api/reports`) é público e não recebe identidade do autor.

## Execução

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
docker compose up -d postgres   # a partir da raiz do projeto
php artisan migrate --seed
php artisan serve
```

Defina `GEMINI_API_KEY` e mantenha `AI_PROVIDER=gemini` para moderação,
embeddings, tutor e filtro da ouvidoria. A API usa `gemini-2.5-flash` para
texto e `gemini-embedding-001` com vetores de 1536 dimensões. `OMBUDSMAN_FILTER_URL` é opcional; quando definido, a API envia
`{content}` via HTTP e persiste a classificação retornada. Falhas de provedores
retornam HTTP 503 de forma explícita.

## Rotas principais

- `POST /api/auth/register`, `POST /api/auth/login`, `GET /api/auth/me`
- `GET /api/posts`, `POST /api/posts`, `POST /api/posts/{post}/comments`
- `POST /api/chat` (Sanctum)
- `POST /api/reports` (anônimo); listagem/alteração somente `role=admin`

## Validação

Com PHP e dependências instalados: `php artisan test`. O teste de feature cobre
registro e rejeição de credenciais inválidas. O admin seed inicial é
`admin@escola.local` / `password`; altere em ambiente real.

## RAG do tutor

`POST /api/chat` gera um embedding da pergunta e consulta os embeddings de
posts aprovados usando a distância de cosseno do pgvector (`<=>`). Até cinco
posts são carregados com autor e disciplina; o contexto enviado ao modelo
contém citações no formato `[Autor: título]`, e a resposta também retorna
`rag.used` e `rag.sources`. Se nenhum post relevante estiver indexado, a API
não inventa contexto: retorna uma orientação pedagógica neutra com três links
de busca do YouTube. Falhas no embedding, no banco ou no provedor de IA
retornam HTTP 503.
