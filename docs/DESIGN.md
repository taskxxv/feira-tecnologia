# Design system — Maré Neon

## Nome da rede

**Maré Neon** é uma rede social de posts curtos onde ideias aparecem como
organismos bioluminescentes em um oceano noturno. Cada publicação é um ponto de
luz; respostas são cardumes; temas populares viram correntes. A metáfora dá à
interface uma personalidade aquática, contemplativa e elétrica, sem copiar
convenções visuais de redes sociais existentes.

## Conceito em 5 linhas

1. O fundo é um oceano abissal azul-petróleo, profundo e respirável.
2. Posts são pequenas criaturas luminosas, com halos em ciano e coral.
3. Conversas se conectam como correntes: suaves, fluidas e sempre em movimento.
4. Superfícies parecem vidro molhado, com bordas iridescentes discretas.
5. A experiência alterna silêncio escuro e clarões de descoberta.

## Paleta

- **Dominante:** abissal `#071923` — azul-petróleo profundo, nunca preto puro.
- **Acentos:** bioluminescência `#72F2D0` e coral-pérola `#FF9D8D`.
- **Neutros com personalidade:** espuma `#ECF8F5`, névoa `#A8C4C6`,
  ardósia-marinha `#29434B`.
- **Dark:** abissal como base, superfícies `#0E2630`/`#14343D`, texto espuma.
- **Light:** espuma-esverdeada `#EAF5F2` como base, superfícies quase brancas
  `#F8FCFA`, texto azul-marinho `#102A33`; os acentos ficam mais escuros para
  manter contraste AA.

## Tipografia

- **Display expressiva:** `DM_Serif_Display`, usada em títulos e marca.
- **Leitura:** `Manrope`, usada em textos, controles e navegação.
- **Mono:** `IBM_Plex_Mono`, usada em metadados, contadores e etiquetas.
- As três famílias são carregadas por `next/font/google` no layout raiz e
  expostas aos tokens CSS; nenhuma fonte é declarada diretamente nos componentes.

## Escala de espaçamento

`--space-1: 0.25rem`, `--space-2: 0.5rem`, `--space-3: 0.75rem`,
`--space-4: 1rem`, `--space-5: 1.25rem`, `--space-6: 1.5rem`,
`--space-8: 2rem`, `--space-10: 2.5rem`, `--space-12: 3rem`,
`--space-16: 4rem`, `--space-20: 5rem`.

## Raios

`sm: 0.625rem`, `md: 1rem`, `lg: 1.5rem`, `xl: 2rem`, `pill: 999px`.

## Sombras

- `--shadow-depth`: sombra azul-marinho difusa para elevação.
- `--shadow-glow`: halo ciano para organismos ativos e foco.
- `--shadow-coral`: halo coral para alertas e ações de destaque.

## Easings e durações

- `--ease-current`: `cubic-bezier(0.22, 0.61, 0.36, 1)` para correntes suaves.
- `--ease-surge`: `cubic-bezier(0.2, 0.9, 0.25, 1.2)` para surgimentos elásticos.
- `--duration-instant`: `120ms`, `--duration-fast`: `180ms`,
  `--duration-base`: `280ms`, `--duration-slow`: `520ms`.
- Animações usam apenas `transform` e `opacity`, incluem
  `prefers-reduced-motion` e nunca usam o easing padrão `ease`.
