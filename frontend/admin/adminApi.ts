import { API_BASE } from '../lib/api';

let csrf = '';
export const setCsrf = (t: string) => { csrf = t || ''; };

export async function adminApi<T = any>(route: string, body?: any, method?: string): Promise<T> {
  const init: RequestInit = { method: method || (body !== undefined ? 'POST' : 'GET'), credentials: 'same-origin', headers: { 'X-CSRF': csrf } };
  if (body !== undefined) { (init.headers as any)['Content-Type'] = 'application/json'; init.body = JSON.stringify(body); }
  const res = await fetch(`${API_BASE}?r=${encodeURIComponent(route)}`, init);
  let data: any = null;
  try { data = await res.json(); } catch { /* ignore */ }
  if (!res.ok || (data && data.ok === false)) throw new Error((data && data.error) || `HTTP ${res.status}`);
  if (data && data.csrf) csrf = data.csrf;
  return data as T;
}

export async function adminUpload(file: File, folder = 'images'): Promise<{ url: string; name: string }> {
  const fd = new FormData();
  fd.append('file', file);
  fd.append('folder', folder);
  const res = await fetch(`${API_BASE}?r=admin/upload`, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF': csrf }, body: fd });
  const data = await res.json().catch(() => null);
  if (!res.ok || !data || data.ok === false) throw new Error((data && data.error) || `HTTP ${res.status}`);
  return data;
}
