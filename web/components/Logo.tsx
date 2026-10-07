type LogoProps = { compact?: boolean; className?: string };

export default function Logo({ compact = false, className = "" }: LogoProps) {
  return (
    <span className={`inline-flex items-center gap-3 ${className}`}>
      <svg aria-hidden="true" className="h-10 w-10 shrink-0" viewBox="0 0 48 48" fill="none">
        <path d="M24 4C13.4 4 6 13.3 6 23.6 6 35.1 14.1 44 24 44s18-8.9 18-20.4C42 13.3 34.6 4 24 4Z" fill="var(--color-surface-raised)" stroke="var(--color-glow)" strokeWidth="1.5" />
        <path d="M10 27.5c5.2-2.8 9.9-2.8 14.1 0 4.3 2.8 8.9 2.8 13.9 0" stroke="var(--color-glow)" strokeLinecap="round" strokeWidth="2" />
        <path d="M13.5 20.5c3.5-1.8 6.7-1.8 9.7 0 3 1.8 6.2 1.8 9.8 0" stroke="var(--color-coral)" strokeLinecap="round" strokeWidth="1.5" />
        <circle cx="17" cy="14.5" r="1.5" fill="var(--color-glow)" />
        <circle cx="31.5" cy="33.5" r="1.5" fill="var(--color-coral)" />
      </svg>
      {!compact && <span className="font-display text-2xl tracking-tight">maré<span className="text-glow">.</span></span>}
    </span>
  );
}
