'use client';
import { initials } from "@/lib/auth";
import { FormEvent, useState } from "react";
import { useStore } from "@/lib/store";
export default function Composer() {
  const { user, addPost } = useStore(); const [text, setText] = useState(""); const [topic, setTopic] = useState("Ideias");
  async function submit(event: FormEvent) { event.preventDefault(); if (!text.trim()) return; try { await addPost(text.trim(), topic); setText(""); } catch (error) { alert(error instanceof Error ? error.message : "Não foi possível publicar."); } }
  return <form onSubmit={submit} className="glass-card rounded-lg p-5">
    <div className="flex gap-3"><span className="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-accent/15 text-sm font-bold text-accent">{initials(user.name)}</span><div className="flex-1"><label htmlFor="new-post" className="sr-only">Compartilhe uma ideia</label><textarea id="new-post" value={text} onChange={(event) => setText(event.target.value)} maxLength={280} rows={3} placeholder="O que está despertando sua curiosidade?" className="w-full resize-none border-0 bg-transparent text-lg leading-7 text-ink outline-none placeholder:text-faint" /></div></div>
    <div className="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-3"><select value={topic} onChange={(event) => setTopic(event.target.value)} className="rounded-pill border border-line bg-surface px-3 py-2 text-sm text-muted"><option>Ideias</option><option>História</option><option>Matemática</option><option>Ciência</option><option>Leitura</option></select><div className="flex items-center gap-3"><span className="text-xs text-faint">{text.length}/280</span><button type="submit" className="primary-button px-4 py-2 text-sm">Publicar pulso</button></div></div>
  </form>;
}
