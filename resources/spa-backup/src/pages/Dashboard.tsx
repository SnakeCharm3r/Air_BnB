import { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { dashboardApi } from '../lib/api';
import { CalendarDays, Users, DoorOpen, DollarSign, TrendingUp } from 'lucide-react';

interface DashboardStats {
  totalBookings: number;
  activeBookings: number;
  totalRooms: number;
  occupiedRooms: number;
  totalRevenue: number;
  monthlyRevenue: number;
  totalStaff: number;
  activeStaff: number;
}

export default function Dashboard() {
  const { profile } = useAuth();
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await dashboardApi.stats();
        setStats(response.data);
      } catch (error) {
        console.error('Failed to fetch dashboard stats:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  const statCards = [
    {
      title: 'Total Bookings',
      value: stats?.totalBookings || 0,
      change: stats?.activeBookings || 0,
      changeLabel: 'Active',
      icon: CalendarDays,
      color: 'blue',
    },
    {
      title: 'Room Occupancy',
      value: `${stats?.occupiedRooms || 0}/${stats?.totalRooms || 0}`,
      change: stats?.totalRooms ? Math.round((stats.occupiedRooms / stats.totalRooms) * 100) : 0,
      changeLabel: '% Occupied',
      icon: DoorOpen,
      color: 'green',
    },
    {
      title: 'Total Revenue',
      value: `$${(stats?.totalRevenue || 0).toLocaleString()}`,
      change: stats?.monthlyRevenue || 0,
      changeLabel: 'This Month',
      icon: DollarSign,
      color: 'yellow',
    },
    {
      title: 'Staff Members',
      value: stats?.totalStaff || 0,
      change: stats?.activeStaff || 0,
      changeLabel: 'Active',
      icon: Users,
      color: 'purple',
    },
  ];

  return (
    <div>
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p className="text-gray-600">Welcome back, {profile?.name}</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {statCards.map((stat) => {
          const Icon = stat.icon;
          return (
            <div key={stat.title} className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div className="flex items-center justify-between mb-4">
                <div className={`p-3 rounded-lg bg-${stat.color}-100`}>
                  <Icon className={`w-6 h-6 text-${stat.color}-600`} />
                </div>
                <div className="text-right">
                  <p className="text-sm text-gray-600">{stat.changeLabel}</p>
                  <p className="text-lg font-semibold text-gray-900">{stat.change}</p>
                </div>
              </div>
              <h3 className="text-lg font-semibold text-gray-900">{stat.value}</h3>
              <p className="text-sm text-gray-600">{stat.title}</p>
            </div>
          );
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
          <div className="space-y-3">
            <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
              <div className="w-2 h-2 bg-green-500 rounded-full"></div>
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-900">New booking created</p>
                <p className="text-xs text-gray-500">Room 101 - John Doe</p>
              </div>
              <span className="text-xs text-gray-500">2m ago</span>
            </div>
            <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
              <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-900">Guest checked in</p>
                <p className="text-xs text-gray-500">Room 205 - Jane Smith</p>
              </div>
              <span className="text-xs text-gray-500">15m ago</span>
            </div>
            <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
              <div className="w-2 h-2 bg-yellow-500 rounded-full"></div>
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-900">Payment received</p>
                <p className="text-xs text-gray-500">$250 - Room 103</p>
              </div>
              <span className="text-xs text-gray-500">1h ago</span>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
          <div className="grid grid-cols-2 gap-3">
            <button className="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-center transition-colors">
              <CalendarDays className="w-6 h-6 text-blue-600 mx-auto mb-2" />
              <span className="text-sm font-medium text-gray-900">New Booking</span>
            </button>
            <button className="p-4 bg-green-50 hover:bg-green-100 rounded-lg text-center transition-colors">
              <Users className="w-6 h-6 text-green-600 mx-auto mb-2" />
              <span className="text-sm font-medium text-gray-900">Check In</span>
            </button>
            <button className="p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg text-center transition-colors">
              <DoorOpen className="w-6 h-6 text-yellow-600 mx-auto mb-2" />
              <span className="text-sm font-medium text-gray-900">Check Out</span>
            </button>
            <button className="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg text-center transition-colors">
              <TrendingUp className="w-6 h-6 text-purple-600 mx-auto mb-2" />
              <span className="text-sm font-medium text-gray-900">Reports</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
