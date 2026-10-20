import React, { createContext, useContext, useEffect, useState, useCallback, ReactNode } from 'react';
import { api } from '../lib/api';

export interface SiteUser { id: string; email: string; displayName: string; photoURL: string; bio?: string; createdAt?: string; }

interface AuthContextType {
  user: SiteUser | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<{ error: any }>;
  signup: (email: string, password: string, name?: string) => Promise<{ error: any }>;
  logout: () => Promise<{ error: any }>;
  updateProfile: (data: Partial<SiteUser> & { password?: string }) => Promise<{ error: any }>;
  refreshUser: () => Promise<{ error: any }>;
}

const AuthContext = createContext<AuthContextType>({} as AuthContextType);

/** Visitor accounts backed by /api (PHP sessions + data/users.json). */
export const AuthProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<SiteUser | null>(null);
  const [loading, setLoading] = useState(true);

  const refreshUser = useCallback(async () => {
    try { const r = await api<{ user: SiteUser | null }>('me'); setUser(r.user); return { error: null }; }
    catch (error) { setUser(null); return { error }; }
    finally { setLoading(false); }
  }, []);

  useEffect(() => { refreshUser(); }, [refreshUser]);

  const wrap = (fn: () => Promise<any>) => async () => {
    try { const r = await fn(); if (r && r.user !== undefined) setUser(r.user); return { error: null }; }
    catch (error) { return { error }; }
  };

  const login = useCallback((email: string, password: string) => wrap(() => api('login', { email, password }))(), []);
  const signup = useCallback((email: string, password: string, name?: string) => wrap(() => api('signup', { email, password, name }))(), []);
  const logout = useCallback(() => wrap(async () => { await api('logout', {}); return { user: null }; })(), []);
  const updateProfile = useCallback((data: any) => wrap(() => api('profile', data))(), []);

  return (
    <AuthContext.Provider value={{ user, loading, login, signup, logout, updateProfile, refreshUser }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
