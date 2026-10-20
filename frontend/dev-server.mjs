// Dev-only mock of site/api/index.php (Node). Serves ../site statically with SPA fallback
// and emulates the PHP JSON API against ../site/data so the built site can be previewed without PHP.
// Usage: node dev-server.mjs [port]
import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../site');
const DATA = path.join(ROOT, 'data');
const UP = path.join(ROOT, 'uploads');
const PORT = Number(process.argv[2] || 8090);
const ADMIN_PASSWORD = 'mohusyn2026';
const COLLECTIONS = ['projects', 'resources', 'feed', 'timeline', 'testimonials', 'gallery', 'vault', 'pages'];
const sessions = new Map();

const load = (n, d) => { try { return JSON.parse(fs.readFileSync(path.join(DATA, n + '.json'), 'utf8')); } catch { return d; } };
const save = (n, v) => fs.writeFileSync(path.join(DATA, n + '.json'), JSON.stringify(v, null, 2));
const id = (p = '') => p + crypto.randomBytes(6).toString('hex');
const MIME = { '.html': 'text/html', '.js': 'text/javascript', '.css': 'text/css', '.json': 'application/json', '.png': 'image/png', '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.svg': 'image/svg+xml', '.webp': 'image/webp', '.gif': 'image/gif', '.ico': 'image/x-icon', '.mp3': 'audio/mpeg', '.mp4': 'video/mp4', '.pdf': 'application/pdf', '.woff2': 'font/woff2', '.woff': 'font/woff' };

function content(forAdmin) {
  const out = { settings: load('settings', {}) };
  for (const c of COLLECTIONS) {
    let items = load(c, []);
    if (!forAdmin) {
      if (c === 'vault') continue;
      items = items.filter((it) => (c === 'testimonials' ? it.approved !== false : it.published !== false));
    }
    out[c] = items;
  }
  if (!forAdmin) delete out.settings.vaultPassword;
  return out;
}

function parseMultipart(buf, boundary) {
  const parts = {}; const files = {};
  const b = Buffer.from('--' + boundary);
  let pos = buf.indexOf(b) + b.length + 2;
  while (pos < buf.length) {
    const end = buf.indexOf(b, pos); if (end < 0) break;
    const part = buf.subarray(pos, end - 2);
    const hEnd = part.indexOf('\r\n\r\n');
    const head = part.subarray(0, hEnd).toString(); const body = part.subarray(hEnd + 4);
    const name = /name="([^"]+)"/.exec(head)?.[1]; const fn = /filename="([^"]*)"/.exec(head)?.[1];
    if (fn !== undefined) files[name] = { name: fn, data: body }; else parts[name] = body.toString();
    pos = end + b.length + 2;
  }
  return { parts, files };
}

