import { useState } from 'react';
import { DollarSign, Receipt, FileText, TrendingUp } from 'lucide-react';

export default function BillingPage() {
  const [activeTab, setActiveTab] = useState('invoices');

  return (
    <div>
      <div className="mb-6">
        <h1 className="text-2xl font-bold text-gray-900">Billing & Payments</h1>
        <p className="text-gray-600">Manage invoices, payments, and billing reports</p>
      </div>

      <div className="mb-6 border-b border-gray-200">
        <nav className="-mb-px flex space-x-8">
          {['invoices', 'payments', 'reports'].map((tab) => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`py-2 px-1 border-b-2 font-medium text-sm capitalize ${
                activeTab === tab
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              {tab}
            </button>
          ))}
        </nav>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-blue-100 rounded-lg">
              <Receipt className="w-6 h-6 text-blue-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">$12,450</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">Total Revenue</h3>
          <p className="text-sm text-gray-600">This month</p>
        </div>

        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-green-100 rounded-lg">
              <DollarSign className="w-6 h-6 text-green-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">$8,230</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">Paid</h3>
          <p className="text-sm text-gray-600">This month</p>
        </div>

        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-yellow-100 rounded-lg">
              <FileText className="w-6 h-6 text-yellow-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">$4,220</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">Outstanding</h3>
          <p className="text-sm text-gray-600">This month</p>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Recent Invoices</h2>
        <div className="space-y-3">
          <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div className="flex items-center gap-3">
              <Receipt className="w-5 h-5 text-gray-400" />
              <div>
                <p className="font-medium text-gray-900">INV-001</p>
                <p className="text-sm text-gray-500">Room 101 - John Doe</p>
              </div>
            </div>
            <div className="text-right">
              <p className="font-medium text-gray-900">$450.00</p>
              <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                Paid
              </span>
            </div>
          </div>
          <div className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div className="flex items-center gap-3">
              <Receipt className="w-5 h-5 text-gray-400" />
              <div>
                <p className="font-medium text-gray-900">INV-002</p>
                <p className="text-sm text-gray-500">Room 205 - Jane Smith</p>
              </div>
            </div>
            <div className="text-right">
              <p className="font-medium text-gray-900">$320.00</p>
              <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                Pending
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
