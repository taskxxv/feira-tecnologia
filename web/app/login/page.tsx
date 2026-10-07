'use client';
import { useAuth } from "@/lib/auth";
import Link from "next/link";
import { FormEvent, useState } from "react";
import { useRouter } from "next/navigation";
function Mark() { return <div className="grid h-14 w-14 place-items-center rounded-lg bg-accent text-2xl font-black text-void shadow-[0_0_35px_var(--color-glow)]">V</div>; }
export default function Login() { const router = useRouter(); const [email, setEmail] = useState(""); const [password, setPassword] = useState("");
  const auth = useAuth();
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  async function submit(event: FormEvent) {
    event.preventDefault(); if (busy) return;
    setError("");
    setBusy(true);
    try {
      await auth.login(email.trim(), password);
      router.replace("/dashboard");
    } catch (e) { setError(e instanceof TypeError ? "Não foi possível conectar à API. Confira se ela está ligada." : e instanceof Error ? e.message : "Não foi possível entrar."); }
    finally { setBusy(false); }
  }

  return <main className="grid min-h-screen place-items-center px-5 py-10"><div className="grid w-full max-w-5xl overflow-hidden rounded-lg border border-line bg-surface/80 shadow-card lg:grid-cols-[1.1fr_.9fr]"><div className="hidden flex-col justify-between bg-surface-soft p-10 lg:flex"><div><Mark /><p className="mt-16 max-w-sm font-display text-4xl font-bold leading-tight">Ideias não precisam ser longas para ir longe<span className="text-accent">.</span></p></div><p className="max-w-xs text-sm leading-6 text-muted">Vértice é o lugar para aprender em público, com curiosidade e cuidado.</p></div><form onSubmit={submit} className="space-y-5 p-7 sm:p-10"><Mark /><div><h1 className="font-display text-3xl font-bold">Que bom ter você aqui.</h1><p className="mt-2 text-muted">Entre para acompanhar o pulso da comunidade.</p></div><label className="block text-sm font-semibold">E-mail<input required type="email" value={email} onChange={(e) => setEmail(e.target.value)} className="field mt-2" placeholder="voce@exemplo.com" /></label><label className="block text-sm font-semibold">Senha<input required type="password" value={password} onChange={(e) => setPassword(e.target.value)} className="field mt-2" placeholder="••••••••" /></label>{error && <p role="alert" className="text-sm text-coral">{error}</p>}<button disabled={busy} className="primary-button w-full disabled:opacity-50">{busy ? "Aguarde..." : "Entrar no Vértice"}</button><p className="text-center text-sm text-muted">Primeira vez por aqui? <Link href="/cadastro" className="font-semibold text-accent underline underline-offset-4">Criar meu espaço</Link></p></form></div></main>; }
