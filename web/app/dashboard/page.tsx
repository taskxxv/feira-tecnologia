'use client';
import Link from "next/link";
import Header from "@/components/Header";
import Composer from "@/components/Composer";
import PostCard from "@/components/PostCard";
import { useStore } from "@/lib/store";
export default function Dashboard() {
  const { posts } = useStore();
  return <><Header /><main className="mx-auto grid max-w-7xl gap-8 px-5 py-8 lg:grid-cols-[minmax(0,1fr)_280px] lg:px-8">
    <section className="mx-auto w-full max-w-2xl space-y-5"><div className="flex items-end justify-between"><div><p className="mb-2 text-sm font-semibold uppercase tracking-[0.18em] text-accent">segunda, 20 de setembro</p><h1 className="font-display text-3xl font-bold tracking-tight sm:text-4xl">O pulso da comunidade<span className="text-accent">.</span></h1><p className="mt-2 text-muted">Ideias curtas. Conversas que ficam.</p></div><span className="hidden rounded-pill border border-line px-3 py-2 text-xs text-muted sm:block">03.482 pessoas online</span></div>
      <div className="flex gap-2 border-b border-line pb-2 text-sm"><button className="rounded-pill bg-surface-soft px-4 py-2 font-semibold text-ink">Para você</button><button className="rounded-pill px-4 py-2 text-muted hover:text-ink">Seguindo</button><button className="rounded-pill px-4 py-2 text-muted hover:text-ink">Em alta</button></div>
      <Composer />{posts.map((post, index) => <PostCard key={post.id} post={post} featured={index === 0} />)}</section>
    <aside className="hidden space-y-5 lg:block"><div className="glass-card rounded-lg p-5"><p className="text-xs font-bold uppercase tracking-[0.18em] text-accent">Mapa de curiosidades</p><h2 className="mt-3 font-display text-xl font-bold">O que está aceso hoje</h2><div className="mt-4 space-y-3">{["Ciência no cotidiano", "Memória e território", "Matemática visual"].map((topic, index) => <div key={topic} className="flex items-center justify-between border-b border-line pb-3 text-sm"><span><span className="mr-2 text-accent">0{index + 1}</span>{topic}</span><span className="text-xs text-faint">{28 - index * 6} pulsos</span></div>)}</div></div><div className="rounded-lg border border-coral/25 bg-coral/10 p-5"><p className="text-sm font-semibold text-coral">Convite aberto</p><h2 className="mt-2 font-display text-lg font-bold">Compartilhe uma pergunta sem medo de ser breve.</h2><Link href="/posts/novo" className="mt-4 inline-block text-sm font-semibold text-coral underline underline-offset-4">Abrir espaço de escrita →</Link></div></aside>
  </main></>;
}
