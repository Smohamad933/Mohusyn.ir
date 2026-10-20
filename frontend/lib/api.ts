/** Tiny API client. All calls go to /api/index.php?r=<route> (no URL-rewrite needed on IIS). */
export const API_BASE = '/api/index.php';

export async function api<T = any>(route: string, body?: any, opts: { method?: string; form?: FormData } = {}): Promise<T> {
  const method = opts.method || (body || opts.form ? 'POST' : 'GET');
  const init: RequestInit = { method, credentials: 'same-origin', headers: {} };
  if (opts.form) init.body = opts.form;
  else if (body !== undefined) {
    (init.headers as any)['Content-Type'] = 'application/json';
    init.body = JSON.stringify(body);
  }
  const res = await fetch(`${API_BASE}?r=${encodeURIComponent(route)}`, init);
  let data: any = null;
  try { data = await res.json(); } catch { /* non-json */ }
  if (!res.ok || (data && data.ok === false)) {
    const msg = (data && (data.error || data.message)) || `HTTP ${res.status}`;
    throw new Error(msg);
  }
  return data as T;
}
