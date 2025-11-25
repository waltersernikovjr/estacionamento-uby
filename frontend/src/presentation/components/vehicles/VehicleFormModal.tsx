import { useState, type FormEvent } from 'react';

interface VehicleFormData {
  license_plate: string;
  brand: string;
  model: string;
  color: string;
  vehicle_type: 'car' | 'motorcycle' | 'truck';
}

interface VehicleFormModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (data: VehicleFormData) => Promise<void>;
}

export function VehicleFormModal({ isOpen, onClose, onSubmit }: VehicleFormModalProps) {
  const [formData, setFormData] = useState<VehicleFormData>({
    license_plate: '',
    brand: '',
    model: '',
    color: '',
    vehicle_type: 'car',
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setIsSubmitting(true);

    try {
      await onSubmit(formData);
      // Reset form
      setFormData({
        license_plate: '',
        brand: '',
        model: '',
        color: '',
        vehicle_type: 'car',
      });
      onClose();
    } catch (err: any) {
      setError(err?.message || 'Erro ao cadastrar veículo');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleChange = (field: keyof VehicleFormData, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div className="p-6">
          <div className="flex justify-between items-center mb-6">
            <h2 className="text-2xl font-bold text-gray-900">
              Cadastrar Veículo
            </h2>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600 transition-colors"
              type="button"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <div className="bg-red-50 border-2 border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                {error}
              </div>
            )}

            {/* Placa */}
            <div>
              <label htmlFor="license_plate" className="block text-sm font-medium text-gray-700 mb-2">
                Placa *
              </label>
              <input
                id="license_plate"
                type="text"
                value={formData.license_plate}
                onChange={(e) => handleChange('license_plate', e.target.value.toUpperCase())}
                className="input-field"
                placeholder="ABC-1234"
                maxLength={8}
                required
              />
            </div>

            {/* Tipo de Veículo */}
            <div>
              <label htmlFor="vehicle_type" className="block text-sm font-medium text-gray-700 mb-2">
                Tipo *
              </label>
              <select
                id="vehicle_type"
                value={formData.vehicle_type}
                onChange={(e) => handleChange('vehicle_type', e.target.value)}
                className="input-field"
                required
              >
                <option value="car">🚗 Carro</option>
                <option value="motorcycle">🏍️ Moto</option>
                <option value="truck">🚚 Caminhão</option>
              </select>
            </div>

            {/* Marca */}
            <div>
              <label htmlFor="brand" className="block text-sm font-medium text-gray-700 mb-2">
                Marca *
              </label>
              <input
                id="brand"
                type="text"
                value={formData.brand}
                onChange={(e) => handleChange('brand', e.target.value)}
                className="input-field"
                placeholder="Ex: Fiat, Honda, Volkswagen"
                required
              />
            </div>

            {/* Modelo */}
            <div>
              <label htmlFor="model" className="block text-sm font-medium text-gray-700 mb-2">
                Modelo *
              </label>
              <input
                id="model"
                type="text"
                value={formData.model}
                onChange={(e) => handleChange('model', e.target.value)}
                className="input-field"
                placeholder="Ex: Uno, Civic, Gol"
                required
              />
            </div>

            {/* Cor */}
            <div>
              <label htmlFor="color" className="block text-sm font-medium text-gray-700 mb-2">
                Cor *
              </label>
              <input
                id="color"
                type="text"
                value={formData.color}
                onChange={(e) => handleChange('color', e.target.value)}
                className="input-field"
                placeholder="Ex: Preto, Branco, Prata"
                required
              />
            </div>

            {/* Botões */}
            <div className="flex gap-3 pt-4">
              <button
                type="button"
                onClick={onClose}
                className="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors"
                disabled={isSubmitting}
              >
                Cancelar
              </button>
              <button
                type="submit"
                className="flex-1 btn-primary"
                disabled={isSubmitting}
              >
                {isSubmitting ? 'Cadastrando...' : 'Cadastrar'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
