import { useState, useEffect, useCallback } from 'react';

/** localStorage-persisted state (replaces the old cloud/Redis state). Returns [state, setState, isReady]. */
export function useLocalState<T>(key: string, initial: T): [T, (v: T | ((p: T) => T)) => void, boolean] {
  const storageKey = `mh_state_${key}`;
  const [state, setState] = useState<T>(() => {
    try { const raw = localStorage.getItem(storageKey); return raw ? { ...initial, ...JSON.parse(raw) } : initial; } catch { return initial; }
  });
  useEffect(() => { try { localStorage.setItem(storageKey, JSON.stringify(state)); } catch { /* ignore */ } }, [state, storageKey]);
  const set = useCallback((v: T | ((p: T) => T)) => setState(prev => (typeof v === 'function' ? (v as any)(prev) : v)), []);
  return [state, set, true];
}
