import { CollectionSchema } from './CollectionEditor';

const today = () => new Date().toISOString().slice(0, 10);

export const SCHEMAS: Record<string, CollectionSchema> = {
  projects: {
    name: 'projects', title: 'پروژه‌ها', desc: 'پروژه‌ای که «شاخص» باشد به‌صورت کارت بزرگ با ویدیو بالای بقیه نمایش داده می‌شود.',
    itemTitle: (p) => p.title, itemSub: (p) => `${p.meta || ''}${p.featured ? ' · ⭐ شاخص' : ''}`, thumb: (p) => p.image,
    blank: () => ({ id: '', title: '', meta: '', action: 'View Project', image: '', url: '', actionType: 'link', featured: false, description: '', videoUrl: '', code: '', codeFilename: '' }),
    fields: [
      { key: 'title', label: 'عنوان (انگلیسی)' },
      { key: 'meta', label: 'زیرعنوان / دسته', placeholder: 'React & PHP' },
      { key: 'image', label: 'تصویر کارت', type: 'file', folder: 'projects' },
      { key: 'featured', label: 'پروژه شاخص (Flagship)', type: 'toggle', hint: 'فقط یکی را شاخص کنید' },
      { key: 'description', label: 'توضیح (برای کارت شاخص)', type: 'textarea', showIf: (p) => !!p.featured },
      { key: 'videoUrl', label: 'لینک ویدیوی یوتیوب (کارت شاخص)', type: 'url', full: true, showIf: (p) => !!p.featured },
      { key: 'actionType', label: 'نوع دکمه', type: 'select', options: [{ value: 'link', label: 'باز کردن لینک' }, { value: 'video', label: 'پخش ویدیو' }, { value: 'code', label: 'نمایش کد' }] },
      { key: 'action', label: 'متن دکمه', placeholder: 'Explore Now' },
      { key: 'url', label: 'لینک', type: 'url', full: true, showIf: (p) => p.actionType !== 'code' },
      { key: 'codeFilename', label: 'نام فایل کد', placeholder: 'tracker.py', showIf: (p) => p.actionType === 'code' },
      { key: 'code', label: 'کد', type: 'html', showIf: (p) => p.actionType === 'code' },
    ],
  },
  resources: {
    name: 'resources', title: 'منابع دانلودی (Study Resources)',
    itemTitle: (r) => r.title, itemSub: (r) => `${r.size || ''} · ${r.date || ''}`,
    blank: () => ({ id: '', title: '', size: '', date: today(), url: '' }),
    fields: [
      { key: 'title', label: 'عنوان', full: true },
      { key: 'url', label: 'فایل', type: 'file', accept: '*/*', folder: 'files', hint: 'فایل را آپلود کنید یا لینک مستقیم بگذارید' },
      { key: 'size', label: 'حجم (نمایشی)', placeholder: '2.4 MB' },
      { key: 'date', label: 'تاریخ', placeholder: '2026-01-01' },
    ],
  },
  feed: {
    name: 'feed', title: 'پست‌های شبکه اجتماعی (Feed)', desc: 'نام شبکه و لینک پروفایل در «تنظیمات › شبکه‌های اجتماعی» تعیین می‌شود.',
    itemTitle: (f) => (f.text || '').slice(0, 60), itemSub: (f) => f.date, thumb: (f) => f.image,
    blank: () => ({ id: '', image: '', date: today(), text: '', likes: 0, comments: 0, url: '' }),
    fields: [
      { key: 'image', label: 'تصویر پست', type: 'file', folder: 'feed' },
      { key: 'text', label: 'متن پست', type: 'textarea' },
      { key: 'date', label: 'تاریخ (نمایشی)', placeholder: 'Jan 12, 2026' },
      { key: 'url', label: 'لینک پست', type: 'url' },
      { key: 'likes', label: 'لایک', type: 'number' },
      { key: 'comments', label: 'کامنت', type: 'number' },
    ],
  },
  timeline: {
    name: 'timeline', title: 'مسیر و دستاوردها (Timeline)',
    itemTitle: (t) => t.title, itemSub: (t) => `${t.year} · ${t.tag || ''}`,
    blank: () => ({ id: '', year: String(new Date().getFullYear()), title: '', tag: '', text: '' }),
    fields: [
      { key: 'year', label: 'سال' },
      { key: 'tag', label: 'برچسب', placeholder: 'Education / Career / Award' },
      { key: 'title', label: 'عنوان', full: true },
      { key: 'text', label: 'توضیح', type: 'textarea' },
    ],
  },
  testimonials: {
    name: 'testimonials', title: 'نظرات (Testimonials)', desc: 'نظراتی که بازدیدکنندگان از طریق سایت ثبت می‌کنند اینجا با وضعیت «پیش‌نویس» می‌آیند؛ با آیکون چشم تأیید کنید.',
    publishKey: 'approved',
    itemTitle: (t) => `${t.name} ${'★'.repeat(Math.max(0, Math.min(5, Number(t.rating) || 0)))}`, itemSub: (t) => `${t.role || ''} — ${(t.text || '').slice(0, 80)}`, thumb: (t) => t.avatar,
    blank: () => ({ id: '', name: '', role: '', text: '', avatar: '', rating: 5, verified: true, createdAt: new Date().toISOString(), approved: true }),
    fields: [
      { key: 'name', label: 'نام' },
      { key: 'role', label: 'سمت / توضیح', placeholder: 'Client, Designer…' },
      { key: 'avatar', label: 'آواتار', type: 'file', folder: 'avatars' },
      { key: 'text', label: 'متن نظر', type: 'textarea' },
      { key: 'rating', label: 'امتیاز (۱ تا ۵)', type: 'number' },
      { key: 'verified', label: 'تیک تأیید', type: 'toggle' },
    ],
  },
  gallery: {
    name: 'gallery', title: 'گالری تصاویر',
    itemTitle: (g) => g.title, itemSub: (g) => g.category, thumb: (g) => g.src,
    blank: () => ({ id: '', src: '', title: '', caption: '', category: 'Moments' }),
    fields: [
      { key: 'src', label: 'تصویر', type: 'file', folder: 'gallery' },
      { key: 'title', label: 'عنوان' },
      { key: 'category', label: 'دسته', placeholder: 'Moments / Travel / Work' },
      { key: 'caption', label: 'توضیح', type: 'textarea' },
    ],
  },
  vault: {
    name: 'vault', title: 'گاوصندوق مخفی (Secret Vault)', desc: 'هر آیتم با یک «کد مخفی» باز می‌شود. بازدیدکننده کد را در صفحه Vault وارد می‌کند و فقط آیتم‌های همان کد را می‌بیند.',
    itemTitle: (v) => v.title, itemSub: (v) => `کد: ${v.secretCode} · ${v.type} · ${v.folderName || ''}`, thumb: (v) => v.thumbnail || (v.type === 'image' ? v.src : ''),
    blank: () => ({ id: '', secretCode: '', type: 'image', src: '', title: '', thumbnail: '', folderName: '' }),
    fields: [
      { key: 'secretCode', label: 'کد مخفی', hint: 'حروف کوچک/بزرگ مهم نیست' },
      { key: 'type', label: 'نوع', type: 'select', options: [{ value: 'image', label: 'تصویر' }, { value: 'video', label: 'ویدیو' }, { value: 'audio', label: 'صدا' }, { value: 'pdf', label: 'PDF' }, { value: 'link', label: 'لینک' }] },
      { key: 'title', label: 'عنوان' },
      { key: 'folderName', label: 'نام پوشه (نمایشی)' },
      { key: 'src', label: 'فایل / لینک', type: 'file', accept: '*/*', folder: 'vault' },
      { key: 'thumbnail', label: 'تصویر بندانگشتی', type: 'file', folder: 'vault' },
    ],
  },
  pages: {
    name: 'pages', title: 'صفحات ثابت', desc: 'آدرس صفحه: /p/اسلاگ — اسلاگ‌های privacy ، terms و support در فوتر لینک شده‌اند.',
    itemTitle: (p) => p.title, itemSub: (p) => `/${['privacy', 'terms', 'support'].includes(p.slug) ? '' : 'p/'}${p.slug}`,
    blank: () => ({ id: '', slug: '', title: '', body: '<h2>Title</h2><p>Text…</p>', updatedAt: today() }),
    fields: [
      { key: 'title', label: 'عنوان' },
      { key: 'slug', label: 'اسلاگ (انگلیسی، بدون فاصله)', placeholder: 'privacy' },
      { key: 'updatedAt', label: 'تاریخ به‌روزرسانی' },
      { key: 'body', label: 'متن (HTML)', type: 'html' },
    ],
  },
};

export const COLLECTION_ORDER = ['projects', 'resources', 'feed', 'timeline', 'testimonials', 'gallery', 'vault', 'pages'];
