'use client';

import { useEffect, useRef } from "react";

export default function NeonCursor() {
  const cursorRef = useRef<HTMLDivElement>(null);
  const trailRefs = useRef<Array<HTMLSpanElement | null>>([]);

  useEffect(() => {
    const cursor = cursorRef.current;
    if (!cursor || window.matchMedia("(pointer: coarse)").matches) return;
    document.documentElement.classList.add("has-neon-cursor");
    let x = -100;
    let y = -100;
    let frame = 0;
    const trail = trailRefs.current;
    const move = (event: PointerEvent) => {
      x = event.clientX;
      y = event.clientY;
      const target = event.target as HTMLElement;
      const interactive = target.closest("button, a, [data-cursor='interactive']");
      const post = target.closest("article");
      cursor.dataset.mode = interactive ? "link" : post ? "post" : "default";
    };
    const render = () => {
      cursor.style.transform = `translate3d(${x}px, ${y}px, 0)`;
      trail.forEach((item, index) => {
        if (!item) return;
        item.style.transform = `translate3d(${x - index * 5}px, ${y - index * 4}px, 0)`;
        item.style.opacity = `${0.28 - index * 0.045}`;
      });
      frame = window.requestAnimationFrame(render);
    };
    window.addEventListener("pointermove", move, { passive: true });
    frame = window.requestAnimationFrame(render);
    return () => {
      document.documentElement.classList.remove("has-neon-cursor");
      window.removeEventListener("pointermove", move);
      window.cancelAnimationFrame(frame);
    };
  }, []);

  return <><div ref={cursorRef} className="neon-cursor" /><div className="neon-trail" aria-hidden="true">{Array.from({ length: 5 }, (_, index) => <span key={index} ref={(node) => { trailRefs.current[index] = node; }} />)}</div></>;
}
