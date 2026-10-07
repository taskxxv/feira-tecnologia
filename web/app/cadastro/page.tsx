'use client';
import { useAuth } from "@/lib/auth";
import Link from "next/link";
import { FormEvent, useState } from "react";
import { useRouter } from "next/navigation";
export default function Register() { const router = useRouter(); const [name, setName] = useState(""); const [email, setEmail] = useState(""); const [password, setPassword] = useState("");
  const auth = useAuth();
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [confirmation, setConfirmation] = useState("");
  async function submit(event: FormEvent) {
    event.preventDefault(); if (busy) return;
    setError("");
    if (password !== confirmation) { setError("As senhas não conferem."); return; }
    setBusy(true);
    try {
      await auth.register(name.trim(), email.trim(), password, confirmation);
      router.replace("/dashboard");
    } catch (e) { setError(e instanceof TypeError ? "Não foi possível conectar à API. Confira se ela está ligada." : e instanceof Error ? e.message : "Não foi possível entrar."); }
    finally { setBusy(false); }
  }

  return <main className="grid min-h-screen place-items-center px-5 py-10"><form onSubmit={submit} className="glass-card w-full max-w-lg space-y-5 rounded-lg p-7 sm:p-10"><div className="flex items-center gap-3"><span className="grid h-12 w-12 place-items-center rounded-lg bg-accent text-xl font-black text-void">V</span><span className="font-display text-xl font-bold">vértice<span className="text-accent">.</span></span></div><div><h1 className="font-display text-3xl font-bold">Seu lugar começa aqui.</h1><p className="mt-2 text-muted">Crie uma presença para compartilhar o que você aprende.</p></div><label className="block text-sm font-semibold">Como podemos chamar você?<input required value={name} onChange={(e) => setName(e.target.value)} className="field mt-2" placeholder="Marina Alves" /></label><label className="block text-sm font-semibold">E-mail<input required type="email" value={email} onChange={(e) => setEmail(e.target.value)} className="field mt-2" placeholder="voce@exemplo.com" /></label><label className="block text-sm font-semibold">Uma senha segura<input required minLength={8} type="password" value={password} onChange={(e) => setPassword(e.target.value)} className="field mt-2" placeholder="Mínimo de 8 caracteres" /></label><label className="block text-sm font-semibold">Confirme a senha<input required minLength={8} type="password" autoComplete="new-password" value={confirmation} onChange={(e) => setConfirmation(e.target.value)} className="field mt-2" /></label>{error && <p role="alert" className="text-sm text-coral">{error}</p>}<button disabled={busy} className="primary-button w-full disabled:opacity-50">{busy ? "Aguarde..." : "Criar meu espaço"}</button><p className="text-center text-sm text-muted">Já tem uma conta? <Link href="/login" className="font-semibold text-accent underline underline-offset-4">Voltar ao login</Link></p></form></main>; }
