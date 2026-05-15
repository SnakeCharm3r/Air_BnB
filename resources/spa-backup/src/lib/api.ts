import axios from 'axios';

export const api = axios.create({
  baseURL: '/api',
  timeout: 10000,
});

// Types
export interface User {
  id: string;
  name: string;
  email: string;
  role: string;
  phone?: string;
  full_name?: string;
}

export interface Room {
  id: string;
  room_number: string;
  room_type_id: string;
  status: 'available' | 'booked' | 'occupied' | 'maintenance' | 'cleaning';
  price: number;
  capacity: number;
  notes?: string;
}

export interface Booking {
  id: string;
  booking_ref: string;
  guest_name: string;
  guest_email?: string;
  guest_phone?: string;
  room_id: string;
  check_in_date: string;
  check_out_date: string;
  status: 'pending' | 'confirmed' | 'checked_in' | 'checked_out' | 'cancelled';
  special_requests?: string;
  total_amount?: number;
  balance_due?: number;
  retainer_paid?: number;
  adults?: number;
  children?: number;
  room_number?: string;
  room_type_name?: string;
}

export interface Staff {
  id: string;
  name: string;
  email: string;
  role: string;
  department: string;
  phone?: string;
  status: 'active' | 'inactive';
  hire_date: string;
}

// API endpoints
export const authApi = {
  login: (credentials: { email: string; password: string }) => 
    api.post('/auth/login', credentials),
  register: (data: any) => 
    api.post('/auth/register', data),
  me: () => 
    api.get('/auth/me'),
  logout: () => 
    api.post('/auth/logout'),
};

export const bookingsApi = {
  list: () => api.get<Booking[]>('/bookings'),
  create: (data: Partial<Booking>) => api.post<Booking>('/bookings', data),
  update: (id: string, data: Partial<Booking>) => api.put(`/bookings/${id}`, data),
  delete: (id: string) => api.delete(`/bookings/${id}`),
  checkIn: (id: string) => api.post(`/bookings/${id}/checkin`),
  checkOut: (id: string) => api.post(`/bookings/${id}/checkout`),
};

export const roomsApi = {
  list: () => api.get<Room[]>('/rooms'),
  create: (data: Partial<Room>) => api.post<Room>('/rooms', data),
  update: (id: string, data: Partial<Room>) => api.put(`/rooms/${id}`, data),
  delete: (id: string) => api.delete(`/rooms/${id}`),
  getAvailable: (dates: { check_in: string; check_out: string }) => 
    api.get<Room[]>('/rooms/available', { params: dates }),
};

export const staffApi = {
  list: () => api.get<Staff[]>('/staff'),
  create: (data: Partial<Staff>) => api.post<Staff>('/staff', data),
  update: (id: string, data: Partial<Staff>) => api.put(`/staff/${id}`, data),
  delete: (id: string) => api.delete(`/staff/${id}`),
  attendance: (id: string) => api.get(`/staff/${id}/attendance`),
};

export const dashboardApi = {
  stats: () => api.get('/dashboard/stats'),
  notifications: () => api.get('/dashboard/notifications'),
};
