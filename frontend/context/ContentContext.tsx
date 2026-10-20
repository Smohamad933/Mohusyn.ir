import React, { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { Content } from '../types';
import { DEFAULT_CONTENT } from '../lib/defaultContent';
import { api } from '../lib/api';

interface Ctx { content: Content; loading: boolean; reload: () => Promise<void>; }
const ContentContext = createContext<Ctx>({ content: DEFAULT_CONTENT, loading: true, reload: async () => {} });

/** Deep-merge API content over defaults so new fields never crash old data files. */
function merge<T>(base: T, over: any): T {
  if (Array.isArray(base)) return (Array.isArray(over) ? over : base) as T;
  if (base && typeof base === 'object') {
    const out: any = { ...base };
    if (over && typeof over === 'object') {
      for (const k of Object.keys(over)) out[k] = k in (base as any) ? merge((base as any)[k], over[k]) : over[k];
    }
    return out;
  }
  return (over === undefined || over === null ? base : over) as T;
}

function applySeo(st: Content['settings']) {
  try {
    if (st.seo?.title) document.title = st.seo.title;
    const setMeta = (sel: string, attr: string, val: string) => { const el = document.querySelector(sel) as HTMLMetaElement | null; if (el && val) el.setAttribute(attr, val); };
    setMeta('meta[name="description"]', 'content', st.seo?.description || '');
    setMeta('meta[name="keywords"]', 'content', st.seo?.keywords || '');
    setMeta('meta[property="og:title"]', 'content', st.seo?.title || '');
    setMeta('meta[property="og:description"]', 'content', st.seo?.description || '');
    setMeta('meta[property="og:image"]', 'content', st.ogImage || '');
  } catch { /* ignore */ }
}

export const ContentProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [content, setContent] = useState<Content>(DEFAULT_CONTENT);
  const [loading, setLoading] = useState(true);
  const reload = async () => {
    try {
      const data = await api<{ ok: boolean; content: Partial<Content> }>('content');
      const merged = merge(DEFAULT_CONTENT, data.content);
      setContent(merged);
      (window as any).__SITE_SETTINGS__ = merged.settings;
      applySeo(merged.settings);
      if (!location.pathname.startsWith('/admin') && !sessionStorage.getItem('mh_viewed')) {
        sessionStorage.setItem('mh_viewed', '1');
        api('track', { kind: 'view' }).catch(() => {});
      }
    } catch (e) { console.warn('[content] falling back to defaults', e); }
    setLoading(false);
  };
  useEffect(() => { reload(); }, []);
  return <ContentContext.Provider value={{ content, loading, reload }}>{children}</ContentContext.Provider>;
};

export const useContent = () => useContext(ContentContext);
export const useSettings = () => useContext(ContentContext).content.settings;
