import type { ParkingSpot, Vehicle } from '../../../domain/types';
import { hasCompatibleVehicle, getIncompatibilityMessage } from '../../../domain/utils/vehicleSpotCompatibility';

interface ParkingSpotCardProps {
  spot: ParkingSpot;
  onReserve: (spotId: number) => void;
  isReserving?: boolean;
  userVehicles?: Vehicle[];
}

const spotTypeLabel: Record<string, string> = {
  regular: '🚗 Carro (Regular)',
  vip: '🚚 Caminhão (VIP)',
  disabled: '♿ PCD',
  car: '🚗 Carro',
  motorcycle: '🏍️ Moto',
  truck: '🚚 Caminhão',
};

const statusLabel: Record<string, { label: string; color: string }> = {
  available: { label: 'Disponível', color: 'bg-green-100 text-green-800' },
  occupied: { label: 'Ocupado', color: 'bg-red-100 text-red-800' },
  reserved: { label: 'Reservado', color: 'bg-yellow-100 text-yellow-800' },
  maintenance: { label: 'Manutenção', color: 'bg-gray-100 text-gray-800' },
};

export function ParkingSpotCard({ spot, onReserve, isReserving, userVehicles = [] }: ParkingSpotCardProps) {
  const status = statusLabel[spot.status] || statusLabel.available;
  const spotLabel = spotTypeLabel[spot.type] || `Vaga ${spot.type}`;
  const isAvailable = spot.status === 'available';
  const isCompatible = userVehicles.length === 0 || hasCompatibleVehicle(userVehicles, spot);
  const canReserve = isAvailable && isCompatible;
  const incompatibilityMsg = !isCompatible ? getIncompatibilityMessage(userVehicles, spot) : '';

  return (
    <div className="card hover:shadow-lg transition-shadow">
      <div className="flex justify-between items-start mb-4">
        <div>
          <h3 className="text-xl font-bold text-gray-900">Vaga {spot.number}</h3>
          <p className="text-sm text-gray-600">{spotLabel}</p>
        </div>
        <span className={`px-3 py-1 rounded-full text-xs font-semibold ${status.color}`}>
          {status.label}
        </span>
      </div>

      <div className="space-y-2 mb-4">
        <div className="flex justify-between text-sm">
          <span className="text-gray-600">Preço por hora:</span>
          <span className="font-semibold text-gray-900">
            R$ {spot.hourly_price.toFixed(2)}
          </span>
        </div>
        {spot.width && spot.length && (
          <div className="flex justify-between text-sm">
            <span className="text-gray-600">Dimensões:</span>
            <span className="text-gray-900">{spot.width}m × {spot.length}m</span>
          </div>
        )}
      </div>

      {canReserve && (
        <button
          onClick={() => onReserve(spot.id)}
          disabled={isReserving}
          className="btn-primary w-full"
        >
          {isReserving ? 'Reservando...' : 'Reservar'}
        </button>
      )}

      {isAvailable && !isCompatible && (
        <div className="relative group">
          <button
            disabled
            className="w-full px-4 py-2 bg-orange-100 text-orange-600 rounded-xl font-semibold cursor-not-allowed border-2 border-orange-300"
          >
            ⚠️ Veículo Incompatível
          </button>
          <div className="absolute bottom-full left-0 right-0 mb-2 hidden group-hover:block">
            <div className="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg">
              {incompatibilityMsg}
            </div>
          </div>
        </div>
      )}

      {!isAvailable && (
        <button
          disabled
          className="w-full px-4 py-2 bg-gray-100 text-gray-400 rounded-xl font-semibold cursor-not-allowed"
        >
          Indisponível
        </button>
      )}
    </div>
  );
}
