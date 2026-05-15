import { useState, useEffect } from 'react';
import { roomsApi, Room } from '../lib/api';
import { Plus, Search, RefreshCw, DoorOpen, Users, DollarSign } from 'lucide-react';

export default function RoomsPage() {
  const [rooms, setRooms] = useState<Room[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');

  const fetchRooms = async () => {
    try {
      const response = await roomsApi.list();
      setRooms(response.data);
    } catch (error) {
      console.error('Failed to fetch rooms:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchRooms();
  }, []);

  const filteredRooms = rooms.filter(room =>
    room.room_number.toLowerCase().includes(search.toLowerCase())
  );

  const statusBadge = {
    available: 'bg-green-100 text-green-800',
    booked: 'bg-blue-100 text-blue-800',
    occupied: 'bg-yellow-100 text-yellow-800',
    maintenance: 'bg-red-100 text-red-800',
    cleaning: 'bg-purple-100 text-purple-800',
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Rooms</h1>
          <p className="text-gray-600">Manage lodge rooms</p>
        </div>
        <div className="flex gap-3">
          <button
            onClick={fetchRooms}
            className="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
            title="Refresh"
          >
            <RefreshCw className="w-5 h-5" />
          </button>
          <button className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <Plus className="w-4 h-4" />
            Add Room
          </button>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-sm border border-gray-200">
        <div className="p-4 border-b border-gray-200">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
            <input
              type="text"
              placeholder="Search rooms..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-4">
          {filteredRooms.length === 0 ? (
            <div className="col-span-full text-center py-8 text-gray-500">
              No rooms found
            </div>
          ) : (
            filteredRooms.map((room) => (
              <div key={room.id} className="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div className="flex items-start justify-between mb-3">
                  <div>
                    <h3 className="font-semibold text-gray-900">Room {room.room_number}</h3>
                    <p className="text-sm text-gray-500">Type: {room.room_type_id}</p>
                  </div>
                  <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusBadge[room.status]}`}>
                    {room.status}
                  </span>
                </div>
                
                <div className="space-y-2 text-sm">
                  <div className="flex items-center gap-2">
                    <Users className="w-4 h-4 text-gray-400" />
                    <span className="text-gray-600">Capacity: {room.capacity}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <DollarSign className="w-4 h-4 text-gray-400" />
                    <span className="text-gray-600">${room.price}/night</span>
                  </div>
                </div>

                <div className="mt-4 flex gap-2">
                  <button className="flex-1 px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">
                    Edit
                  </button>
                  <button className="flex-1 px-3 py-1 text-sm bg-gray-50 text-gray-700 rounded hover:bg-gray-100 transition-colors">
                    View
                  </button>
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </div>
  );
}
