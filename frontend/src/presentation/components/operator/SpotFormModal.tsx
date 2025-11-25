import { useState } from 'react';
import type { ParkingSpot } from '../../../domain/types';

interface SpotFormData {
  number: string;
  type: 'regular' | 'vip' | 'disabled';
  hourly_price: number;
  width: number;
  length: number;
}

interface SpotFormModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (data: SpotFormData) => void;
  spot?: ParkingSpot | null;
}

export function SpotFormModal({ isOpen, onClose, onSubmit, spot }: SpotFormModalProps) {
  const [formData, setFormData] = useState<SpotFormData>({
    number: spot?.number || '',
    type: spot?.type || 'regular',
    hourly_price: spot?.hourly_price || 5,
    width: spot?.width || 2.5,
    length: spot?.length || 5,
  });

  if (!isOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(formData);
    onClose();
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: ['hourly_price', 'width', 'length'].includes(name) ? parseFloat(value) : value
    }));
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl max-w-md w-full">
        <div className="p-6">
          <div className="flex justify-between items-center mb-6">
            <h2 className="text-2xl font-bold text-gray-900">
              {spot ? 'Editar Vaga' : 'Cadastrar Vaga no Sistema'}
            </h2>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600 text-2xl"
            >
              ×
            </button>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Número da Vaga Física *
              </label>
              <input
                type="text"
                name="number"
                value={formData.number}
                onChange={handleChange}
                required
                placeholder="Ex: A01, B05, G12"
                className="input-field"
              />
              <p className="text-xs text-gray-500 mt-1">
                Informe o número pintado/sinalizado na vaga física do estacionamento
              </p>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Tipo de Vaga *
              </label>
              <select
                name="type"
                value={formData.type}
                onChange={handleChange}
                required
                className="input-field"
              >
                <option value="regular">🚗 Regular (Carros)</option>
                <option value="vip">🚚 VIP (Caminhões)</option>
                <option value="disabled">♿ PCD</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Preço por Hora (R$) *
              </label>
              <input
                type="number"
                name="hourly_price"
                value={formData.hourly_price}
                onChange={handleChange}
                required
                step="0.01"
                min="0"
                className="input-field"
              />
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Largura (m) *
                </label>
                <input
                  type="number"
                  name="width"
                  value={formData.width}
                  onChange={handleChange}
                  required
                  step="0.1"
                  min="0"
                  className="input-field"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Comprimento (m) *
                </label>
                <input
                  type="number"
                  name="length"
                  value={formData.length}
                  onChange={handleChange}
                  required
                  step="0.1"
                  min="0"
                  className="input-field"
                />
              </div>
            </div>

            <div className="flex gap-3 pt-4">
              <button
                type="button"
                onClick={onClose}
                className="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50"
              >
                Cancelar
              </button>
              <button
                type="submit"
                className="flex-1 btn-primary"
              >
                {spot ? 'Atualizar' : 'Criar'} Vaga
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
