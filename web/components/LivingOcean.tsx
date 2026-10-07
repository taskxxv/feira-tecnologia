'use client';

import { motion, useReducedMotion } from "framer-motion";
import { useEffect, useMemo, useRef } from "react";

const particles = Array.from({ length: 18 }, (_, index) => ({
  id: index,
  left: `${(index * 47) % 100}%`,
  top: `${(index * 71) % 100}%`,
  size: `${2 + (index % 3)}px`,
  delay: (index % 6) * 0.35,
}));

export default function LivingOcean() {
  const ref = useRef<HTMLDivElement>(null);
  const reducedMotion = useReducedMotion();
  const waves = useMemo(() => [0, 1, 2], []);

  useEffect(() => {
    const element = ref.current;
    if (!element || reducedMotion) return;
    let frame = 0;
    let pointerX = 0;
    let pointerY = 0;
    const updatePointer = (event: PointerEvent) => {
      pointerX = event.clientX / window.innerWidth - 0.5;
      pointerY = event.clientY / window.innerHeight - 0.5;
    };
    const update = () => {
      element.style.setProperty("--ocean-x", `${pointerX * 28}px`);
      element.style.setProperty("--ocean-y", `${pointerY * 22 + window.scrollY * -0.025}px`);
      frame = window.requestAnimationFrame(update);
    };
    window.addEventListener("pointermove", updatePointer, { passive: true });
    frame = window.requestAnimationFrame(update);
    return () => {
      window.removeEventListener("pointermove", updatePointer);
      window.cancelAnimationFrame(frame);
    };
  }, [reducedMotion]);

  return (
    <div ref={ref} className="living-ocean" aria-hidden="true">
      <div className="ocean-aurora ocean-aurora-one" />
      <div className="ocean-aurora ocean-aurora-two" />
      {waves.map((wave) => <motion.div key={wave} className={`ocean-wave ocean-wave-${wave + 1}`} animate={reducedMotion ? undefined : { x: ["-4%", "4%", "-4%"], y: [0, wave * 5, 0] }} transition={{ duration: 16 + wave * 4, repeat: Infinity, ease: [0.22, 0.61, 0.36, 1] }} />)}
      {particles.map((particle) => <motion.i key={particle.id} className="ocean-particle" style={{ left: particle.left, top: particle.top, width: particle.size, height: particle.size }} animate={reducedMotion ? undefined : { y: [0, -18, 0], opacity: [0.2, 0.8, 0.2] }} transition={{ duration: 4 + particle.id % 3, delay: particle.delay, repeat: Infinity, ease: [0.22, 0.61, 0.36, 1] }} />)}
    </div>
  );
}
