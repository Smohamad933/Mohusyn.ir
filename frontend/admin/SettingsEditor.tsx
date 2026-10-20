import React, { useState } from 'react';
import toast from 'react-hot-toast';
import { Save } from 'lucide-react';
import { Settings } from '../types';
import { Btn, Card, Field, Input, Textarea, Toggle, FileField, StringList, ObjectList } from './ui';
import { adminApi } from './adminApi';

type Tab = 'identity' | 'hero' | 'about' | 'socials' | 'sections' | 'music' | 'misc';
const TABS: { id: Tab; label: string }[] = [
  { id: 'identity', label: 'هویت و سئو' }, { id: 'hero', label: 'هدر (Hero)' }, { id: 'about', label: 'درباره من' },
  { id: 'socials', label: 'تماس و شبکه‌ها' }, { id: 'sections', label: 'بخش‌ها و عنوان‌ها' }, { id: 'music', label: 'موزیک‌پلیر' }, { id: 'misc', label: 'سایر' },
];

const SECTION_LABELS: Record<string, string> = {
  about: 'درباره من', projects: 'پروژه‌ها', resources: 'منابع دانلودی', feed: 'فید شبکه اجتماعی', timeline: 'مسیر/تایم‌لاین', testimonials: 'نظرات', contact: 'تماس',
  marquee: 'نوار لوگوی ابزارها', gallery: 'گالری (منو)', tools: 'ابزارها (منو)', vault: 'گاوصندوق (منو)', music: 'موزیک‌پلیر', dock: 'نوار پایین (Dock)', island: 'Dynamic Island',
  preloader: 'صفحه لودینگ', welcome: 'پیام خوش‌آمد', contextMenu: 'منوی راست‌کلیک', auth: 'ورود/ثبت‌نام بازدیدکننده',
};
const TITLE_LABELS: Record<string, string> = { projects: 'پروژه‌ها', resources: 'منابع', feed: 'فید', marquee: 'نوار ابزارها', timeline: 'تایم‌لاین', testimonials: 'نظرات', contact: 'تماس', gallery: 'گالری' };

