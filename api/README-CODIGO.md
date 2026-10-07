# Guia de leitura do código

## Ordem recomendada

1. `routes/api.php`: veja os endpoints disponíveis.
2. `Http/Requests`: confira as regras de entrada.
3. `Http/Controllers`: acompanhe o fluxo HTTP.
4. `Services/AIService.php`: veja as integrações de IA.
5. `Models`: entenda entidades e relacionamentos.
6. `database/migrations`: confira a estrutura persistente.

## Comentários no código

Os comentários devem explicar decisões de negócio ou integrações que não sejam
óbvias pelo código. Evite comentários que apenas repitam o nome de uma função.

Decisões importantes documentadas na API:

- `anonymous_reports` não possui vínculo com usuários.
- O RAG consulta somente posts aprovados.
- O guardrail é executado antes da publicação.
- Rotas administrativas exigem o papel `admin`.
