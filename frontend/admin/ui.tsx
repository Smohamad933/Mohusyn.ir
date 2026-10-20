import React, { useRef, useState } from 'react';
import { Upload, Trash2, ChevronUp, ChevronDown, Plus, X, Loader2 } from 'lucide-react';
import { adminUpload } from './adminApi';

/* ───────── Primitives (RTL, Persian) ───────── */

export const Card: React.FC<{ title?: string; desc?: string; children: React.ReactNode; actions?: React.ReactNode; className?: string }> = ({ title, desc, children, actions, className = '' }) => (
  <section className={`ad-card ${className}`}>
    {(title || actions) && (
      <header className="ad-card-head">
        <div>{title && <h3>{title}</h3>}{desc && <p>{desc}</p>}</div>
        {actions && <div className="ad-actions">{actions}</div>}
      </header>
    )}
    {children}
  </section>
);

export const Btn: React.FC<React.ButtonHTMLAttributes<HTMLButtonElement> & { variant?: 'primary' | 'ghost' | 'danger' | 'soft'; loading?: boolean; size?: 'sm' | 'md' }> =
  ({ variant = 'soft', loading, size = 'md', children, className = '', ...rest }) => (
    <button {...rest} disabled={rest.disabled || loading} className={`ad-btn ad-btn-${variant} ad-btn-${size} ${className}`}>
      {loading && <Loader2 size={14} className="spin" />}{children}
    </button>
  );

export const Field: React.FC<{ label?: string; hint?: string; children: React.ReactNode; full?: boolean }> = ({ label, hint, children, full }) => (
  <label className={`ad-field ${full ? 'full' : ''}`}>
    {label && <span className="ad-label">{label}</span>}
    {children}
    {hint && <span className="ad-hint">{hint}</span>}
  </label>
);

export const Input: React.FC<React.InputHTMLAttributes<HTMLInputElement>> = (p) => <input {...p} className={`ad-input ${p.className || ''}`} />;
export const Textarea: React.FC<React.TextareaHTMLAttributes<HTMLTextAreaElement>> = (p) => <textarea {...p} className={`ad-input ad-textarea ${p.className || ''}`} />;
export const Select: React.FC<React.SelectHTMLAttributes<HTMLSelectElement>> = (p) => <select {...p} className={`ad-input ${p.className || ''}`} />;

export const Toggle: React.FC<{ checked: boolean; onChange: (v: boolean) => void; label?: string }> = ({ checked, onChange, label }) => (
  <button type="button" className={`ad-toggle ${checked ? 'on' : ''}`} onClick={() => onChange(!checked)} aria-pressed={checked}>
    <span className="ad-toggle-knob" />{label && <span className="ad-toggle-label">{label}</span>}
  </button>
);

/** Text field + upload button. Stores the URL (e.g. /uploads/x.jpg). */
export const FileField: React.FC<{ value: string; onChange: (v: string) => void; accept?: string; folder?: string; preview?: boolean }> =
  ({ value, onChange, accept = 'image/*', folder = 'images', preview = true }) => {
    const ref = useRef<HTMLInputElement>(null);
    const [busy, setBusy] = useState(false);
    const [err, setErr] = useState('');
    const isImg = /\.(jpe?g|png|gif|webp|svg|ico)(\?|$)/i.test(value || '');
    const pick = async (f?: File) => {
      if (!f) return;
      setBusy(true); setErr('');
      try { const r = await adminUpload(f, folder); onChange(r.url); } catch (e: any) { setErr(e.message || 'خطا در آپلود'); }
      setBusy(false);
    };
    return (
      <div className="ad-file">
        {preview && isImg && value && <img src={value} alt="" className="ad-file-preview" onError={(e) => ((e.target as HTMLImageElement).style.display = 'none')} />}
        <div className="ad-file-row">
          <Input value={value || ''} onChange={(e) => onChange(e.target.value)} placeholder="آدرس فایل یا لینک (https://…)" dir="ltr" />
          <Btn type="button" size="sm" loading={busy} onClick={() => ref.current?.click()}><Upload size={14} /> آپلود</Btn>
          {value && <Btn type="button" size="sm" variant="ghost" onClick={() => onChange('')} title="پاک کردن"><X size={14} /></Btn>}
        </div>
        {err && <div className="ad-error">{err}</div>}
        <input ref={ref} type="file" accept={accept} hidden onChange={(e) => pick(e.target.files?.[0])} />
      </div>
    );
  };

/** Simple list of strings (tags) */
export const StringList: React.FC<{ value: string[]; onChange: (v: string[]) => void; placeholder?: string }> = ({ value, onChange, placeholder }) => {
  const [draft, setDraft] = useState('');
  const add = () => { const t = draft.trim(); if (!t) return; onChange([...(value || []), t]); setDraft(''); };
  return (
    <div className="ad-tags">
      <div className="ad-tag-list">
        {(value || []).map((t, i) => (
          <span key={i} className="ad-tag">{t}<button type="button" onClick={() => onChange(value.filter((_, j) => j !== i))}><X size={12} /></button></span>
        ))}
      </div>
      <div className="ad-file-row">
        <Input value={draft} onChange={(e) => setDraft(e.target.value)} placeholder={placeholder || 'مورد جدید… (Enter)'} onKeyDown={(e) => { if (e.key === 'Enter') { e.preventDefault(); add(); } }} />
        <Btn type="button" size="sm" onClick={add}><Plus size={14} /></Btn>
      </div>
    </div>
  );
};

/** Generic reorderable list of objects, rendered with a row renderer. */
export function ObjectList<T extends object>({ value, onChange, blank, render, title }: { value: T[]; onChange: (v: T[]) => void; blank: () => T; render: (item: T, set: (patch: Partial<T>) => void, idx: number) => React.ReactNode; title?: (item: T, idx: number) => string }) {
  const list = value || [];
  const move = (i: number, d: number) => { const j = i + d; if (j < 0 || j >= list.length) return; const a = [...list]; [a[i], a[j]] = [a[j], a[i]]; onChange(a); };
  return (
    <div className="ad-objlist">
      {list.map((it, i) => (
        <div key={i} className="ad-objrow">
          <div className="ad-objrow-head">
            <strong>{title ? title(it, i) : `#${i + 1}`}</strong>
            <div className="ad-actions">
              <Btn type="button" size="sm" variant="ghost" onClick={() => move(i, -1)} disabled={i === 0}><ChevronUp size={14} /></Btn>
              <Btn type="button" size="sm" variant="ghost" onClick={() => move(i, 1)} disabled={i === list.length - 1}><ChevronDown size={14} /></Btn>
              <Btn type="button" size="sm" variant="danger" onClick={() => { if (confirm('حذف شود؟')) onChange(list.filter((_, j) => j !== i)); }}><Trash2 size={14} /></Btn>
            </div>
          </div>
          <div className="ad-grid">{render(it, (patch) => onChange(list.map((x, j) => (j === i ? { ...x, ...patch } : x))), i)}</div>
        </div>
      ))}
      <Btn type="button" onClick={() => onChange([...list, blank()])}><Plus size={14} /> افزودن</Btn>
    </div>
  );
}

export const Empty: React.FC<{ text: string }> = ({ text }) => <div className="ad-empty">{text}</div>;
