'use client';
import { useState } from "react";
import { useAuth, initials } from "@/lib/auth";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useStore } from "@/lib/store";
import Logo from "@/components/Logo";
import ThemeToggle from "@/components/ThemeToggle";

function Icon({ name }: { name: "home" | "spark" | "shield" | "sun" }) {
  const paths = { home: "M4 10.5 12 4l8 6.5v8a1 1 0 0 1-1 1h-5v-5h-4v5H5a1 1 0 0 1-1-1z", spark: "m12 3 1.7 5.3L19 10l-5.3 1.7L12 17l-1.7-5.3L5 10l5.3-1.7z", shield: "M12 3 19 6v5c0 4.4-2.9 8-7 10-4.1-2-7-5.6-7-10V6z", sun: "M12 4V2m0 20v-2m8-8h2M2 12h2m13.7-5.7 1.4-1.4M4.9 19.1l1.4-1.4m0-11.4L4.9 4.9m14.2 14.2-1.4-1.4M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0" };
  return <svg aria-hidden="true" className="h-5 w-5" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.7" viewBox="0 0 24 24"><path d={paths[name]} /></svg>;
}
export default function Header() {
  const pathname = usePathname(); const router = useRouter(); const { user } = useStore();
  const { logout } = useAuth(); const [busy, setBusy] = useState(false); const [error, setError] = useState("");
  async function leave() {
    setBusy(true); setError("");
    try { await logout(); router.replace("/login"); }
    catch { setError("Não foi possível sair. Confira a API e tente novamente."); }
    finally { setBusy(false); }
  }
  const links = [{ href: "/dashboard", label: "Pulso", icon: "home" as const }, { href: "/chat", label: "Núcleo IA", icon: "spark" as const }, { href: "/ouvidoria", label: "Escuta", icon: "shield" as const }];
  return <header className="sticky top-0 z-20 border-b border-line bg-void/80 backdrop-blur-xl">
    <div className="mx-auto flex max-w-7xl items-center justify-between gap-5 px-5 py-3 lg:px-8">
      <Link href="/dashboard" aria-label="Maré Neon, ir para o pulso"><Logo /></Link>
      <nav className="flex items-center gap-1" aria-label="Navegação principal">
        {links.map((link) => <Link key={link.href} href={link.href} className={`action-button border-transparent ${pathname === link.href ? "bg-surface-soft text-ink" : ""}`}><Icon name={link.icon} /><span className="hidden md:inline">{link.label}</span></Link>)}
      </nav>
      <div className="flex items-center gap-2">
        <ThemeToggle />
        <button className="hidden text-left sm:block" onClick={() => router.push("/dashboard")}><span className="block text-sm font-semibold">{user.name}</span><span className="block text-xs text-muted">@{user.handle}</span></button>
        <span className="grid h-10 w-10 place-items-center rounded-full border-2 border-accent/50 bg-surface-soft text-sm font-bold text-accent" aria-hidden="true">{initials(user.name)}</span>
        <button className="action-button" disabled={busy} onClick={leave}>{busy ? "Saindo..." : "Sair"}</button>
      </div>
    </div>
    {error && <p role="alert" className="px-5 pb-3 text-sm text-coral">{error}</p>}
  </header>;
}
