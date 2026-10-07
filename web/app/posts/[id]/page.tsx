'use client';
import { FormEvent, useState } from "react";
import { useParams } from "next/navigation";
import Link from "next/link";
import Header from "@/components/Header";
import PostCard from "@/components/PostCard";
import { useStore } from "@/lib/store";
export default function PostDetail() { const { id } = useParams<{ id: string }>(); const { posts, addComment } = useStore(); const post = posts.find((item) => item.id === Number(id)); const [comment, setComment] = useState("");
  if (!post) return <><Header /><main className="mx-auto max-w-2xl px-5 py-16"><p className="text-muted">Este pulso não foi encontrado.</p><Link href="/dashboard" className="mt-4 inline-block text-accent underline">Voltar ao feed</Link></main></>;
  function submit(event: FormEvent) { event.preventDefault(); if (comment.trim()) { addComment(post!.id, comment.trim()); setComment(""); } }
  return <><Header /><main className="mx-auto max-w-2xl space-y-5 px-5 py-8"><Link href="/dashboard" className="text-sm text-muted hover:text-accent">← Voltar ao pulso</Link><PostCard post={post} featured /><section className="glass-card rounded-lg p-5"><h2 className="font-display text-xl font-bold">Conversa <span className="text-accent">{post.comments}</span></h2><div className="mt-5 space-y-4">{post.commentsList.map((item) => <div key={item.id} className="flex gap-3 border-b border-line pb-4"><span className="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-accent/15 text-xs font-bold text-accent">{item.author.name.slice(0, 2).toUpperCase()}</span><div><p className="text-sm font-semibold">{item.author.name} <span className="font-normal text-muted">@{item.author.handle}</span></p><p className="mt-1 leading-6 text-muted">{item.text}</p></div></div>)}</div><form onSubmit={submit} className="mt-5 flex gap-2"><input required value={comment} onChange={(event) => setComment(event.target.value)} className="field" placeholder="Acrescente uma camada à conversa..." aria-label="Novo comentário" /><button className="primary-button shrink-0 px-4">Enviar</button></form></section></main></>;
}