export const SettingsEditor: React.FC<{ settings: Settings; onSaved: (s: Settings) => void }> = ({ settings, onSaved }) => {
  const [s, setS] = useState<Settings>(settings);
  const [tab, setTab] = useState<Tab>('identity');
  const [dirty, setDirty] = useState(false);
  const [saving, setSaving] = useState(false);
  const set = (patch: Partial<Settings>) => { setS((p) => ({ ...p, ...patch })); setDirty(true); };
  const setIn = <K extends keyof Settings>(k: K, patch: Partial<Settings[K]>) => set({ [k]: { ...(s[k] as any), ...patch } } as any);

  const save = async () => {
    setSaving(true);
    try { await adminApi('admin/settings', { settings: s }); onSaved(s); setDirty(false); toast.success('تنظیمات ذخیره شد ✅'); }
    catch (e: any) { toast.error(e.message); }
    setSaving(false);
  };

  const T = (label: string, k: keyof Settings, opts: { dir?: string; full?: boolean; hint?: string } = {}) => (
    <Field label={label} full={opts.full} hint={opts.hint}><Input value={(s[k] as any) || ''} dir={opts.dir} onChange={(e) => set({ [k]: e.target.value } as any)} /></Field>
  );

  return (
    <Card title="تنظیمات سایت" actions={<Btn variant="primary" onClick={save} loading={saving} disabled={!dirty}><Save size={14} /> ذخیره تنظیمات</Btn>}>
      {dirty && <div className="ad-notice">تغییرات ذخیره‌نشده دارید.</div>}
      <div className="ad-tabs">{TABS.map((t) => <button key={t.id} className={tab === t.id ? 'active' : ''} onClick={() => setTab(t.id)}>{t.label}</button>)}</div>

      {tab === 'identity' && (
        <div className="ad-grid">
          {T('نام سایت / برند (منو)', 'siteName')}
          {T('نام کوچک', 'firstName')}
          {T('نام کامل', 'fullName', { full: true })}
          {T('شغل / تگ‌لاین', 'tagline')}
          {T('زیرعنوان کوتاه', 'subtitle')}
          <Field label="توضیح کوتاه (هدر)" full><Textarea rows={2} value={s.description} onChange={(e) => set({ description: e.target.value })} /></Field>
          <h4 className="ad-sub">سئو (SEO)</h4>
          <Field label="عنوان صفحه (title)" full><Input value={s.seo?.title || ''} onChange={(e) => setIn('seo', { title: e.target.value })} /></Field>
          <Field label="توضیح متا" full><Textarea rows={2} value={s.seo?.description || ''} onChange={(e) => setIn('seo', { description: e.target.value })} /></Field>
          <Field label="کلمات کلیدی"><Input value={s.seo?.keywords || ''} onChange={(e) => setIn('seo', { keywords: e.target.value })} /></Field>
          <Field label="آدرس سایت"><Input dir="ltr" value={s.seo?.url || ''} onChange={(e) => setIn('seo', { url: e.target.value })} /></Field>
          <Field label="تصویر اشتراک‌گذاری (OG Image)" full><FileField value={s.ogImage} onChange={(v) => set({ ogImage: v })} folder="site" /></Field>
        </div>
      )}

      {tab === 'hero' && (
        <div className="ad-grid">
          {T('پیشوند تایپ‌رایتر', 'heroPrefix', { hint: 'مثلاً: I am a' })}
          <Field label="نقش‌ها (تایپ می‌شوند)" full><StringList value={s.roles} onChange={(v) => set({ roles: v })} placeholder="Web Developer" /></Field>
          <Field label="تصویر پرتره هدر" full><FileField value={s.heroImage} onChange={(v) => set({ heroImage: v })} folder="site" /></Field>
          <h4 className="ad-sub">دکمه‌ها</h4>
          <Field label="متن دکمه اصلی"><Input value={s.ctaPrimary?.label || ''} onChange={(e) => setIn('ctaPrimary', { label: e.target.value })} /></Field>
          <Field label="لینک دکمه اصلی"><Input dir="ltr" value={s.ctaPrimary?.href || ''} onChange={(e) => setIn('ctaPrimary', { href: e.target.value })} /></Field>
          <Field label="متن دکمه دوم (رزومه)"><Input value={s.ctaSecondary?.label || ''} onChange={(e) => setIn('ctaSecondary', { label: e.target.value })} /></Field>
          <Field label="فایل / لینک رزومه"><FileField value={s.ctaSecondary?.href || ''} onChange={(v) => setIn('ctaSecondary', { href: v })} accept="*/*" folder="files" preview={false} /></Field>
          <Field><Toggle checked={!!s.ctaSecondary?.show} onChange={(v) => setIn('ctaSecondary', { show: v })} label="نمایش دکمه دوم" /></Field>
          <h4 className="ad-sub">چیپ‌های اطلاعات زیر هدر</h4>
          <div className="full">
            <ObjectList value={s.badges} onChange={(v) => set({ badges: v })} blank={() => ({ emoji: '✨', label: '', value: '' })} title={(b) => `${b.emoji} ${b.label}`}
              render={(b, up) => (<>
                <Field label="ایموجی"><Input value={b.emoji} onChange={(e) => up({ emoji: e.target.value })} /></Field>
                <Field label="برچسب"><Input value={b.label} onChange={(e) => up({ label: e.target.value })} /></Field>
                <Field label="مقدار"><Input value={b.value} onChange={(e) => up({ value: e.target.value })} /></Field>
              </>)} />
          </div>
          <h4 className="ad-sub">بنر بالای سایت</h4>
          <Field><Toggle checked={!!s.showBanner} onChange={(v) => set({ showBanner: v })} label="نمایش بنر" /></Field>
          <Field label="تصویر بنر" full><FileField value={s.bannerImage} onChange={(v) => set({ bannerImage: v })} folder="site" /></Field>
        </div>
      )}

      {tab === 'about' && (
        <div className="ad-grid">
          <Field label="تصویر بخش درباره" full><FileField value={s.aboutImage} onChange={(v) => set({ aboutImage: v })} folder="site" /></Field>
          <Field label="کلمه کوچک بالای عنوان"><Input value={s.about.kicker} onChange={(e) => setIn('about', { kicker: e.target.value })} /></Field>
          <Field label="خط نقش"><Input value={s.about.roleLine} onChange={(e) => setIn('about', { roleLine: e.target.value })} /></Field>
          <Field label="پاراگراف اول" full hint="با **متن** می‌توانید بخشی را پررنگ کنید"><Textarea rows={3} value={s.about.p1} onChange={(e) => setIn('about', { p1: e.target.value })} /></Field>
          <Field label="پاراگراف دوم" full><Textarea rows={3} value={s.about.p2} onChange={(e) => setIn('about', { p2: e.target.value })} /></Field>
          <Field label="مهارت اصلی (بج روی عکس)"><Input value={s.about.topSkill} onChange={(e) => setIn('about', { topSkill: e.target.value })} /></Field>
          <Field label="علاقه (بج)"><Input value={s.about.passion} onChange={(e) => setIn('about', { passion: e.target.value })} /></Field>
          <Field label="کشور (بج)"><Input value={s.about.country} onChange={(e) => setIn('about', { country: e.target.value })} /></Field>
          <Field><Toggle checked={!!s.about.openToWork} onChange={(v) => setIn('about', { openToWork: v })} label="نشان «آماده همکاری»" /></Field>
          <Field label="تگ‌های تکنولوژی" full><StringList value={s.about.tech} onChange={(v) => setIn('about', { tech: v })} /></Field>
          <h4 className="ad-sub">آمارها</h4>
          <div className="full"><ObjectList value={s.about.stats} onChange={(v) => setIn('about', { stats: v })} blank={() => ({ value: '', label: '' })} title={(x) => `${x.value} ${x.label}`}
            render={(x, up) => (<><Field label="عدد"><Input value={x.value} onChange={(e) => up({ value: e.target.value })} /></Field><Field label="برچسب"><Input value={x.label} onChange={(e) => up({ label: e.target.value })} /></Field></>)} /></div>
          <h4 className="ad-sub">کارت‌های اطلاعات (تحصیلات، سخت‌افزار…)</h4>
          <div className="full"><ObjectList value={s.about.cards} onChange={(v) => setIn('about', { cards: v })} blank={() => ({ title: '', text: '' })} title={(x) => x.title}
            render={(x, up) => (<><Field label="عنوان"><Input value={x.title} onChange={(e) => up({ title: e.target.value })} /></Field><Field label="متن"><Input value={x.text} onChange={(e) => up({ text: e.target.value })} /></Field></>)} /></div>
          <h4 className="ad-sub">نوار لوگوها (Powering My Workflow)</h4>
          <div className="full"><ObjectList value={s.stack} onChange={(v) => set({ stack: v })} blank={() => ({ name: '', icon: '⚡' })} title={(x) => `${x.icon} ${x.name}`}
            render={(x, up) => (<><Field label="نام"><Input value={x.name} onChange={(e) => up({ name: e.target.value })} /></Field><Field label="آیکون (ایموجی یا آدرس تصویر)"><Input value={x.icon} onChange={(e) => up({ icon: e.target.value })} /></Field></>)} /></div>
        </div>
      )}

      {tab === 'socials' && (
        <div className="ad-grid">
          {(['email', 'phone', 'whatsapp', 'location', 'linkedin', 'instagram', 'facebook', 'youtube', 'github'] as const).map((k) => (
            <Field key={k} label={{ email: 'ایمیل', phone: 'تلفن', whatsapp: 'واتس‌اپ (با کد کشور، مثل 98912…)', location: 'موقعیت', github: 'GitHub (اختیاری — خالی بماند تا نمایش داده نشود)', linkedin: 'LinkedIn', instagram: 'Instagram', facebook: 'Facebook', youtube: 'YouTube' }[k]}>
              <Input dir="ltr" value={(s.socials as any)[k] || ''} onChange={(e) => setIn('socials', { [k]: e.target.value } as any)} />
            </Field>
          ))}
          <h4 className="ad-sub">بخش تماس</h4>
          <Field label="وضعیت (Availability)"><Input value={s.contact.availability} onChange={(e) => setIn('contact', { availability: e.target.value })} /></Field>
          <Field label="تعداد پروژه‌های انجام‌شده"><Input value={s.contact.projectsDone} onChange={(e) => setIn('contact', { projectsDone: e.target.value })} /></Field>
          <h4 className="ad-sub">فید شبکه اجتماعی</h4>
          <Field label="نام شبکه (Instagram / Facebook …)"><Input value={s.feed.network} onChange={(e) => setIn('feed', { network: e.target.value })} /></Field>
          <Field label="لینک پروفایل"><Input dir="ltr" value={s.feed.profileUrl} onChange={(e) => setIn('feed', { profileUrl: e.target.value })} /></Field>
          <Field label="عکس پروفایل" full><FileField value={s.feed.profilePic} onChange={(v) => setIn('feed', { profilePic: v })} folder="site" /></Field>
          <h4 className="ad-sub">فوتر</h4>
          <Field label="جمله فوتر" full><Textarea rows={2} value={s.footer.quote} onChange={(e) => setIn('footer', { quote: e.target.value })} /></Field>
          <Field label="متن کپی‌رایت (قبل از ❤)"><Input value={s.footer.text} onChange={(e) => setIn('footer', { text: e.target.value })} /></Field>
        </div>
      )}

      {tab === 'sections' && (
        <div className="ad-grid">
          <h4 className="ad-sub">روشن/خاموش کردن بخش‌ها</h4>
          <div className="full ad-toggle-grid">
            {Object.keys({ ...SECTION_LABELS, ...s.sections }).map((k) => (
              <Toggle key={k} checked={s.sections[k] !== false} onChange={(v) => setIn('sections', { [k]: v })} label={SECTION_LABELS[k] || k} />
            ))}
          </div>
          <h4 className="ad-sub">عنوان و زیرعنوان بخش‌ها</h4>
          {Object.keys({ ...TITLE_LABELS, ...s.sectionTitles }).map((k) => (
            <React.Fragment key={k}>
              <Field label={`${TITLE_LABELS[k] || k} — عنوان`}><Input value={s.sectionTitles[k]?.title || ''} onChange={(e) => setIn('sectionTitles', { [k]: { ...(s.sectionTitles[k] || { subtitle: '' }), title: e.target.value } })} /></Field>
              <Field label="زیرعنوان" hint={k === 'feed' ? '{name} با نام شما جایگزین می‌شود' : undefined}><Input value={s.sectionTitles[k]?.subtitle || ''} onChange={(e) => setIn('sectionTitles', { [k]: { ...(s.sectionTitles[k] || { title: '' }), subtitle: e.target.value } })} /></Field>
            </React.Fragment>
          ))}
          <h4 className="ad-sub">آمار بخش نظرات</h4>
          <Field label="رضایت (%)"><Input value={s.feedbackStats.satisfaction} onChange={(e) => setIn('feedbackStats', { satisfaction: e.target.value })} /></Field>
          <Field label="تعداد نظرات"><Input value={s.feedbackStats.reviews} onChange={(e) => setIn('feedbackStats', { reviews: e.target.value })} /></Field>
          <Field label="امتیاز"><Input value={s.feedbackStats.rating} onChange={(e) => setIn('feedbackStats', { rating: e.target.value })} /></Field>
          <Field label="تأییدشده"><Input value={s.feedbackStats.verified} onChange={(e) => setIn('feedbackStats', { verified: e.target.value })} /></Field>
          <Field label="متن پایین" full><Input value={s.feedbackStats.footer} onChange={(e) => setIn('feedbackStats', { footer: e.target.value })} /></Field>
        </div>
      )}

      {tab === 'music' && (
        <div className="ad-grid">
          <Field><Toggle checked={!!s.music.autoplay} onChange={(v) => setIn('music', { autoplay: v })} label="پخش خودکار بعد از اولین کلیک" /></Field>
          <h4 className="ad-sub">پوشه‌ها</h4>
          <div className="full"><ObjectList value={s.music.folders} onChange={(v) => setIn('music', { folders: v })} blank={() => ({ key: '', label: '', emoji: '🎵' })} title={(f) => `${f.emoji} ${f.label}`}
            render={(f, up) => (<>
              <Field label="کلید (انگلیسی)"><Input dir="ltr" value={f.key} onChange={(e) => up({ key: e.target.value })} /></Field>
              <Field label="نام نمایشی"><Input value={f.label} onChange={(e) => up({ label: e.target.value })} /></Field>
              <Field label="ایموجی"><Input value={f.emoji} onChange={(e) => up({ emoji: e.target.value })} /></Field>
            </>)} /></div>
          <h4 className="ad-sub">آهنگ‌ها</h4>
          <div className="full"><ObjectList value={s.music.tracks} onChange={(v) => setIn('music', { tracks: v })} blank={() => ({ id: 't' + Date.now(), title: '', artist: '', url: '', cover: '', category: s.music.folders[0]?.key || 'songs' })} title={(t) => `${t.title} — ${t.artist}`}
            render={(t, up) => (<>
              <Field label="عنوان"><Input value={t.title} onChange={(e) => up({ title: e.target.value })} /></Field>
              <Field label="خواننده"><Input value={t.artist} onChange={(e) => up({ artist: e.target.value })} /></Field>
              <Field label="لینک یوتیوب یا فایل MP3" full><FileField value={t.url} onChange={(v) => up({ url: v })} accept="audio/*" folder="music" preview={false} /></Field>
              <Field label="کاور"><FileField value={t.cover} onChange={(v) => up({ cover: v })} folder="music" /></Field>
              <Field label="پوشه"><select className="ad-input" value={t.category} onChange={(e) => up({ category: e.target.value })}>{s.music.folders.map((f) => <option key={f.key} value={f.key}>{f.label}</option>)}</select></Field>
            </>)} /></div>
        </div>
      )}

      {tab === 'misc' && (
        <div className="ad-grid">
          <Field><Toggle checked={!!s.welcome.enabled} onChange={(v) => setIn('welcome', { enabled: v })} label="پیام خوش‌آمدگویی" /></Field>
          <Field label="متن خوش‌آمد" full><Input value={s.welcome.text} onChange={(e) => setIn('welcome', { text: e.target.value })} /></Field>
          <Field label="رمز عمومی گاوصندوق (اختیاری)" hint="در صفحه Vault به‌عنوان راهنما استفاده می‌شود؛ کدهای اصلی را در بخش گاوصندوق تعریف کنید"><Input dir="ltr" value={s.vaultPassword || ''} onChange={(e) => set({ vaultPassword: e.target.value })} /></Field>
        </div>
      )}
    </Card>
  );
};
