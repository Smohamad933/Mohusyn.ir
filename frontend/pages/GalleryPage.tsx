import React, { useEffect, useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Camera, ArrowLeft, Instagram, X, ChevronLeft, ChevronRight } from 'lucide-react';
import { useContent } from '../context/ContentContext';

/** Photo gallery page — photos are managed in the admin panel (collection: gallery). */
const GalleryPage: React.FC = () => {
  const navigate = useNavigate();
  const { content } = useContent();
  const st = content.settings;
  const photos = content.gallery.filter(p => p.published !== false);
  const categories = useMemo(() => ['All', ...Array.from(new Set(photos.map(p => p.category).filter(Boolean)))], [photos]);
  const [cat, setCat] = useState('All');
  const [open, setOpen] = useState<number | null>(null);
  const shown = cat === 'All' ? photos : photos.filter(p => p.category === cat);
  const titles = st.sectionTitles.gallery;

  useEffect(() => {
    const prev = document.title;
    document.title = `${titles.title} – ${st.fullName}`;
    return () => { document.title = prev; };
  }, [titles.title, st.fullName]);

  useEffect(() => {
    if (open === null) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setOpen(null);
      if (e.key === 'ArrowRight') setOpen(o => (o === null ? null : (o + 1) % shown.length));
      if (e.key === 'ArrowLeft') setOpen(o => (o === null ? null : (o - 1 + shown.length) % shown.length));
    };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [open, shown.length]);

  return (
    <div className="min-h-screen text-slate-900 dark:text-white bg-slate-50 dark:bg-black">
      <header className="sticky top-0 z-50 border-b border-slate-200 dark:border-neutral-800 bg-white/80 dark:bg-black/80 backdrop-blur-md">
        <div className="flex items-center justify-between max-w-5xl px-4 py-4 mx-auto">
          <button onClick={() => navigate('/')} className="flex items-center gap-2 text-sm font-medium transition-colors text-slate-600 dark:text-neutral-400 hover:text-slate-900 dark:hover:text-white active:scale-95" aria-label="Back to Home">
            <ArrowLeft size={16} /> Back to Home
          </button>
          <h1 className="flex items-center gap-2 text-xl font-bold font-signature text-slate-900 dark:text-white">
            <Camera className="text-purple-500" size={22} aria-hidden="true" />
            <span>Life in <span className="text-purple-500">Frames</span></span>
          </h1>
          <div className="w-24" aria-hidden="true" />
        </div>
      </header>

      <main className="max-w-5xl px-4 py-10 mx-auto">
        <p className="mb-2 text-center text-slate-500 dark:text-neutral-400">{titles.subtitle}</p>
        <p className="mb-8 text-xs text-center text-slate-400 dark:text-neutral-600">Photos of {st.fullName}</p>

        {categories.length > 2 && (
          <div className="flex flex-wrap justify-center gap-2 mb-8">
            {categories.map(c => (
              <button key={c} onClick={() => setCat(c)}
                className={`px-4 py-1.5 text-xs font-bold rounded-full border transition-all ${cat === c ? 'bg-purple-600 border-purple-600 text-white' : 'border-slate-200 dark:border-neutral-800 text-slate-500 hover:border-purple-400'}`}>
                {c}
              </button>
            ))}
          </div>
        )}

        <div className="grid grid-cols-2 gap-4 md:grid-cols-3" role="list" aria-label={`Photo gallery of ${st.fullName}`}>
          {shown.map((photo, index) => (
            <figure key={photo.id} role="listitem" onClick={() => setOpen(index)}
              className="relative overflow-hidden transition-all shadow-md cursor-pointer group rounded-xl aspect-square hover:shadow-xl transform-gpu bg-slate-100 dark:bg-neutral-900">
              <img src={photo.src} alt={photo.caption || photo.title} title={photo.title} loading={index < 4 ? 'eager' : 'lazy'} decoding="async" width={600} height={600}
                className="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110 will-change-transform" />
              <figcaption className="absolute inset-0 flex items-center justify-center transition-opacity duration-300 opacity-0 bg-black/60 group-hover:opacity-100">
                <span className="px-4 py-1 text-sm font-medium text-white border rounded-full border-white/30 bg-black/30 backdrop-blur-sm">{photo.caption || photo.title}</span>
              </figcaption>
            </figure>
          ))}
        </div>
        {!shown.length && <p className="py-20 text-center text-slate-400">No photos yet.</p>}

        {st.feed.profileUrl && (
          <div className="pt-8 mt-10 text-center border-t border-slate-200 dark:border-neutral-800">
            <p className="mb-3 text-sm text-slate-500 dark:text-neutral-400">More photos of {st.fullName}</p>
            <a href={st.feed.profileUrl} target="_blank" rel="noopener noreferrer" className="inline-flex items-center gap-2 text-sm font-medium text-blue-500 hover:underline">
              <Instagram size={16} aria-hidden="true" /> See more on {st.feed.network}
            </a>
          </div>
        )}
      </main>

      {open !== null && shown[open] && (
        <div className="fixed inset-0 z-[10000] flex items-center justify-center bg-black/95 backdrop-blur-sm" onClick={() => setOpen(null)}>
          <button className="absolute p-2 text-white rounded-full top-4 right-4 bg-white/10 hover:bg-red-500" onClick={() => setOpen(null)} aria-label="Close"><X size={22} /></button>
          <button className="absolute p-3 text-white rounded-full left-3 bg-white/10 hover:bg-white/20" onClick={e => { e.stopPropagation(); setOpen((open - 1 + shown.length) % shown.length); }} aria-label="Previous"><ChevronLeft /></button>
          <figure className="max-w-[92vw] max-h-[90vh]" onClick={e => e.stopPropagation()}>
            <img src={shown[open].src} alt={shown[open].caption} className="max-w-[92vw] max-h-[80vh] object-contain rounded-xl shadow-2xl" />
            <figcaption className="mt-3 text-sm text-center text-white/70">{shown[open].title}{shown[open].caption ? ` — ${shown[open].caption}` : ''}</figcaption>
          </figure>
          <button className="absolute p-3 text-white rounded-full right-3 bg-white/10 hover:bg-white/20" onClick={e => { e.stopPropagation(); setOpen((open + 1) % shown.length); }} aria-label="Next"><ChevronRight /></button>
        </div>
      )}
    </div>
  );
};

export default GalleryPage;
