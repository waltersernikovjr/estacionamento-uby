import { httpClient } from './httpClient';
import type { Vehicle, ApiResponse } from '../../domain/types';

interface CreateVehicleData {
  plate: string;
  brand: string;
  model: string;
  color: string;
  type: 'car' | 'motorcycle' | 'truck';
  year?: number;
}

interface UpdateVehicleData {
  license_plate?: string;
  brand?: string;
  model?: string;
  color?: string;
  vehicle_type?: 'car' | 'motorcycle' | 'truck';
  type?: 'car' | 'motorcycle' | 'truck';
}

export const vehicleApi = {
  
  async getMyVehicles(): Promise<Vehicle[]> {
    const response = await httpClient.get<ApiResponse<Vehicle[]>>('/vehicles');
    
    return response.data.data.map(v => ({
      ...v,
      vehicle_type: v.type || (v as any).vehicle_type
    } as Vehicle));
  },

  
  async getVehicleById(id: number): Promise<Vehicle> {
    const response = await httpClient.get<ApiResponse<Vehicle>>(`/vehicles/${id}`);
    return response.data.data;
  },

  
  async createVehicle(data: CreateVehicleData): Promise<Vehicle> {
    const response = await httpClient.post<ApiResponse<Vehicle>>('/vehicles', data);
    return response.data.data;
  },

  
  async updateVehicle(id: number, data: UpdateVehicleData): Promise<Vehicle> {
    const payload: any = {
      ...data,
      type: data.vehicle_type || data.type,
    };
    delete payload.vehicle_type;
    delete payload.license_plate;
    
    const response = await httpClient.put<ApiResponse<Vehicle>>(`/vehicles/${id}`, payload);
    return response.data.data;
  },

  
  async deleteVehicle(id: number): Promise<void> {
    await httpClient.delete(`/vehicles/${id}`);
  },
};
