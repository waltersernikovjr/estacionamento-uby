import { useState } from 'react';
import type { Vehicle, ParkingSpot } from '../../../domain/types';
import { getCompatibleVehicles } from '../../../domain/utils/vehicleSpotCompatibility';

interface VehicleSelectionModalProps {
  isOpen: boolean;
  onClose: () => void;
  vehicles: Vehicle[];
  reservedVehicleIds: number[];
  onSelect: (vehicleId: number) => void;
  spot: ParkingSpot | null;
}

export function VehicleSelectionModal({
  isOpen,
  onClose,
  vehicles,
  reservedVehicleIds,
  onSelect,
  spot,
}: VehicleSelectionModalProps) {
  const [selectedVehicleId, setSelectedVehicleId] = useState<number | null>(null);

  if (!isOpen || !spot) return null;

  const compatibleVehicles = getCompatibleVehicles(vehicles, spot);
  
  const availableVehicles = compatibleVehicles.filter(v => !reservedVehicleIds.includes(v.id));

  const spotTypeNames: Record<ParkingSpot['type'], string> = {
    regular: 'carros (vagas regulares)',
    vip: 'caminhões (vagas VIP)',
    disabled: 'carros (vagas PCD)',
  };

  const handleConfirm = () => {
    if (selectedVehicleId) {
      onSelect(selectedVehicleId);
      setSelectedVehicleId(null);
    }
  };

  const handleClose = () => {
    setSelectedVehicleId(null);
    onClose();
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div className="p-6">
          <div className="flex justify-between items-center mb-6">
            <h2 className="text-2xl font-bold text-gray-900">
              Selecionar Veículo
            </h2>
            <button
              onClick={handleClose}
              className="text-gray-400 hover:text-gray-600 text-2xl"
            >
              ×
            </button>
          </div>

          <p className="text-gray-600 mb-4">
            Reservar <strong>Vaga {spot.number}</strong> (para {spotTypeNames[spot.type]}) com qual veículo?
          </p>

          {availableVehicles.length === 0 && compatibleVehicles.length === 0 && (
            <div className="text-center py-8">
              <p className="text-orange-600 font-semibold mb-2">
                ⚠️ Você não possui veículos compatíveis com esta vaga
              </p>
              <p className="text-sm text-gray-500 mb-4">
                Esta vaga é para {spotTypeNames[spot.type]}.
              </p>
            </div>
          )}

          {availableVehicles.length === 0 && compatibleVehicles.length > 0 && (
            <div className="text-center py-8">
              <p className="text-yellow-600 font-semibold mb-2">
                ⚠️ Todos os seus veículos compatíveis já possuem reservas ativas
              </p>
              <p className="text-sm text-gray-500">
                Finalize uma reserva existente antes de criar uma nova.
              </p>
            </div>
          )}
          {availableVehicles.length > 0 && (
            <>
              <div className="space-y-3 mb-6">
                {availableVehicles.map((vehicle) => (
                  <label
                    key={vehicle.id}
                    className={`block p-4 border-2 rounded-xl cursor-pointer transition-all ${
                      selectedVehicleId === vehicle.id
                        ? 'border-primary-600 bg-primary-50'
                        : 'border-gray-200 hover:border-gray-300'
                    }`}
                  >
                    <input
                      type="radio"
                      name="vehicle"
                      value={vehicle.id}
                      checked={selectedVehicleId === vehicle.id}
                      onChange={() => setSelectedVehicleId(vehicle.id)}
                      className="sr-only"
                    />
                    <div className="flex items-center justify-between">
                      <div>
                        <p className="font-semibold text-gray-900">
                          {vehicle.brand} {vehicle.model}
                        </p>
                        <p className="text-sm text-gray-600">
                          Placa: {vehicle.license_plate} • {vehicle.color}
                        </p>
                      </div>
                      {selectedVehicleId === vehicle.id && (
                        <span className="text-primary-600 text-xl">✓</span>
                      )}
                    </div>
                  </label>
                ))}
              </div>

              <div className="flex gap-3">
                <button
                  onClick={handleClose}
                  className="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50"
                >
                  Cancelar
                </button>
                <button
                  onClick={handleConfirm}
                  disabled={!selectedVehicleId}
                  className="flex-1 btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Confirmar Reserva
                </button>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
