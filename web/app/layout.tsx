import './globals.css';
import { AuthProvider, AuthGate } from '@/lib/auth';
import type { Metadata } from 'next';
import { StoreProvider } from '@/lib/store';
import { DM_Serif_Display, IBM_Plex_Mono, Manrope } from 'next/font/google';
import CinematicIntro from '@/components/CinematicIntro';
import LivingOcean from '@/components/LivingOcean';
import NeonCursor from '@/components/NeonCursor';

const display = DM_Serif_Display({ subsets: ['latin'], weight: '400', variable: '--font-dm-serif', display: 'swap' });
const body = Manrope({ subsets: ['latin'], variable: '--font-manrope', display: 'swap' });
const mono = IBM_Plex_Mono({ subsets: ['latin'], weight: ['400', '500'], variable: '--font-ibm-plex', display: 'swap' });

export const metadata: Metadata = {
  title: 'Maré Neon — ideias que brilham juntas',
  description: 'Uma rede social de posts curtos para conversas que deixam rastro.',
};
export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="pt-BR" data-theme="dark" suppressHydrationWarning>
      <body className={`${display.variable} ${body.variable} ${mono.variable}`}><AuthProvider><StoreProvider><LivingOcean /><NeonCursor /><CinematicIntro /><AuthGate>{children}</AuthGate></StoreProvider></AuthProvider></body>
    </html>
  );
}
