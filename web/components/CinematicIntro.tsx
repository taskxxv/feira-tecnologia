'use client';

import { AnimatePresence, motion } from "framer-motion";
import { useEffect, useState } from "react";

const currentEase: [number, number, number, number] = [0.22, 0.61, 0.36, 1];
const surgeEase: [number, number, number, number] = [0.2, 0.9, 0.25, 1.2];

export default function CinematicIntro() {
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    if (window.sessionStorage.getItem("mare-neon-intro-seen")) return;
    setVisible(true);
    const timeout = window.setTimeout(() => {
      window.sessionStorage.setItem("mare-neon-intro-seen", "true");
      setVisible(false);
    }, 2200);
    return () => window.clearTimeout(timeout);
  }, []);

  return (
    <AnimatePresence>
      {visible && (
        <motion.div
          className="fixed inset-0 z-[100] grid place-items-center overflow-hidden bg-abyss"
          initial={{ opacity: 1 }}
          exit={{ opacity: 0, transition: { duration: 0.5, ease: currentEase } }}
          aria-label="Maré Neon carregando"
          role="status"
        >
          <motion.div
            className="absolute h-72 w-72 rounded-full bg-glow/10 blur-3xl"
            animate={{ scale: [0.7, 1.15, 1], opacity: [0, 0.8, 0.5] }}
            transition={{ duration: 1.8, ease: currentEase }}
          />
          <div className="relative flex flex-col items-center gap-5">
            <svg className="h-28 w-28" viewBox="0 0 48 48" fill="none" aria-hidden="true">
              <motion.path
                d="M24 4C13.4 4 6 13.3 6 23.6 6 35.1 14.1 44 24 44s18-8.9 18-20.4C42 13.3 34.6 4 24 4Z"
                stroke="var(--color-glow)"
                strokeWidth="1.5"
                initial={{ pathLength: 0, opacity: 0 }}
                animate={{ pathLength: 1, opacity: 1 }}
                transition={{ duration: 1.1, ease: currentEase }}
              />
              <motion.path
                d="M10 27.5c5.2-2.8 9.9-2.8 14.1 0 4.3 2.8 8.9 2.8 13.9 0M13.5 20.5c3.5-1.8 6.7-1.8 9.7 0 3 1.8 6.2 1.8 9.8 0"
                stroke="var(--color-coral)"
                strokeLinecap="round"
                strokeWidth="1.5"
                initial={{ pathLength: 0, opacity: 0 }}
                animate={{ pathLength: 1, opacity: 1 }}
                transition={{ delay: 0.65, duration: 0.8, ease: currentEase }}
              />
              <motion.circle cx="17" cy="14.5" r="1.5" fill="var(--color-glow)" initial={{ scale: 0 }} animate={{ scale: 1 }} transition={{ delay: 1.25, ease: surgeEase }} />
              <motion.circle cx="31.5" cy="33.5" r="1.5" fill="var(--color-coral)" initial={{ scale: 0 }} animate={{ scale: 1 }} transition={{ delay: 1.35, ease: surgeEase }} />
            </svg>
            <motion.p
              className="font-display text-4xl text-ink"
              initial={{ opacity: 0, y: 12 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 1.2, duration: 0.5, ease: currentEase }}
            >
              maré<span className="text-glow">.</span>
            </motion.p>
          </div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}
