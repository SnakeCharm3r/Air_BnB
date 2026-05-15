import React, { createContext, useContext, useState, useEffect } from 'react';
import { api } from '../lib/api';

interface User {
  id: string;
  name: string;
  email: string;
  role: string;
}

interface AuthContextType {
  token: string | null;
  profile: User | null;
  login: (email: string, password: string) => Promise<void>;
  logout: () => void;
  setProfile: (profile: User | null) => void;
  refreshProfile: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [token, setToken] = useState<string | null>(
    localStorage.getItem('auth_token')
  );
  const [profile, setProfile] = useState<User | null>(null);

  useEffect(() => {
    if (token) {
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    } else {
      delete api.defaults.headers.common['Authorization'];
    }
  }, [token]);

  const login = async (email: string, password: string) => {
    try {
      const response = await api.post('/auth/login', { email, password });
      const { token: newToken, user } = response.data;
      
      setToken(newToken);
      setProfile(user);
      localStorage.setItem('auth_token', newToken);
      api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
    } catch (error) {
      throw error;
    }
  };

  const logout = () => {
    setToken(null);
    setProfile(null);
    localStorage.removeItem('auth_token');
    delete api.defaults.headers.common['Authorization'];
  };

  const refreshProfile = async () => {
    if (!token) return;
    try {
      const response = await api.get('/auth/me');
      setProfile(response.data);
    } catch (error) {
      console.error('Failed to refresh profile:', error);
    }
  };

  return (
    <AuthContext.Provider value={{ token, profile, login, logout, setProfile, refreshProfile }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}