const server = http.createServer(async (req, res) => {
  const url = new URL(req.url, 'http://x');
  const json = (o, s = 200) => { res.writeHead(s, { 'Content-Type': 'application/json; charset=utf-8', 'Cache-Control': 'no-store' }); res.end(JSON.stringify(o)); };
  const fail = (m, s = 400) => json({ ok: false, error: m }, s);
  const cookie = Object.fromEntries((req.headers.cookie || '').split(';').map((c) => c.trim().split('=')).filter((x) => x[0]));
  let sid = cookie.MHSESS; if (!sid || !sessions.has(sid)) { sid = id(); sessions.set(sid, {}); res.setHeader('Set-Cookie', `MHSESS=${sid}; Path=/; HttpOnly`); }
  const sess = sessions.get(sid);

  if (url.pathname === '/api/index.php') {
    const r = url.searchParams.get('r') || '';
    const chunks = []; for await (const c of req) chunks.push(c); const raw = Buffer.concat(chunks);
    let body = {}; try { body = JSON.parse(raw.toString() || '{}'); } catch { }
    const admin = () => { if (!sess.admin) { fail('Unauthorized', 401); return false; } return true; };
    try {
      switch (r) {
        case 'health': return json({ ok: true, php: 'node-mock', dataWritable: true });
        case 'content': return json({ ok: true, content: content(false), csrf: 'dev' });
        case 'track': return json({ ok: true });
        case 'contact': { if (!body.name || !body.message) return fail('Name and message are required.'); const m = load('messages', []); m.unshift({ id: id('m'), ...body, createdAt: new Date().toISOString(), read: false }); save('messages', m); return json({ ok: true }); }
        case 'feedback': { const t = load('testimonials', []); t.unshift({ id: id('c'), name: body.name, role: 'Visitor', text: body.text || '', avatar: '', rating: body.rating || 5, verified: false, createdAt: new Date().toISOString(), approved: false }); save('testimonials', t); return json({ ok: true, pending: true }); }
        case 'vault': { const code = String(body.code || '').toLowerCase(); return json({ ok: true, items: load('vault', []).filter((v) => String(v.secretCode).toLowerCase() === code && v.published !== false) }); }
        case 'signup': { const users = load('users', []); if (users.some((u) => u.email === body.email)) return fail('An account with this email already exists.'); const u = { id: id('u'), email: body.email, displayName: body.name || body.email.split('@')[0], photoURL: '', bio: '', pass: body.password, createdAt: new Date().toISOString() }; users.push(u); save('users', users); sess.uid = u.id; const { pass, ...pub } = u; return json({ ok: true, user: pub }); }
        case 'login': { const u = load('users', []).find((x) => x.email === body.email && x.pass === body.password); if (!u) return fail('Invalid email or password.', 401); sess.uid = u.id; const { pass, ...pub } = u; return json({ ok: true, user: pub }); }
        case 'logout': sess.uid = null; return json({ ok: true, user: null });
        case 'me': { const u = load('users', []).find((x) => x.id === sess.uid); if (!u) return json({ ok: true, user: null }); const { pass, ...pub } = u; return json({ ok: true, user: pub }); }
        case 'profile': { const users = load('users', []); const i = users.findIndex((x) => x.id === sess.uid); if (i < 0) return fail('Not signed in', 401); Object.assign(users[i], { displayName: body.displayName ?? users[i].displayName, photoURL: body.photoURL ?? users[i].photoURL, bio: body.bio ?? users[i].bio }); save('users', users); const { pass, ...pub } = users[i]; return json({ ok: true, user: pub }); }
        case 'admin/login': { if (body.password !== (load('_admin', {}).plain || ADMIN_PASSWORD)) return fail('رمز عبور اشتباه است.', 401); sess.admin = true; return json({ ok: true, csrf: 'dev' }); }
        case 'admin/logout': sess.admin = false; return json({ ok: true });
        case 'admin/me': return json({ ok: true, admin: !!sess.admin, csrf: 'dev' });
        case 'admin/password': if (!admin()) return; save('_admin', { plain: body.password }); return json({ ok: true });
        case 'admin/content': if (!admin()) return; return json({ ok: true, content: content(true), csrf: 'dev' });
        case 'admin/settings': if (!admin()) return; save('settings', body.settings); return json({ ok: true });
        case 'admin/collection': { if (!admin()) return; const items = (body.items || []).map((it) => ({ ...it, id: it.id || id() })); save(body.name, items); return json({ ok: true, items }); }
        case 'admin/item': { if (!admin()) return; let items = load(body.name, []); if (body.delete) items = items.filter((x) => x.id !== body.id); else { const it = { ...body.item, id: body.item.id || id() }; const i = items.findIndex((x) => x.id === it.id); if (i < 0) items.push(it); else items[i] = it; } save(body.name, items); return json({ ok: true, items }); }
        case 'admin/upload': { if (!admin()) return; const bd = /boundary=(.+)$/.exec(req.headers['content-type'] || '')?.[1]; const { parts, files } = parseMultipart(raw, bd); const f = files.file; if (!f) return fail('No file'); const sub = (parts.folder || '').replace(/[^a-z0-9_-]/gi, ''); const dir = path.join(UP, sub); fs.mkdirSync(dir, { recursive: true }); const ext = path.extname(f.name).toLowerCase(); const name = path.basename(f.name, ext).replace(/[^a-z0-9_-]/gi, '-').slice(0, 60) + '-' + Date.now() + ext; fs.writeFileSync(path.join(dir, name), f.data); return json({ ok: true, url: `/uploads/${sub ? sub + '/' : ''}${name}`, name, size: f.data.length }); }
        case 'admin/media': { if (!admin()) return; if (body.delete) { const p = path.join(ROOT, body.url); if (p.startsWith(UP) && fs.existsSync(p)) fs.unlinkSync(p); return json({ ok: true }); } const files = []; const walk = (d) => { for (const e of fs.readdirSync(d, { withFileTypes: true })) { const p = path.join(d, e.name); if (e.isDirectory()) walk(p); else if (!['.gitkeep', 'web.config'].includes(e.name)) { const st = fs.statSync(p); files.push({ url: '/uploads' + p.slice(UP.length).replace(/\\/g, '/'), name: e.name, size: st.size, mtime: st.mtimeMs / 1000, image: /\.(jpe?g|png|gif|webp|svg|ico)$/i.test(e.name) }); } } }; walk(UP); files.sort((a, b) => b.mtime - a.mtime); return json({ ok: true, files }); }
        case 'admin/messages': { if (!admin()) return; let m = load('messages', []); if (req.method === 'POST') { if (body.delete) m = m.filter((x) => x.id !== body.id); else if ('read' in body) m = m.map((x) => (x.id === body.id ? { ...x, read: !!body.read } : x)); save('messages', m); } return json({ ok: true, messages: m }); }
        case 'admin/users': { if (!admin()) return; let u = load('users', []); if (body.delete) { u = u.filter((x) => x.id !== body.id); save('users', u); } return json({ ok: true, users: u.map(({ pass, ...x }) => x) }); }
        case 'admin/stats': { if (!admin()) return; const m = load('messages', []); return json({ ok: true, stats: load('stats', {}), unread: m.filter((x) => !x.read).length, pendingTestimonials: load('testimonials', []).filter((t) => t.approved === false).length, users: load('users', []).length, php: 'node-mock (dev)', writable: true }); }
        default: return fail('Unknown route: ' + r, 404);
      }
    } catch (e) { return fail(String(e.message || e), 500); }
  }

  // static + SPA fallback
  let p = decodeURIComponent(url.pathname);
  if (p.startsWith('/data/')) { res.writeHead(403); return res.end('Forbidden'); }
  let fp = path.join(ROOT, p);
  if (!fp.startsWith(ROOT) || !fs.existsSync(fp) || fs.statSync(fp).isDirectory()) fp = path.join(ROOT, 'index.html');
  res.writeHead(200, { 'Content-Type': MIME[path.extname(fp).toLowerCase()] || 'application/octet-stream', 'Cache-Control': fp.endsWith('index.html') ? 'no-store' : 'public, max-age=3600' });
  fs.createReadStream(fp).pipe(res);
});
server.listen(PORT, '0.0.0.0', () => console.log(`preview on http://0.0.0.0:${PORT} (root ${ROOT})`));
