import { useState } from 'react';
import { FileText, Download, Calendar, TrendingUp, DollarSign, Users } from 'lucide-react';

export default function ReportsPage() {
  const [selectedReport, setSelectedReport] = useState('occupancy');

  const reports = [
    {
      id: 'occupancy',
      name: 'Occupancy Report',
      description: 'Room occupancy rates and trends',
      icon: Users,
    },
    {
      id: 'revenue',
      name: 'Revenue Report',
      description: 'Income and financial performance',
      icon: DollarSign,
    },
    {
      id: 'booking',
      name: 'Booking Report',
      description: 'Booking statistics and patterns',
      icon: Calendar,
    },
    {
      id: 'staff',
      name: 'Staff Performance',
      description: 'Staff productivity and attendance',
      icon: TrendingUp,
    },
  ];

  return (
    <div>
      <div className="mb-6">
        <h1 className="text-2xl font-bold text-gray-900">Reports</h1>
        <p className="text-gray-600">Generate and view lodge reports</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div className="lg:col-span-1">
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">Report Types</h2>
            <div className="space-y-2">
              {reports.map((report) => {
                const Icon = report.icon;
                return (
                  <button
                    key={report.id}
                    onClick={() => setSelectedReport(report.id)}
                    className={`w-full text-left p-3 rounded-lg transition-colors ${
                      selectedReport === report.id
                        ? 'bg-blue-50 border-blue-200 text-blue-700'
                        : 'hover:bg-gray-50 text-gray-700'
                    }`}
                  >
                    <div className="flex items-center gap-3">
                      <Icon className="w-5 h-5" />
                      <div>
                        <div className="font-medium">{report.name}</div>
                        <div className="text-sm opacity-75">{report.description}</div>
                      </div>
                    </div>
                  </button>
                );
              })}
            </div>
          </div>
        </div>

        <div className="lg:col-span-3">
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-lg font-semibold text-gray-900">
                {reports.find(r => r.id === selectedReport)?.name}
              </h2>
              <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <Download className="w-4 h-4" />
                Export
              </button>
            </div>

            <div className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="bg-gray-50 rounded-lg p-4">
                  <h3 className="text-sm font-medium text-gray-500 mb-1">Total Bookings</h3>
                  <p className="text-2xl font-bold text-gray-900">156</p>
                  <p className="text-sm text-green-600">↑ 12% from last month</p>
                </div>
                <div className="bg-gray-50 rounded-lg p-4">
                  <h3 className="text-sm font-medium text-gray-500 mb-1">Occupancy Rate</h3>
                  <p className="text-2xl font-bold text-gray-900">78%</p>
                  <p className="text-sm text-green-600">↑ 5% from last month</p>
                </div>
                <div className="bg-gray-50 rounded-lg p-4">
                  <h3 className="text-sm font-medium text-gray-500 mb-1">Revenue</h3>
                  <p className="text-2xl font-bold text-gray-900">$24,580</p>
                  <p className="text-sm text-green-600">↑ 18% from last month</p>
                </div>
              </div>

              <div className="border-t pt-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Monthly Overview</h3>
                <div className="space-y-3">
                  <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span className="text-sm font-medium text-gray-700">January 2024</span>
                    <div className="flex items-center gap-4">
                      <span className="text-sm text-gray-600">142 bookings</span>
                      <span className="text-sm font-medium text-gray-900">$22,450</span>
                    </div>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span className="text-sm font-medium text-gray-700">February 2024</span>
                    <div className="flex items-center gap-4">
                      <span className="text-sm text-gray-600">156 bookings</span>
                      <span className="text-sm font-medium text-gray-900">$24,580</span>
                    </div>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span className="text-sm font-medium text-gray-700">March 2024</span>
                    <div className="flex items-center gap-4">
                      <span className="text-sm text-gray-600">138 bookings</span>
                      <span className="text-sm font-medium text-gray-900">$21,320</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
