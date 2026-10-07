"use client";
import { createContext, useContext, useEffect, useState, useCallback } from "react";
import { usePathname, useRouter } from "next/navigation";
import { api, ApiError, User } from "./api";

type Auth = {
  user: User | null;
  loading: boolean;
  error: string;
  refresh: () => Promise<void>;
  login: (email: string, password: string) => Promise<void>;
  register: (name: string, email: string, password: string, confirmation: string) => Promise<void>;
  logout: () => Promise<void>;
};
const Context = createContext<Auth | null>(null);
export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const refresh = useCallback(async () => {
    setLoading(true); setError("");
    try {
      if (!localStorage.getItem("token")) { setUser(null); return; }
      setUser(await api<User>("/auth/me"));
    } catch (e) {
      setUser(null);
      if (e instanceof ApiError && e.status === 401) localStorage.removeItem("token");
      else setError("Não foi possível verificar a sessão. Confira se a API está ligada e tente novamente.");
    } finally { setLoading(false); }
  }, []);
  useEffect(() => { void refresh(); }, [refresh]);
  useEffect(() => {
    const sync = (event: StorageEvent) => { if (event.key === "token" || event.key === null) void refresh(); };
    window.addEventListener("storage", sync);
    return () => window.removeEventListener("storage", sync);
  }, [refresh]);
  async function authenticate(path: string, data: Record<string, string>) {
    const result = await api<{ user: User; token: string }>(path, { method: "POST", body: JSON.stringify(data) });
    localStorage.setItem("token", result.token);
    localStorage.removeItem("vertice-user");
    setUser(result.user); setError("");
  }
  async function logout() {
    try { await api("/auth/logout", { method: "POST" }); }
    catch (e) { if (!(e instanceof ApiError && e.status === 401)) throw e; }
    localStorage.removeItem("token"); localStorage.removeItem("vertice-user");
    setUser(null); setError("");
  }
  return <Context.Provider value={{ user, loading, error, refresh,
    login: (email, password) => authenticate("/auth/login", { email, password }),
    register: (name, email, password, password_confirmation) => authenticate("/auth/register", { name, email, password, password_confirmation }),
    logout }}>{children}</Context.Provider>;
}
export function useAuth() {
  const value = useContext(Context);
  if (!value) throw new Error("AuthProvider ausente");
  return value;
}
export function AuthGate({ children }: { children: React.ReactNode }) {
  const { user, loading, error, refresh } = useAuth();
  const path = usePathname(); const router = useRouter();
  const protectedPage = ["/dashboard", "/posts", "/chat", "/ouvidoria", "/admin"].some(prefix => path === prefix || path.startsWith(prefix + "/"));
  useEffect(() => {
    if (protectedPage && !loading && !user && !error) router.replace("/login");
  }, [protectedPage, loading, user, error, router]);
  if (!protectedPage) return <>{children}</>;
  if (loading) return <main className="grid min-h-screen place-items-center" role="status">Verificando sessão...</main>;
  if (error) return <main className="grid min-h-screen place-items-center px-5"><div className="glass-card space-y-4 p-6"><p role="alert">{error}</p><button className="primary-button" onClick={() => void refresh()}>Tentar novamente</button></div></main>;
  if (!user) return null;
  if ((path === "/admin" || path.startsWith("/admin/")) && user.role !== "admin") return <main className="grid min-h-screen place-items-center"><div><p>Acesso reservado à administração.</p><button className="primary-button mt-4" onClick={() => router.replace("/dashboard")}>Voltar ao painel</button></div></main>;
  return <>{children}</>;
}
export function initials(name: string) { return name.trim().split(/\s+/).slice(0, 2).map(part => part[0]).join("").toUpperCase(); }
