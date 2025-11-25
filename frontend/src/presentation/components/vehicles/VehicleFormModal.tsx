import { useState, useEffect, type FormEvent } from 'react';
import type { Vehicle } from '../../../domain/types';

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
  editingVehicle?: Vehicle | null;
}

export function VehicleFormModal({ isOpen, onClose, onSubmit, editingVehicle }: VehicleFormModalProps) {
  const [formData, setFormData] = useState<VehicleFormData>({
    license_plate: '',
    brand: '',
    model: '',
    color: '',
    vehicle_type: 'car',
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState('');

  // Preencher formulário quando estiver editando
  useEffect(() => {
    if (isOpen) {
      setError('');
      
      if (editingVehicle) {
        setFormData({
          license_plate: editingVehicle.license_plate,
          brand: editingVehicle.brand,
          model: editingVehicle.model,
          color: editingVehicle.color,
          vehicle_type: editingVehicle.vehicle_type,
        });
      } else {
        setFormData({
          license_plate: '',
          brand: '',
          model: '',
          color: '',
          vehicle_type: 'car',
        });
      }
    }
  }, [editingVehicle, isOpen]);

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
      const response = err?.response;
      const backendMessage = response?.data?.message;
      const validationErrors = response?.data?.errors;
      
      if (validationErrors && Object.keys(validationErrors).length > 0) {
        const plateError = validationErrors.license_plate;
        if (plateError) {
          setError(Array.isArray(plateError) ? plateError[0] : plateError);
        } else {
          const firstError = Object.values(validationErrors)[0];
          setError(Array.isArray(firstError) ? firstError[0] : String(firstError));
        }
      } else if (backendMessage) {
        setError(backendMessage);
      } else if (response?.statusText) {
        setError(`Erro ${response.status}: ${response.statusText}`);
      } else {
        setError('Erro ao salvar veículo. Tente novamente.');
      }
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleChange = (field: keyof VehicleFormData, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handlePlateChange = (value: string) => {
    const cleaned = value.replace(/[^A-Z0-9]/gi, '').toUpperCase();
    handleChange('license_plate', cleaned);
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div className="p-6">
          <div className="flex justify-between items-center mb-6">
            <h2 className="text-2xl font-bold text-gray-900">
              {editingVehicle ? 'Editar Veículo' : 'Cadastrar Veículo'}
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
                Placa * <span className="text-xs text-gray-500">(7 caracteres, ex: ABC1D23)</span>
              </label>
              <div className="relative">
                <input
                  id="license_plate"
                  type="text"
                  value={formData.license_plate}
                  onChange={(e) => handlePlateChange(e.target.value)}
                  className={`input-field ${editingVehicle ? 'bg-gray-100 cursor-not-allowed' : ''}`}
                  placeholder="ABC1D23"
                  maxLength={7}
                  disabled={!!editingVehicle}
                  required
                />
                {editingVehicle && (
                  <div className="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg className="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clipRule="evenodd"/>
                    </svg>
                  </div>
                )}
              </div>
              {editingVehicle && (
                <p className="text-xs text-gray-500 mt-1 flex items-center gap-1">
                  <svg className="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd"/>
                  </svg>
                  A placa não pode ser alterada após o cadastro
                </p>
              )}
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
                {isSubmitting 
                  ? (editingVehicle ? 'Salvando...' : 'Cadastrando...') 
                  : (editingVehicle ? 'Salvar' : 'Cadastrar')
                }
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
