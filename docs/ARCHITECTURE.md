# Arquitetura

## Organização

```text
plataforma-escola/
├── api/                     # API Laravel, domínio e persistência
│   ├── app/
│   │   ├── Http/            # Controllers, Requests e Middleware
│   │   ├── Models/          # Entidades Eloquent
│   │   └── Services/        # IA e regras de integração
│   ├── database/            # Migrations e seeders
│   ├── routes/              # Rotas HTTP da API
│   └── tests/               # Testes automatizados da API
├── web/                     # Next.js App Router
│   ├── app/                 # Rotas e páginas
│   ├── components/          # Componentes compartilhados
│   └── lib/                 # Cliente da API e utilitários
├── docs/                    # Documentação operacional e técnica
├── docker-compose.yml       # PostgreSQL com pgvector
└── README.md               # Entrada principal do projeto
```

## Fronteiras

- A aplicação web conversa somente com a API Laravel.
- A API é responsável por autenticação, autorização, validação e IA.
- O PostgreSQL é a fonte persistente do fórum e da ouvidoria.
- `anonymous_reports` não tem `user_id` nem associação com `users`.
- Embeddings de posts aprovados são armazenados em `post_embeddings` e
  consultados com distância de cosseno do `pgvector`.

## Fluxo de IA

1. Posts passam pelo guardrail antes de serem aprovados.
2. Posts aprovados recebem embedding e podem ser recuperados pelo Tutor IA.
3. A pergunta do chat recebe embedding e consulta os conteúdos mais próximos.
4. A resposta cita autor e título dos conteúdos recuperados.
5. Sem contexto escolar suficiente, o tutor retorna um fallback pedagógico com
   sugestões de pesquisa.
6. Relatos da ouvidoria passam por categorização, severidade e limpeza antes de
   serem exibidos à administração.
