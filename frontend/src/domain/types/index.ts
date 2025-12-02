export interface User {
  id: number;
  name: string;
  email: string;
  type: 'customer' | 'operator';
}

export interface Customer extends User {
  type: 'customer';
  cpf: string;
  phone: string;
  address: Address;
  vehicles: Vehicle[];
}

export interface Operator extends User {
  type: 'operator';
  registrationNumber: string;
}

export interface Address {
  street: string;
  number: string;
  complement?: string;
  neighborhood: string;
  city: string;
  state: string;
  zipCode: string;
}

export type VehicleType = 'car' | 'motorcycle' | 'truck';
export type ParkingSpotType = 'regular' | 'vip' | 'disabled';

export interface Vehicle {
  id: number;
  customer_id: number;
  license_plate: string;
  brand: string;
  model: string;
  color: string;
  vehicle_type: VehicleType;
  type?: VehicleType;
}

export interface ParkingSpot {
  id: number;
  number: string;
  type: ParkingSpotType;
  status: 'available' | 'occupied' | 'reserved' | 'maintenance';
  hourly_price: number;
  width: number;
  length: number;
  operator_id: number | null;
  created_at: string;
  updated_at: string;
}

export interface Reservation {
  id: number;
  customer_id: number;
  vehicle_id: number;
  parking_spot_id: number;
  entry_time: string;
  exit_time: string | null;
  status: 'active' | 'completed' | 'cancelled';
  total_price: number | null;
  customer?: Customer;
  vehicle?: Vehicle;
  parking_spot?: ParkingSpot;
  payment?: Payment;
}

export interface Payment {
  id: number;
  reservation_id: number;
  amount: number;
  payment_method: 'credit_card' | 'debit_card' | 'pix' | 'cash';
  payment_status: 'pending' | 'completed' | 'failed';
  paid_at: string | null;
}

export interface ChatSession {
  id: number;
  customer_id: number;
  operator_id: number | null;
  status: 'waiting' | 'active' | 'closed';
  started_at: string;
  closed_at: string | null;
  customer?: Customer;
  operator?: Operator;
  messages: ChatMessage[];
}

export interface ChatMessage {
  id: number;
  session_id: number;
  sender_id: number;
  sender_type: 'customer' | 'operator';
  message: string;
  sent_at: string;
  read_at: string | null;
}

export interface Message {
  id?: number;
  senderId: number;
  senderType: 'customer' | 'operator';
  senderName: string;
  recipientId?: number;
  message: string;
  timestamp: string;
  customerId?: number;
}

export interface AuthResponse {
  user?: Customer | Operator;
  token?: string;
  message?: string;
  email?: string;
  requires_verification?: boolean;
}

export interface ApiResponse<T = any> {
  data: T;
  message?: string;
}

export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}
