import React, { useMemo, useState } from 'react';
import toast from 'react-hot-toast';
import { ChevronUp, ChevronDown, Trash2, Plus, Save, Pencil, Eye, EyeOff, Search } from 'lucide-react';
import { Btn, Card, Field, Input, Textarea, Select, Toggle, FileField, Empty } from './ui';
import { adminApi } from './adminApi';

export type FieldDef = {
  key: string; label: string;
  type?: 'text' | 'textarea' | 'toggle' | 'number' | 'file' | 'select' | 'html' | 'url';
  options?: { value: string; label: string }[]; hint?: string; full?: boolean; accept?: string; folder?: string; placeholder?: string;
  showIf?: (item: any) => boolean;
};

export type CollectionSchema = {
  name: string; title: string; desc?: string; fields: FieldDef[];
  itemTitle: (it: any) => string; itemSub?: (it: any) => string; thumb?: (it: any) => string;
  blank: () => any; publishKey?: string; /* 'published' | 'approved' */
};

const SchemaFields: React.FC<{ fields: FieldDef[]; item: any; set: (patch: any) => void }> = ({ fields, item, set }) => (
  <div className="ad-grid">
    {fields.filter((f) => !f.showIf || f.showIf(item)).map((f) => {
      const v = item[f.key];
      const t = f.type || 'text';
      if (t === 'toggle') return <Field key={f.key} hint={f.hint}><Toggle checked={!!v} onChange={(x) => set({ [f.key]: x })} label={f.label} /></Field>;
      if (t === 'file') return <Field key={f.key} label={f.label} hint={f.hint} full><FileField value={v || ''} onChange={(x) => set({ [f.key]: x })} accept={f.accept} folder={f.folder} /></Field>;
      if (t === 'textarea' || t === 'html') return <Field key={f.key} label={f.label} hint={f.hint} full><Textarea rows={t === 'html' ? 12 : 4} value={v || ''} onChange={(e) => set({ [f.key]: e.target.value })} dir={t === 'html' ? 'ltr' : 'auto'} placeholder={f.placeholder} /></Field>;
      if (t === 'select') return <Field key={f.key} label={f.label} hint={f.hint}><Select value={v || ''} onChange={(e) => set({ [f.key]: e.target.value })}>{(f.options || []).map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}</Select></Field>;
      if (t === 'number') return <Field key={f.key} label={f.label} hint={f.hint}><Input type="number" value={v ?? ''} onChange={(e) => set({ [f.key]: Number(e.target.value) })} /></Field>;
      return <Field key={f.key} label={f.label} hint={f.hint} full={f.full}><Input value={v || ''} onChange={(e) => set({ [f.key]: e.target.value })} dir={t === 'url' ? 'ltr' : 'auto'} placeholder={f.placeholder} /></Field>;
    })}
  </div>
);

export const CollectionEditor: React.FC<{ schema: CollectionSchema; items: any[]; onSaved: (items: any[]) => void }> = ({ schema, items, onSaved }) => {
  const [list, setList] = useState<any[]>(items);
  const [open, setOpen] = useState<number | null>(null);
  const [dirty, setDirty] = useState(false);
  const [saving, setSaving] = useState(false);
  const [q, setQ] = useState('');
  const pk = schema.publishKey || 'published';

  const update = (next: any[]) => { setList(next); setDirty(true); };
  const move = (i: number, d: number) => { const j = i + d; if (j < 0 || j >= list.length) return; const a = [...list]; [a[i], a[j]] = [a[j], a[i]]; update(a); setOpen(j); };
  const remove = (i: number) => { if (!confirm('این مورد حذف شود؟')) return; update(list.filter((_, j) => j !== i)); setOpen(null); };
  const add = () => { const it = { ...schema.blank(), [pk]: true }; update([it, ...list]); setOpen(0); };
  const save = async () => {
    setSaving(true);
    try {
      const r = await adminApi<{ items: any[] }>('admin/collection', { name: schema.name, items: list });
      setList(r.items); onSaved(r.items); setDirty(false); toast.success('ذخیره شد ✅');
    } catch (e: any) { toast.error(e.message); }
    setSaving(false);
  };

  const visible = useMemo(() => list.map((it, i) => ({ it, i })).filter(({ it }) => !q || JSON.stringify(it).toLowerCase().includes(q.toLowerCase())), [list, q]);

  return (
    <Card title={schema.title} desc={schema.desc} actions={<>
      <div className="ad-search"><Search size={14} /><input value={q} onChange={(e) => setQ(e.target.value)} placeholder="جستجو…" /></div>
      <Btn onClick={add}><Plus size={14} /> مورد جدید</Btn>
      <Btn variant="primary" onClick={save} loading={saving} disabled={!dirty}><Save size={14} /> ذخیره تغییرات</Btn>
    </>}>
      {dirty && <div className="ad-notice">تغییرات ذخیره‌نشده دارید — روی «ذخیره تغییرات» بزنید.</div>}
      {list.length === 0 && <Empty text="هنوز موردی ثبت نشده است." />}
      <div className="ad-list">
        {visible.map(({ it, i }) => {
          const published = it[pk] !== false;
          const th = schema.thumb?.(it);
          return (
            <div key={it.id || i} className={`ad-item ${open === i ? 'open' : ''} ${published ? '' : 'draft'}`}>
              <div className="ad-item-head" onClick={() => setOpen(open === i ? null : i)}>
                {th ? <img src={th} alt="" className="ad-thumb" onError={(e) => ((e.target as HTMLImageElement).style.visibility = 'hidden')} /> : <div className="ad-thumb ad-thumb-empty">{i + 1}</div>}
                <div className="ad-item-text">
                  <strong>{schema.itemTitle(it) || '(بدون عنوان)'}</strong>
                  {schema.itemSub && <small>{schema.itemSub(it)}</small>}
                </div>
                <div className="ad-actions" onClick={(e) => e.stopPropagation()}>
                  <Btn size="sm" variant="ghost" title={published ? 'منتشر شده — برای مخفی کردن کلیک کنید' : 'پیش‌نویس — برای انتشار کلیک کنید'} onClick={() => update(list.map((x, j) => (j === i ? { ...x, [pk]: !published } : x)))}>{published ? <Eye size={14} /> : <EyeOff size={14} />}</Btn>
                  <Btn size="sm" variant="ghost" onClick={() => move(i, -1)} disabled={i === 0}><ChevronUp size={14} /></Btn>
                  <Btn size="sm" variant="ghost" onClick={() => move(i, 1)} disabled={i === list.length - 1}><ChevronDown size={14} /></Btn>
                  <Btn size="sm" variant="ghost" onClick={() => setOpen(open === i ? null : i)}><Pencil size={14} /></Btn>
                  <Btn size="sm" variant="danger" onClick={() => remove(i)}><Trash2 size={14} /></Btn>
                </div>
              </div>
              {open === i && (
                <div className="ad-item-body">
                  <SchemaFields fields={schema.fields} item={it} set={(patch) => update(list.map((x, j) => (j === i ? { ...x, ...patch } : x)))} />
                </div>
              )}
            </div>
          );
        })}
      </div>
    </Card>
  );
};

export { SchemaFields };
