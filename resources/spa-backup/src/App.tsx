import { Routes, Route, Navigate } from 'react-router-dom';
import { useState, useEffect } from 'react';
import Layout from './components/Layout';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import BookingsPage from './pages/BookingsPage';
import RoomsPage from './pages/RoomsPage';
import StaffPage from './pages/StaffPage';
import BillingPage from './pages/BillingPage';
import InventoryPage from './pages/InventoryPage';
import ReportsPage from './pages/ReportsPage';
import TasksPage from './pages/TasksPage';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import { api } from './lib/api';

function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const { token, profile } = useAuth();
  
  if (!token) {
    return <Navigate to="/login" replace />;
  }
  
  return <>{children}</>;
}

function AppRoutes() {
  const { token, setProfile } = useAuth();
  
  useEffect(() => {
    if (token) {
      api.get('/user').then(response => {
        setProfile(response.data);
      }).catch(() => {
        // Token invalid, will be handled by AuthContext
      });
    }
  }, [token, setProfile]);

  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route path="/" element={
        <ProtectedRoute>
          <Layout />
        </ProtectedRoute>
      }>
        <Route index element={<Navigate to="/dashboard" replace />} />
        <Route path="dashboard" element={<Dashboard />} />
        <Route path="bookings" element={<BookingsPage />} />
        <Route path="rooms" element={<RoomsPage />} />
        <Route path="staff" element={<StaffPage />} />
        <Route path="billing" element={<BillingPage />} />
        <Route path="inventory" element={<InventoryPage />} />
        <Route path="reports" element={<ReportsPage />} />
        <Route path="tasks" element={<TasksPage />} />
      </Route>
    </Routes>
  );
}

export default function App() {
  return (
    <AuthProvider>
      <AppRoutes />
    </AuthProvider>
  );
}
