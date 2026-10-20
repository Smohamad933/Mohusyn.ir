import React, { useEffect, useMemo, useState } from 'react';

const CELL = 56;

/** Full-page "square grid" backdrop (inspired by apple-notch.vercel.app): faint grid lines,
 *  a soft spotlight that follows the pointer and a handful of cells that randomly light up. */
const SquareGridBackground: React.FC<{ cells?: number }> = ({ cells = 14 }) => {
  const [size, setSize] = useState({ w: 1920, h: 1080 });
  const [tick, setTick] = useState(0);
  useEffect(() => {
    const onResize = () => setSize({ w: window.innerWidth, h: window.innerHeight });
    onResize();
    window.addEventListener('resize', onResize);
    const t = setInterval(() => setTick((x) => x + 1), 4200);
    const onMove = (e: MouseEvent) => {
      document.documentElement.style.setProperty('--grid-x', e.clientX + 'px');
      document.documentElement.style.setProperty('--grid-y', e.clientY + 'px');
    };
    window.addEventListener('mousemove', onMove, { passive: true });
    return () => { window.removeEventListener('resize', onResize); window.removeEventListener('mousemove', onMove); clearInterval(t); };
  }, []);

  const lit = useMemo(() => {
    const cols = Math.ceil(size.w / CELL), rows = Math.ceil(size.h / CELL);
    return Array.from({ length: cells }, (_, i) => ({
      id: `${tick}-${i}`,
      x: Math.floor(Math.random() * cols) * CELL,
      y: Math.floor(Math.random() * rows) * CELL,
      delay: Math.random() * 2.5,
      dur: 2.5 + Math.random() * 2,
      hue: Math.random() > 0.5 ? 'violet' : 'pink',
    }));
  }, [size, tick, cells]);

  return (
    <div className="grid-bg" aria-hidden="true">
      <div className="grid-bg-lines" />
      <div className="grid-bg-spot" />
      {lit.map((c) => (
        <span key={c.id} className={`grid-bg-cell ${c.hue}`} style={{ left: c.x, top: c.y, width: CELL - 1, height: CELL - 1, animationDelay: `${c.delay}s`, animationDuration: `${c.dur}s` }} />
      ))}
      <div className="grid-bg-fade" />
    </div>
  );
};

export default SquareGridBackground;
