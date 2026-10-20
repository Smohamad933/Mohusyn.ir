import React from "react";
import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}
/** Extract a YouTube video id from any YouTube URL (or return the input if it already looks like an id). */
export function ytId(url: string): string {
  if (!url) return '';
  if (/^[A-Za-z0-9_-]{6,15}$/.test(url)) return url;
  const m = url.match(/(?:v=|youtu\.be\/|embed\/|shorts\/|live\/)([A-Za-z0-9_-]{6,15})/);
  return m ? m[1] : '';
}

/** Build a YouTube embed URL with common options. */
export function ytEmbed(url: string, o: { autoplay?: boolean; mute?: boolean; loop?: boolean; controls?: boolean } = {}): string {
  const id = ytId(url);
  if (!id) return '';
  const q = [
    o.autoplay ? 'autoplay=1' : '', o.mute ? 'mute=1' : '', o.loop ? `loop=1&playlist=${id}` : '',
    o.controls === false ? 'controls=0&showinfo=0' : '', 'rel=0', 'modestbranding=1', 'iv_load_policy=3',
  ].filter(Boolean).join('&');
  return `https://www.youtube.com/embed/${id}?${q}`;
}

/** Render **bold** markers in a plain string as <strong>. */
export function boldify(text: string): (string | JSX.Element)[] {
  const parts = (text || '').split(/(\*\*[^*]+\*\*)/g);
  return parts.map((p, i) => (p.startsWith('**') && p.endsWith('**') ? <strong key={i} className="font-bold text-slate-900 dark:text-white">{p.slice(2, -2)}</strong> : p));
}
