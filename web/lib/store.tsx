'use client';

import { createContext, useContext, useEffect, useMemo, useState } from "react";
import { initialPosts, initialReports, Profile, SocialPost } from "./mock-data";
import { api, Post as ApiPost, Subject } from "./api";

import { useAuth } from "./auth";

type Store = {
  user: Profile;
  posts: SocialPost[];
  reports: typeof initialReports;
  theme: "dark" | "light";
  toggleTheme: () => void;
  toggleLike: (id: number) => void;
  toggleRepost: (id: number) => void;
  addPost: (text: string, topic: string) => Promise<void>;
  addComment: (id: number, text: string) => void;
  addReport: (text: string) => void;
  updateReport: (id: number, status: string) => void;
};
const StoreContext = createContext<Store | null>(null);

export function StoreProvider({ children }: { children: React.ReactNode }) {
  const { user: account } = useAuth();
  const user = useMemo<Profile>(() => ({ id: account?.id ?? 0, name: account?.name ?? "Visitante", handle: account ? `usuario${account.id}` : "visitante", role: account?.role ?? "", accent: "#7ce2c0" }), [account]);
  const [posts, setPosts] = useState<SocialPost[]>([]);
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [reports, setReports] = useState(initialReports);
  const [theme, setTheme] = useState<"dark" | "light">("dark");
  useEffect(() => {
    const saved = window.localStorage.getItem("vertice-theme");
    if (saved === "light" || saved === "dark") setTheme(saved);
  }, []);
  useEffect(() => { document.documentElement.dataset.theme = theme; window.localStorage.setItem("vertice-theme", theme); }, [theme]);
  useEffect(() => {
    let active = true;
    async function loadPosts() {
      try {
        const [publicPage, minePage, subjectList] = await Promise.all([
          api<{ data: ApiPost[] }>("/posts"),
          account ? api<{ data: ApiPost[] }>("/my/posts") : Promise.resolve({ data: [] }),
          api<Subject[]>("/subjects"),
        ]);
        if (!active) return;
        setSubjects(subjectList);
        const combined = [...publicPage.data, ...minePage.data]
          .filter((post, index, list) => list.findIndex((item) => item.id === post.id) === index)
          .sort((a, b) => +new Date(b.created_at) - +new Date(a.created_at));
        setPosts(combined.map((post) => toSocialPost(post)));
      } catch {
        if (active) setPosts([]);
      }
    }
    void loadPosts();
    return () => { active = false; };
  }, [account]);
  const value = useMemo<Store>(() => ({
    user, posts, reports, theme,
    toggleTheme: () => setTheme((current) => current === "dark" ? "light" : "dark"),
    toggleLike: (id) => setPosts((items) => items.map((post) => post.id === id ? { ...post, liked: !post.liked, likes: post.likes + (post.liked ? -1 : 1) } : post)),
    toggleRepost: (id) => setPosts((items) => items.map((post) => post.id === id ? { ...post, reposted: !post.reposted, reposts: post.reposts + (post.reposted ? -1 : 1) } : post)),
    addPost: async (text, topic) => {
      let subject = subjects.find((item) => item.name === topic) ?? subjects[0];
      if (!subject) { subject = await api<Subject[]>("/subjects").then((items) => items.find((item) => item.name === topic) ?? items[0]); }
      const title = text.split(/[.!?\n]/)[0].trim().slice(0, 200) || "Nova publicação";
      const created = await api<ApiPost>("/posts", { method: "POST", body: JSON.stringify({ subject_id: subject.id, title, content: text }) });
      setPosts((items) => [toSocialPost(created), ...items.filter((item) => item.id !== created.id)]);
    },
    addComment: (id, text) => setPosts((items) => items.map((post) => post.id === id ? { ...post, comments: post.comments + 1, commentsList: [...post.commentsList, { id: Date.now(), author: user, text }] } : post)),
    addReport: (text) => setReports((items) => [{ id: Date.now(), category: "Novo relato", severity: "em análise", status: "nova", text }, ...items]),
    updateReport: (id, status) => setReports((items) => items.map((report) => report.id === id ? { ...report, status } : report)),
  }), [posts, reports, theme, user]);
  return <StoreContext.Provider value={value}>{children}</StoreContext.Provider>;
}

function toSocialPost(post: ApiPost): SocialPost {
  const name = post.user?.name ?? "Usuário";
  return {
    id: post.id,
    author: { id: post.user?.id ?? 0, name, handle: `usuario${post.user?.id ?? 0}`, role: "", accent: "#7ce2c0" },
    text: post.content,
    topic: post.subject?.name ?? "Ideias",
    createdAt: post.created_at ? new Date(post.created_at).toLocaleString("pt-BR", { dateStyle: "short", timeStyle: "short" }) : "agora",
    likes: 0,
    reposts: 0,
    comments: post.comments_count ?? post.comments?.length ?? 0,
    commentsList: (post.comments ?? []).map((comment) => ({ id: comment.id, author: { id: comment.user?.id ?? 0, name: comment.user?.name ?? "Usuário", handle: `usuario${comment.user?.id ?? 0}`, role: "", accent: "#a99cff" }, text: comment.content })),
  };
}
export function useStore() {
  const value = useContext(StoreContext);
  if (!value) throw new Error("useStore precisa estar dentro de StoreProvider");
  return value;
}
