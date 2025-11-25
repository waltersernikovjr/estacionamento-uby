import type { ParkingSpot } from '../../../domain/types';

interface ParkingSpotManagementCardProps {
  spot: ParkingSpot;
  onEdit: (spot: ParkingSpot) => void;
  onDelete: (id: number) => void;
  onToggleStatus: (id: number, status: ParkingSpot['status']) => void;
}

const spotTypeLabel: Record<string, string> = {
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

export function ParkingSpotManagementCard({
  spot,
  onEdit,
  onDelete,
  onToggleStatus,
}: ParkingSpotManagementCardProps) {
  const status = statusLabel[spot.status] || statusLabel.available;

  const handleStatusChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    onToggleStatus(spot.id, e.target.value as ParkingSpot['status']);
  };

  return (
    <div className="card">
      <div className="flex justify-between items-start mb-4">
        <div>
          <h3 className="text-xl font-bold text-gray-900">Vaga {spot.number}</h3>
          <p className="text-sm text-gray-600">{spotTypeLabel[spot.type]}</p>
        </div>
        <span className={`px-3 py-1 rounded-full text-xs font-semibold ${status.color}`}>
          {status.label}
        </span>
      </div>

      <div className="space-y-2 mb-4 text-sm">
        <div className="flex justify-between">
          <span className="text-gray-600">Preço/hora:</span>
          <span className="font-semibold text-gray-900">R$ {spot.hourly_price.toFixed(2)}</span>
        </div>
        <div className="flex justify-between">
          <span className="text-gray-600">Dimensões:</span>
          <span className="text-gray-900">{spot.width}m × {spot.length}m</span>
        </div>
      </div>

      <div className="space-y-2">
        <select
          value={spot.status}
          onChange={handleStatusChange}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
        >
          <option value="available">Disponível</option>
          <option value="occupied">Ocupado</option>
          <option value="reserved">Reservado</option>
          <option value="maintenance">Manutenção</option>
        </select>

        <div className="flex gap-2">
          <button
            onClick={() => onEdit(spot)}
            className="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors text-sm"
          >
            Editar
          </button>
          <button
            onClick={() => onDelete(spot.id)}
            className="flex-1 px-4 py-2 bg-red-100 text-red-700 rounded-xl font-semibold hover:bg-red-200 transition-colors text-sm"
          >
            Excluir
          </button>
        </div>
      </div>
    </div>
  );
}
