import type { Vehicle, ParkingSpot, VehicleType, ParkingSpotType } from '../types';

/**
 * Mapeia o tipo de vaga do backend para o tipo de veículo compatível.
 * Backend: regular, vip, disabled
 * Veículos: car, motorcycle, truck
 */
export function mapSpotTypeToVehicleType(spotType: ParkingSpotType): VehicleType {
  const mapping: Record<ParkingSpotType, VehicleType> = {
    regular: 'car',      // Vagas regulares são para carros
    vip: 'truck',        // Vagas VIP são para caminhões
    disabled: 'car',     // Vagas PCD são para carros
  };
  return mapping[spotType];
}

/**
 * Verifica se um veículo é compatível com uma vaga.
 */
export function isVehicleCompatibleWithSpot(
  vehicleType: VehicleType,
  spotType: ParkingSpotType
): boolean {
  const compatibleVehicleType = mapSpotTypeToVehicleType(spotType);
  return vehicleType === compatibleVehicleType;
}

/**
 * Filtra veículos compatíveis com uma vaga específica.
 */
export function getCompatibleVehicles(
  vehicles: Vehicle[],
  spot: ParkingSpot
): Vehicle[] {
  return vehicles.filter(v => isVehicleCompatibleWithSpot(v.vehicle_type, spot.type));
}

/**
 * Verifica se o usuário tem algum veículo compatível com a vaga.
 */
export function hasCompatibleVehicle(
  vehicles: Vehicle[],
  spot: ParkingSpot
): boolean {
  return getCompatibleVehicles(vehicles, spot).length > 0;
}

/**
 * Retorna mensagem explicativa sobre incompatibilidade.
 */
export function getIncompatibilityMessage(
  vehicles: Vehicle[],
  spot: ParkingSpot
): string {
  const spotTypeNames: Record<ParkingSpotType, string> = {
    regular: 'carros (vagas regulares)',
    vip: 'caminhões (vagas VIP)',
    disabled: 'carros (vagas PCD)',
  };

  const userVehicleTypes = [...new Set(vehicles.map(v => v.vehicle_type))];
  const userTypeNames: Record<VehicleType, string> = {
    car: 'carro(s)',
    motorcycle: 'moto(s)',
    truck: 'caminhão(ões)',
  };

  const spotTypeName = spotTypeNames[spot.type] || `vagas do tipo ${spot.type}`;
  const vehicleTypesList = userVehicleTypes.map(t => userTypeNames[t] || t).join(', ');
  
  if (vehicles.length === 0) {
    return `Esta vaga é exclusiva para ${spotTypeName}. Cadastre um veículo compatível primeiro.`;
  }
  
  return `Esta vaga é exclusiva para ${spotTypeName}. Você possui: ${vehicleTypesList}.`;
}
