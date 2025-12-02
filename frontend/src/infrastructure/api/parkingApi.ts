import { httpClient } from './httpClient';
import type { ApiResponse, ParkingSpot, Reservation, Vehicle } from '../../domain/types';

export const parkingApi = {
  async getAvailableSpots(): Promise<ParkingSpot[]> {
    const response = await httpClient.get<ApiResponse<ParkingSpot[]>>('/parking-spots-available');
    return response.data.data;
  },

  async getSpotById(id: number): Promise<ParkingSpot> {
    const response = await httpClient.get<ApiResponse<ParkingSpot>>(`/parking-spots/${id}`);
    return response.data.data;
  },

  async createReservation(data: {
    parking_spot_id: number;
    vehicle_id: number;
  }): Promise<Reservation> {
    const payload = {
      ...data,
      entry_time: new Date().toISOString(),
    };
    const response = await httpClient.post<ApiResponse<Reservation>>('/reservations', payload);
    return response.data.data;
  },

  async getMyReservations(): Promise<Reservation[]> {
    const response = await httpClient.get<ApiResponse<Reservation[]>>('/reservations');
    return response.data.data;
  },

  async completeReservation(id: number): Promise<Reservation> {
    const response = await httpClient.post<ApiResponse<Reservation>>(`/reservations/${id}/complete`);
    return response.data.data;
  },

  async checkoutReservation(id: number): Promise<Reservation> {
    const payload = {
      exit_time: new Date().toISOString(),
    };
    const response = await httpClient.post<ApiResponse<Reservation>>(`/reservations/${id}/complete`, payload);
    return response.data.data;
  },

  async cancelReservation(id: number): Promise<void> {
    await httpClient.post(`/reservations/${id}/cancel`);
  },

  async getMyVehicles(): Promise<Vehicle[]> {
    const response = await httpClient.get<ApiResponse<Vehicle[]>>('/vehicles');
    return response.data.data.map(v => ({
      ...v,
      vehicle_type: (v as any).type || v.vehicle_type
    } as Vehicle));
  },

  async addVehicle(data: {
    license_plate: string;
    brand: string;
    model: string;
    color: string;
    vehicle_type: 'car' | 'motorcycle' | 'truck';
  }): Promise<Vehicle> {
    const payload = {
      license_plate: data.license_plate,
      brand: data.brand,
      model: data.model,
      color: data.color,
      type: data.vehicle_type,
    };
    
    const response = await httpClient.post<ApiResponse<Vehicle>>('/vehicles', payload);
    const vehicle = response.data.data;
    return {
      ...vehicle,
      vehicle_type: (vehicle as any).type || vehicle.vehicle_type
    } as Vehicle;
  },

  async removeVehicle(id: number): Promise<void> {
    await httpClient.delete(`/vehicles/${id}`);
  },
};

