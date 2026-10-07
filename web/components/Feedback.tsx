export function ErrorBox({ children }: { children: React.ReactNode }) {
  return (
    <div role="alert" className="rounded-lg bg-red-50 p-3 text-sm text-red-700">
      {children}
    </div>
  );
}
export function Loading() {
  return <p className="animate-pulse text-slate-500">Carregando…</p>;
}
