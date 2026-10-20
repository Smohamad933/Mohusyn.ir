import React, { useEffect, useState } from 'react';
import { Link, Navigate, Route, Routes, useLocation, useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import {
  LayoutDashboard, Settings as SettingsIcon, FolderKanban, Download, Rss, Milestone, MessageSquare, Camera, Lock, FileText,
  Mail, Users, Image as ImageIcon, KeyRound, LogOut, ExternalLink, Trash2, Check, RefreshCw, Copy,
} from 'lucide-react';
import './admin.css';
import { Content } from '../types';
import { DEFAULT_CONTENT } from '../lib/defaultContent';
import { adminApi, setCsrf } from './adminApi';
import { Btn, Card, Field, Input, Empty } from './ui';
import { SettingsEditor } from './SettingsEditor';
import { CollectionEditor } from './CollectionEditor';
import { SCHEMAS, COLLECTION_ORDER } from './schemas';

const ICONS: Record<string, React.ReactNode> = {
  projects: <FolderKanban size={16} />, resources: <Download size={16} />, feed: <Rss size={16} />, timeline: <Milestone size={16} />,
  testimonials: <MessageSquare size={16} />, gallery: <Camera size={16} />, vault: <Lock size={16} />, pages: <FileText size={16} />,
};

/* ───────── Login ───────── */
const Login: React.FC<{ onOk: () => void }> = ({ onOk }) => {
  const [pw, setPw] = useState('');
  const [busy, setBusy] = useState(false);
  const submit = async (e: React.FormEvent) => {
    e.preventDefault(); setBusy(true);
    try { await adminApi('admin/login', { password: pw }); onOk(); } catch (err: any) { toast.error(err.message); }
    setBusy(false);
  };
  return (
    <div className="ad-login">
      <form onSubmit={submit} className="ad-login-box">
        <div className="ad-logo">M</div>
        <h1>پنل مدیریت</h1>
        <p>برای ورود رمز عبور مدیریت را وارد کنید.</p>
        <Input type="password" value={pw} onChange={(e) => setPw(e.target.value)} placeholder="رمز عبور" autoFocus dir="ltr" />
        <Btn variant="primary" type="submit" loading={busy} className="w-full">ورود</Btn>
        <Link to="/" className="ad-back">← بازگشت به سایت</Link>
      </form>
    </div>
  );
};

/* ───────── Dashboard ───────── */
const Dashboard: React.FC<{ content: Content }> = ({ content }) => {
  const [st, setSt] = useState<any>(null);
  useEffect(() => { adminApi('admin/stats').then(setSt).catch(() => setSt({ error: true })); }, []);
  const days = st?.stats ? Object.keys(st.stats).sort() : [];
  const views = (n: number) => days.slice(-n).reduce((a, d) => a + (st.stats[d]?.views || 0), 0);
  const last14 = days.slice(-14);
  const max = Math.max(1, ...last14.map((d) => st.stats[d]?.views || 0));
  return (
    <>
      <div className="ad-stats">
        <div className="ad-stat"><span>بازدید امروز</span><b>{st?.stats ? (st.stats[new Date().toISOString().slice(0, 10)]?.views || 0) : '…'}</b></div>
        <div className="ad-stat"><span>بازدید ۷ روز</span><b>{st?.stats ? views(7) : '…'}</b></div>
        <div className="ad-stat"><span>بازدید ۳۰ روز</span><b>{st?.stats ? views(30) : '…'}</b></div>
        <div className="ad-stat"><span>پیام‌های خوانده‌نشده</span><b>{st?.unread ?? '…'}</b></div>
        <div className="ad-stat"><span>نظرات در انتظار تأیید</span><b>{st?.pendingTestimonials ?? '…'}</b></div>
        <div className="ad-stat"><span>کاربران ثبت‌نامی</span><b>{st?.users ?? '…'}</b></div>
      </div>
      <div className="ad-two">
        <Card title="بازدید ۱۴ روز اخیر">
          {last14.length === 0 ? <Empty text="هنوز آماری ثبت نشده است." /> : (
            <div className="ad-bars">{last14.map((d) => <div key={d} className="ad-bar" title={`${d}: ${st.stats[d].views}`}><i style={{ height: `${(st.stats[d].views / max) * 100}%` }} /><small>{d.slice(5)}</small></div>)}</div>
          )}
        </Card>
        <Card title="وضعیت سیستم">
          <ul className="ad-kv">
            <li><span>نسخه PHP</span><b dir="ltr">{st?.php || '—'}</b></li>
            <li><span>دسترسی نوشتن data/ و uploads/</span><b>{st ? (st.writable ? '✅ درست است' : '❌ خیر — دسترسی Modify به IIS_IUSRS بدهید') : '…'}</b></li>
            <li><span>پروژه‌ها</span><b>{content.projects.length}</b></li>
            <li><span>آیتم‌های گالری</span><b>{content.gallery.length}</b></li>
            <li><span>آهنگ‌ها</span><b>{content.settings.music.tracks.length}</b></li>
          </ul>
          <div className="ad-actions" style={{ marginTop: 12 }}>
            <a className="ad-btn ad-btn-soft ad-btn-md" href="/" target="_blank" rel="noreferrer"><ExternalLink size={14} /> مشاهده سایت</a>
          </div>
        </Card>
      </div>
    </>
  );
};

/* ───────── Messages ───────── */
const Messages: React.FC = () => {
  const [msgs, setMsgs] = useState<any[] | null>(null);
  const load = () => adminApi<{ messages: any[] }>('admin/messages').then((r) => setMsgs(r.messages)).catch((e) => toast.error(e.message));
  useEffect(() => { load(); }, []);
  const act = async (body: any) => { try { const r = await adminApi<{ messages: any[] }>('admin/messages', body); setMsgs(r.messages); } catch (e: any) { toast.error(e.message); } };
  return (
    <Card title="پیام‌های فرم تماس" actions={<Btn onClick={load}><RefreshCw size={14} /> بروزرسانی</Btn>}>
      {!msgs ? <Empty text="در حال بارگذاری…" /> : msgs.length === 0 ? <Empty text="پیامی وجود ندارد." /> : (
        <div className="ad-msgs">
          {msgs.map((m) => (
            <div key={m.id} className={`ad-msg ${m.read ? '' : 'unread'}`}>
              <div className="ad-msg-head">
                <div><b>{m.name}</b> {m.email && <a href={`mailto:${m.email}`} dir="ltr">{m.email}</a>}<small> · {new Date(m.createdAt).toLocaleString('fa-IR')}</small></div>
                <div className="ad-actions">
                  {!m.read && <Btn size="sm" onClick={() => act({ id: m.id, read: true })}><Check size={14} /> خوانده شد</Btn>}
                  <Btn size="sm" variant="danger" onClick={() => { if (confirm('حذف پیام؟')) act({ id: m.id, delete: true }); }}><Trash2 size={14} /></Btn>
                </div>
              </div>
              {m.subject && <div className="ad-msg-subject">{m.subject}</div>}
              <p dir="auto">{m.message}</p>
            </div>
          ))}
        </div>
      )}
    </Card>
  );
};

/* ───────── Users ───────── */
const UsersPage: React.FC = () => {
  const [users, setUsers] = useState<any[] | null>(null);
  useEffect(() => { adminApi<{ users: any[] }>('admin/users').then((r) => setUsers(r.users)).catch((e) => toast.error(e.message)); }, []);
  const del = async (id: string) => { if (!confirm('حذف کاربر؟')) return; try { const r = await adminApi<{ users: any[] }>('admin/users', { id, delete: true }); setUsers(r.users); } catch (e: any) { toast.error(e.message); } };
  return (
    <Card title="کاربران ثبت‌نام‌کرده در سایت" desc="بازدیدکنندگانی که با دکمه Sign In / Register حساب ساخته‌اند.">
      {!users ? <Empty text="در حال بارگذاری…" /> : users.length === 0 ? <Empty text="هنوز کاربری ثبت‌نام نکرده است." /> : (
        <table className="ad-table"><thead><tr><th>نام</th><th>ایمیل</th><th>تاریخ</th><th></th></tr></thead>
          <tbody>{users.map((u) => <tr key={u.id}><td>{u.displayName}</td><td dir="ltr">{u.email}</td><td>{new Date(u.createdAt).toLocaleDateString('fa-IR')}</td><td><Btn size="sm" variant="danger" onClick={() => del(u.id)}><Trash2 size={14} /></Btn></td></tr>)}</tbody></table>
      )}
    </Card>
  );
};

/* ───────── Media ───────── */
const Media: React.FC = () => {
  const [files, setFiles] = useState<any[] | null>(null);
  const load = () => adminApi<{ files: any[] }>('admin/media').then((r) => setFiles(r.files)).catch((e) => toast.error(e.message));
  useEffect(() => { load(); }, []);
  const upload = async (list: FileList | null) => {
    if (!list) return;
    const { adminUpload } = await import('./adminApi');
    for (const f of Array.from(list)) { try { await adminUpload(f, 'media'); } catch (e: any) { toast.error(`${f.name}: ${e.message}`); } }
    toast.success('آپلود شد'); load();
  };
  const del = async (url: string) => { if (!confirm('فایل حذف شود؟')) return; try { await adminApi('admin/media', { url, delete: true }); load(); } catch (e: any) { toast.error(e.message); } };
  const copy = (u: string) => { navigator.clipboard?.writeText(u); toast.success('آدرس کپی شد'); };
  const fmt = (n: number) => (n > 1e6 ? (n / 1e6).toFixed(1) + ' MB' : (n / 1e3).toFixed(0) + ' KB');
  return (
    <Card title="رسانه‌ها (پوشه uploads)" actions={<label className="ad-btn ad-btn-primary ad-btn-md">آپلود فایل<input type="file" multiple hidden onChange={(e) => upload(e.target.files)} /></label>}>
      {!files ? <Empty text="در حال بارگذاری…" /> : files.length === 0 ? <Empty text="فایلی آپلود نشده است." /> : (
        <div className="ad-media">
          {files.map((f) => (
            <div key={f.url} className="ad-media-item">
              {f.image ? <img src={f.url} alt="" /> : <div className="ad-media-file">{f.name.split('.').pop()}</div>}
              <div className="ad-media-meta"><span title={f.name}>{f.name}</span><small>{fmt(f.size)}</small></div>
              <div className="ad-actions">
                <Btn size="sm" onClick={() => copy(f.url)} title="کپی آدرس"><Copy size={13} /></Btn>
                <a className="ad-btn ad-btn-soft ad-btn-sm" href={f.url} target="_blank" rel="noreferrer"><ExternalLink size={13} /></a>
                <Btn size="sm" variant="danger" onClick={() => del(f.url)}><Trash2 size={13} /></Btn>
              </div>
            </div>
          ))}
        </div>
      )}
    </Card>
  );
};

/* ───────── Password ───────── */
const Password: React.FC = () => {
  const [a, setA] = useState(''); const [b, setB] = useState(''); const [busy, setBusy] = useState(false);
  const save = async () => {
    if (a.length < 6) return toast.error('حداقل ۶ کاراکتر');
    if (a !== b) return toast.error('تکرار رمز یکسان نیست');
    setBusy(true);
    try { await adminApi('admin/password', { password: a }); toast.success('رمز تغییر کرد'); setA(''); setB(''); } catch (e: any) { toast.error(e.message); }
    setBusy(false);
  };
  return (
    <Card title="تغییر رمز مدیریت" desc="رمز جدید به‌صورت هش‌شده در data/_admin.json ذخیره می‌شود و جایگزین رمز پیش‌فرض فایل config.php می‌گردد.">
      <div className="ad-grid">
        <Field label="رمز جدید"><Input type="password" dir="ltr" value={a} onChange={(e) => setA(e.target.value)} /></Field>
        <Field label="تکرار رمز"><Input type="password" dir="ltr" value={b} onChange={(e) => setB(e.target.value)} /></Field>
      </div>
      <Btn variant="primary" onClick={save} loading={busy}><KeyRound size={14} /> ذخیره رمز</Btn>
    </Card>
  );
};

/* ───────── Shell ───────── */
const NAV = [
  { to: '', label: 'داشبورد', icon: <LayoutDashboard size={16} /> },
  { to: 'settings', label: 'تنظیمات سایت', icon: <SettingsIcon size={16} /> },
  ...COLLECTION_ORDER.map((c) => ({ to: c, label: SCHEMAS[c].title.split(' (')[0], icon: ICONS[c] })),
  { to: 'messages', label: 'پیام‌ها', icon: <Mail size={16} /> },
  { to: 'users', label: 'کاربران', icon: <Users size={16} /> },
  { to: 'media', label: 'رسانه‌ها', icon: <ImageIcon size={16} /> },
  { to: 'password', label: 'رمز مدیریت', icon: <KeyRound size={16} /> },
];

const AdminApp: React.FC = () => {
  const [auth, setAuth] = useState<boolean | null>(null);
  const [content, setContent] = useState<Content | null>(null);
  const [menuOpen, setMenuOpen] = useState(false);
  const loc = useLocation();
  const nav = useNavigate();

  useEffect(() => { document.documentElement.classList.add('dark'); document.title = 'پنل مدیریت'; }, []);
  useEffect(() => { adminApi<{ admin: boolean; csrf: string }>('admin/me').then((r) => { setCsrf(r.csrf); setAuth(r.admin); }).catch(() => setAuth(false)); }, []);

  const load = async () => {
    try {
      const r = await adminApi<{ content: Partial<Content> }>('admin/content');
      const c: any = { ...DEFAULT_CONTENT, ...r.content, settings: deepMerge(DEFAULT_CONTENT.settings, r.content.settings || {}) };
      setContent(c);
    } catch (e: any) { toast.error(e.message); }
  };
  useEffect(() => { if (auth) load(); }, [auth]);
  useEffect(() => { setMenuOpen(false); }, [loc.pathname]);

  const logout = async () => { await adminApi('admin/logout', {}); setAuth(false); nav('/admin'); };

  if (auth === null) return <div className="ad-root"><div className="ad-loading">در حال بارگذاری…</div></div>;
  if (!auth) return <div className="ad-root"><Login onOk={() => setAuth(true)} /></div>;

  const sub = loc.pathname.replace(/^\/admin\/?/, '').split('/')[0];
  const pageKey = (content ? loc.pathname : '') + (content ? '#loaded' : '');

  return (
    <div className="ad-root">
      <header className="ad-topbar">
        <button className="ad-burger" onClick={() => setMenuOpen(!menuOpen)}>☰</button>
        <div className="ad-brand"><span className="ad-logo sm">M</span> پنل مدیریت</div>
        <div className="ad-actions">
          <a className="ad-btn ad-btn-ghost ad-btn-sm" href="/" target="_blank" rel="noreferrer"><ExternalLink size={14} /> سایت</a>
          <Btn size="sm" variant="ghost" onClick={logout}><LogOut size={14} /> خروج</Btn>
        </div>
      </header>
      <div className="ad-body">
        <aside className={`ad-side ${menuOpen ? 'open' : ''}`}>
          {NAV.map((n) => <Link key={n.to} to={`/admin/${n.to}`} className={`ad-nav ${sub === n.to ? 'active' : ''}`}>{n.icon}<span>{n.label}</span></Link>)}
        </aside>
        <main className="ad-main">
          {!content ? <div className="ad-loading">در حال بارگذاری محتوا…</div> : (
            <Routes key={pageKey}>
              <Route index element={<Dashboard content={content} />} />
              <Route path="settings" element={<SettingsEditor settings={content.settings} onSaved={(s) => setContent({ ...content, settings: s })} />} />
              {COLLECTION_ORDER.map((c) => (
                <Route key={c} path={c} element={<CollectionEditor schema={SCHEMAS[c]} items={(content as any)[c] || []} onSaved={(items) => setContent({ ...content, [c]: items } as Content)} />} />
              ))}
              <Route path="messages" element={<Messages />} />
              <Route path="users" element={<UsersPage />} />
              <Route path="media" element={<Media />} />
              <Route path="password" element={<Password />} />
              <Route path="*" element={<Navigate to="/admin" replace />} />
            </Routes>
          )}
        </main>
      </div>
    </div>
  );
};

function deepMerge<T>(base: T, over: any): T {
  if (Array.isArray(base)) return (Array.isArray(over) ? over : base) as T;
  if (base && typeof base === 'object') {
    const out: any = { ...base };
    if (over && typeof over === 'object') for (const k of Object.keys(over)) out[k] = k in (base as any) ? deepMerge((base as any)[k], over[k]) : over[k];
    return out;
  }
  return (over === undefined || over === null ? base : over) as T;
}

export default AdminApp;
