import { useState } from 'react';
import { CheckSquare, Plus, Search, Clock, User, AlertCircle } from 'lucide-react';

export default function TasksPage() {
  const [search, setSearch] = useState('');
  const [filter, setFilter] = useState('all');

  const tasks = [
    {
      id: 1,
      title: 'Clean Room 101',
      description: 'Guest checked out, room needs cleaning',
      assignee: 'John Doe',
      status: 'pending',
      priority: 'high',
      dueDate: '2024-03-15',
    },
    {
      id: 2,
      title: 'Fix leaking faucet in Room 205',
      description: 'Reported by guest, needs immediate attention',
      assignee: 'Mike Wilson',
      status: 'in-progress',
      priority: 'high',
      dueDate: '2024-03-14',
    },
    {
      id: 3,
      title: 'Restock toiletries',
      description: 'Shampoo and soap running low',
      assignee: 'Sarah Johnson',
      status: 'completed',
      priority: 'medium',
      dueDate: '2024-03-13',
    },
  ];

  const statusBadge = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'in-progress': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800',
  };

  const priorityBadge = {
    'low': 'bg-gray-100 text-gray-800',
    'medium': 'bg-orange-100 text-orange-800',
    'high': 'bg-red-100 text-red-800',
  };

  const filteredTasks = tasks.filter(task => {
    const matchesSearch = task.title.toLowerCase().includes(search.toLowerCase()) ||
                         task.description.toLowerCase().includes(search.toLowerCase());
    const matchesFilter = filter === 'all' || task.status === filter;
    return matchesSearch && matchesFilter;
  });

  return (
    <div>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Tasks</h1>
          <p className="text-gray-600">Manage lodge tasks and maintenance</p>
        </div>
        <div className="flex gap-3">
          <button className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <Plus className="w-4 h-4" />
            Add Task
          </button>
        </div>
      </div>

      <div className="mb-6 flex flex-wrap gap-4">
        <div className="relative flex-1 min-w-[200px]">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
          <input
            type="text"
            placeholder="Search tasks..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <select
          value={filter}
          onChange={(e) => setFilter(e.target.value)}
          className="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          aria-label="Filter tasks by status"
          title="Filter tasks by status"
        >
          <option value="all">All Tasks</option>
          <option value="pending">Pending</option>
          <option value="in-progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-yellow-100 rounded-lg">
              <Clock className="w-6 h-6 text-yellow-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">8</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">Pending Tasks</h3>
          <p className="text-sm text-gray-600">Awaiting action</p>
        </div>

        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-blue-100 rounded-lg">
              <AlertCircle className="w-6 h-6 text-blue-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">5</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">In Progress</h3>
          <p className="text-sm text-gray-600">Being worked on</p>
        </div>

        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <div className="p-3 bg-green-100 rounded-lg">
              <CheckSquare className="w-6 h-6 text-green-600" />
            </div>
            <span className="text-2xl font-bold text-gray-900">23</span>
          </div>
          <h3 className="text-sm font-medium text-gray-900">Completed</h3>
          <p className="text-sm text-gray-600">This week</p>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-sm border border-gray-200">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50 border-b border-gray-200">
              <tr>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Task
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Assignee
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Priority
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Due Date
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {filteredTasks.length === 0 ? (
                <tr>
                  <td colSpan={6} className="px-4 py-8 text-center text-gray-500">
                    No tasks found
                  </td>
                </tr>
              ) : (
                filteredTasks.map((task) => (
                  <tr key={task.id} className="hover:bg-gray-50">
                    <td className="px-4 py-3">
                      <div>
                        <div className="font-medium text-gray-900">{task.title}</div>
                        <div className="text-sm text-gray-500">{task.description}</div>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex items-center gap-2">
                        <div className="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                          <User className="w-3 h-3 text-gray-600" />
                        </div>
                        <span className="text-sm text-gray-900">{task.assignee}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${priorityBadge[task.priority]}`}>
                        {task.priority}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-sm text-gray-900">
                      {task.dueDate}
                    </td>
                    <td className="px-4 py-3">
                      <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusBadge[task.status]}`}>
                        {task.status.replace('-', ' ')}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-sm">
                      <button className="text-blue-600 hover:text-blue-900 font-medium mr-3">
                        Edit
                      </button>
                      <button className="text-green-600 hover:text-green-900 font-medium">
                        Complete
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
