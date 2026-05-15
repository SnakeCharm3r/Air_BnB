import { useState } from 'react';
import { Package, Plus, Search, AlertTriangle } from 'lucide-react';

export default function InventoryPage() {
  const [search, setSearch] = useState('');

  return (
    <div>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Inventory</h1>
          <p className="text-gray-600">Manage lodge inventory and supplies</p>
        </div>
        <div className="flex gap-3">
          <button className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <Plus className="w-4 h-4" />
            Add Item
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-blue-100 rounded-lg">
              <Package className="w-6 h-6 text-blue-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">245</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">Total Items</h3>
          <p className="text-sm text-gray-600">In inventory</p>
        </div>

        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-yellow-100 rounded-lg">
              <AlertTriangle className="w-6 h-6 text-yellow-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">12</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">Low Stock</h3>
          <p className="text-sm text-gray-600">Items to restock</p>
        </div>

        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-green-100 rounded-lg">
              <Package className="w-6 h-6 text-green-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">$8,450</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">Total Value</h3>
          <p className="text-sm text-gray-600">Of inventory</p>
        </div>

        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-purple-100 rounded-lg">
              <Package className="w-6 h-6 text-purple-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">18</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">Categories</h3>
          <p className="text-sm text-gray-600">Item types</p>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-sm border border-gray-200">
        <div className="p-4 border-b border-gray-200">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
            <input
              type="text"
              placeholder="Search inventory..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50 border-b border-gray-200">
              <tr>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Item
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Category
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Quantity
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Unit Price
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total Value
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              <tr className="hover:bg-gray-50">
                <td className="px-4 py-3">
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                      <Package className="w-4 h-4 text-gray-600" />
                    </div>
                    <div>
                      <div className="font-medium text-gray-900">Towel Set</div>
                      <div className="text-sm text-gray-500">Premium cotton</div>
                    </div>
                  </div>
                </td>
                <td className="px-4 py-3 text-sm text-gray-900">Linens</td>
                <td className="px-4 py-3 text-sm text-gray-900">45</td>
                <td className="px-4 py-3 text-sm text-gray-900">$25.00</td>
                <td className="px-4 py-3 text-sm text-gray-900">$1,125.00</td>
                <td className="px-4 py-3">
                  <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                    In Stock
                  </span>
                </td>
              </tr>
              <tr className="hover:bg-gray-50">
                <td className="px-4 py-3">
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                      <Package className="w-4 h-4 text-gray-600" />
                    </div>
                    <div>
                      <div className="font-medium text-gray-900">Shampoo</div>
                      <div className="text-sm text-gray-500">Travel size</div>
                    </div>
                  </div>
                </td>
                <td className="px-4 py-3 text-sm text-gray-900">Toiletries</td>
                <td className="px-4 py-3 text-sm text-gray-900">8</td>
                <td className="px-4 py-3 text-sm text-gray-900">$3.50</td>
                <td className="px-4 py-3 text-sm text-gray-900">$28.00</td>
                <td className="px-4 py-3">
                  <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Low Stock
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
