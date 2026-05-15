import { Outlet, Link, useLocation } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { 
  Home, 
  Calendar, 
  DoorOpen, 
  Users, 
  DollarSign, 
  Package, 
  FileText, 
  CheckSquare, 
  LogOut,
  Menu,
  X
} from 'lucide-react';
import { useState } from 'react';

const navigation = [
  { name: 'Dashboard', href: '/dashboard', icon: Home },
  { name: 'Bookings', href: '/bookings', icon: Calendar },
  { name: 'Rooms', href: '/rooms', icon: DoorOpen },
  { name: 'Staff', href: '/staff', icon: Users },
  { name: 'Billing', href: '/billing', icon: DollarSign },
  { name: 'Inventory', href: '/inventory', icon: Package },
  { name: 'Reports', href: '/reports', icon: FileText },
  { name: 'Tasks', href: '/tasks', icon: CheckSquare },
];

export default function Layout() {
  const { profile, logout } = useAuth();
  const location = useLocation();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Mobile sidebar */}
      <div className={`fixed inset-0 z-50 lg:hidden ${sidebarOpen ? '' : 'pointer-events-none'}`}>
        <div className={`absolute inset-0 bg-gray-600 transition-opacity ${sidebarOpen ? 'opacity-75' : 'opacity-0'}`}
             onClick={() => setSidebarOpen(false)} />
        <div className={`relative flex w-64 flex-1 flex-col bg-white transform transition-transform ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'}`}>
          <div className="flex items-center justify-between p-4 border-b">
            <h1 className="text-xl font-bold text-gray-900">Lodge POS</h1>
            <button 
              onClick={() => setSidebarOpen(false)} 
              className="p-1 rounded-lg hover:bg-gray-100"
              aria-label="Close navigation menu"
              title="Close navigation menu"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
          <nav className="flex-1 p-4 space-y-2">
            {navigation.map((item) => {
              const Icon = item.icon;
              const isActive = location.pathname === item.href;
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className={`flex items-center gap-3 px-3 py-2 rounded-lg transition-colors ${
                    isActive 
                      ? 'bg-blue-50 text-blue-700' 
                      : 'text-gray-700 hover:bg-gray-100'
                  }`}
                >
                  <Icon className="w-5 h-5" />
                  {item.name}
                </Link>
              );
            })}
          </nav>
          <div className="p-4 border-t">
            <div className="flex items-center justify-between mb-3">
              <div>
                <p className="text-sm font-medium text-gray-900">{profile?.name}</p>
                <p className="text-xs text-gray-500 capitalize">{profile?.role}</p>
              </div>
            </div>
            <button
              onClick={logout}
              className="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            >
              <LogOut className="w-4 h-4" />
              Sign out
            </button>
          </div>
        </div>
      </div>

      {/* Desktop sidebar */}
      <div className="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
        <div className="flex flex-col flex-grow bg-white border-r border-gray-200">
          <div className="flex items-center p-4 border-b">
            <h1 className="text-xl font-bold text-gray-900">Lodge POS</h1>
          </div>
          <nav className="flex-1 p-4 space-y-2">
            {navigation.map((item) => {
              const Icon = item.icon;
              const isActive = location.pathname === item.href;
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className={`flex items-center gap-3 px-3 py-2 rounded-lg transition-colors ${
                    isActive 
                      ? 'bg-blue-50 text-blue-700' 
                      : 'text-gray-700 hover:bg-gray-100'
                  }`}
                >
                  <Icon className="w-5 h-5" />
                  {item.name}
                </Link>
              );
            })}
          </nav>
          <div className="p-4 border-t">
            <div className="flex items-center justify-between mb-3">
              <div>
                <p className="text-sm font-medium text-gray-900">{profile?.name}</p>
                <p className="text-xs text-gray-500 capitalize">{profile?.role}</p>
              </div>
            </div>
            <button
              onClick={logout}
              className="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            >
              <LogOut className="w-4 h-4" />
              Sign out
            </button>
          </div>
        </div>
      </div>

      {/* Main content */}
      <div className="lg:pl-64">
        {/* Top bar */}
        <div className="sticky top-0 z-10 flex h-16 bg-white border-b border-gray-200 lg:hidden">
          <button 
            onClick={() => setSidebarOpen(true)} 
            className="p-4 text-gray-500 hover:text-gray-600"
            aria-label="Open navigation menu"
            title="Open navigation menu"
          >
            <Menu className="w-6 h-6" />
          </button>
          <h1 className="flex-1 flex items-center justify-center text-lg font-semibold text-gray-900">
            Lodge POS
          </h1>
        </div>

        {/* Page content */}
        <main className="p-4 lg:p-8">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
