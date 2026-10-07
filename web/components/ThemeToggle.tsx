'use client';

import { useStore } from "@/lib/store";

export default function ThemeToggle() {
  const { theme, toggleTheme } = useStore();
  function changeTheme(event: React.MouseEvent<HTMLButtonElement>) {
    const nextTheme = theme === "dark" ? "light" : "dark";
    const documentWithTransition = document as Document & { startViewTransition?: (callback: () => void) => void };
    if (!documentWithTransition.startViewTransition) {
      toggleTheme();
      return;
    }
    const button = event.currentTarget.getBoundingClientRect();
    document.documentElement.style.setProperty("--theme-x", `${button.left + button.width / 2}px`);
    document.documentElement.style.setProperty("--theme-y", `${button.top + button.height / 2}px`);
    documentWithTransition.startViewTransition(() => {
      document.documentElement.dataset.theme = nextTheme;
      toggleTheme();
    });
  }
  return <button className="action-button px-2" onClick={changeTheme} aria-label={theme === "dark" ? "Ativar tema claro" : "Ativar tema escuro"}><svg aria-hidden="true" className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.7"><path d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.4-6.4-1.4 1.4M7 17l-1.4 1.4m0-12.8L7 7m10 10 1.4 1.4" /><circle cx="12" cy="12" r="3.5" /></svg></button>;
}
