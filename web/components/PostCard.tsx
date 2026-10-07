'use client';
import Link from "next/link";
import { SocialPost } from "@/lib/mock-data";
import { useStore } from "@/lib/store";
function ActionIcon({ type }: { type: "like" | "repost" | "comment" | "share" }) {
  const d = { like: "M12 20S4 15.5 4 9.5A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 8 2.5C20 15.5 12 20 12 20Z", repost: "M17 2l4 4-4 4M3 6h18M7 22l-4-4 4-4M21 18H3", comment: "M4 5h16v11H8l-4 4z", share: "m4 12 16-8-5 16-3-6z" }[type];
  return <svg aria-hidden="true" className="h-4 w-4" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.7" viewBox="0 0 24 24"><path d={d} /></svg>;
}
export default function PostCard({ post, featured = false }: { post: SocialPost; featured?: boolean }) {
  const { toggleLike, toggleRepost } = useStore();
  return <article className={`glass-card rounded-lg p-5 transition duration-300 hover:-translate-y-0.5 ${featured ? "border-accent/40 shadow-glow" : ""}`}>
    <div className="flex gap-3">
      <span className="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-line text-sm font-bold" style={{ color: post.author.accent, backgroundColor: `${post.author.accent}18` }}>{post.author.name.split(" ").map((part) => part[0]).join("").slice(0, 2)}</span>
      <div className="min-w-0 flex-1"><div className="flex flex-wrap items-baseline gap-x-2 gap-y-1"><strong>{post.author.name}</strong><span className="text-sm text-muted">@{post.author.handle}</span><span className="text-xs text-faint">· {post.createdAt}</span></div>
        <Link href={`/posts/${post.id}`} className="mt-3 block whitespace-pre-wrap text-[1.02rem] leading-7 text-ink hover:text-accent">{post.text}</Link>
        <div className="mt-4 flex items-center justify-between gap-2 border-t border-line pt-3">
          <span className="rounded-pill bg-surface-soft px-3 py-1 text-xs font-semibold text-accent"># {post.topic}</span>
          <div className="flex gap-1">
            <button className={`action-button border-transparent px-2 ${post.liked ? "text-coral" : ""}`} onClick={() => toggleLike(post.id)} aria-label={post.liked ? "Descurtir post" : "Curtir post"}><ActionIcon type="like" /><span>{post.likes}</span></button>
            <button className={`action-button border-transparent px-2 ${post.reposted ? "text-accent" : ""}`} onClick={() => toggleRepost(post.id)} aria-label={post.reposted ? "Desfazer repost" : "Repostar"}><ActionIcon type="repost" /><span>{post.reposts}</span></button>
            <Link className="action-button border-transparent px-2" href={`/posts/${post.id}`} aria-label="Comentar post"><ActionIcon type="comment" /><span>{post.comments}</span></Link>
            <button className="action-button border-transparent px-2" aria-label="Compartilhar post"><ActionIcon type="share" /></button>
          </div>
        </div>
      </div>
    </div>
  </article>;
}
