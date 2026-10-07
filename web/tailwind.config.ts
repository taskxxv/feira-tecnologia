import type { Config } from "tailwindcss";

export default {
  content: ["./app/**/*.{ts,tsx}", "./components/**/*.{ts,tsx}", "./lib/**/*.{ts,tsx}"],
  theme: {
    extend: {
      colors: {
        abyss: "var(--color-abyss)",
        void: "var(--color-void)",
        surface: "var(--color-surface)",
        "surface-raised": "var(--color-surface-raised)",
        "surface-soft": "var(--color-surface-soft)",
        ink: "var(--color-ink)",
        muted: "var(--color-muted)",
        faint: "var(--color-faint)",
        accent: "var(--color-accent)",
        "accent-strong": "var(--color-accent-strong)",
        coral: "var(--color-coral)",
        line: "var(--color-line)",
        glow: "var(--color-glow)",
        "glow-strong": "var(--color-glow-strong)",
      },
      borderRadius: { sm: "var(--radius-sm)", md: "var(--radius-md)", lg: "var(--radius-lg)", xl: "var(--radius-xl)", pill: "var(--radius-pill)" },
      fontFamily: { display: ["var(--font-display)"], body: ["var(--font-body)"], mono: ["var(--font-mono)"] },
      boxShadow: { depth: "var(--shadow-depth)", glow: "var(--shadow-glow)", coral: "var(--shadow-coral)" },
    },
  },
  plugins: [],
} satisfies Config;
