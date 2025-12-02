import { useState } from 'react';
import type { Reservation } from '../../../domain/types';

interface FinishReservationModalProps {
  isOpen: boolean;
  reservation: Reservation | null;
  onClose: () => void;
  onConfirm: (notes?: string) => void;
}

export function FinishReservationModal({ 
  isOpen, 
  reservation, 
  onClose, 
  onConfirm 
}: FinishReservationModalProps) {
  const [notes, setNotes] = useState('');
  const [isProcessing, setIsProcessing] = useState(false);

  if (!isOpen || !reservation) return null;

  const handleConfirm = async () => {
    setIsProcessing(true);
    try {
      await onConfirm(notes.trim() || undefined);
      setNotes('');
      onClose();
    } catch (err) {
      console.error('Erro ao finalizar:', err);
    } finally {
      setIsProcessing(false);
    }
  };

  const entryTime = new Date(reservation.entry_time);
  const now = new Date();
  const elapsedMs = now.getTime() - entryTime.getTime();
  const hours = Math.floor(elapsedMs / (1000 * 60 * 60));
  const minutes = Math.floor((elapsedMs % (1000 * 60 * 60)) / (1000 * 60));

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div className="p-6">
          <div className="flex justify-between items-start mb-6">
            <div>
              <h2 className="text-2xl font-bold text-gray-900">
                ⚠️ Reserva Ativa Detectada
              </h2>
              <p className="text-sm text-gray-600 mt-1">
                Esta vaga possui uma reserva em andamento
              </p>
            </div>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600 text-2xl"
            >
              ×
            </button>
          </div>

          <div className="space-y-4 mb-6">
            <div className="bg-blue-50 border border-blue-200 rounded-xl p-4">
              <h3 className="font-semibold text-blue-900 mb-3">Informações da Reserva</h3>
              
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-blue-700">Vaga:</span>
                  <span className="font-semibold text-blue-900">
                    {reservation.parking_spot?.number || 'N/A'}
                  </span>
                </div>

                <div className="flex justify-between">
                  <span className="text-blue-700">Cliente:</span>
                  <span className="font-semibold text-blue-900">
                    {reservation.customer?.name || 'N/A'}
                  </span>
                </div>

                <div className="flex justify-between">
                  <span className="text-blue-700">Veículo:</span>
                  <span className="font-semibold text-blue-900">
                    {reservation.vehicle?.brand} {reservation.vehicle?.model}
                  </span>
                </div>

                <div className="flex justify-between">
                  <span className="text-blue-700">Placa:</span>
                  <span className="font-semibold text-blue-900">
                    {reservation.vehicle?.license_plate}
                  </span>
                </div>

                <div className="flex justify-between">
                  <span className="text-blue-700">Tempo decorrido:</span>
                  <span className="font-semibold text-blue-900">
                    {hours}h {minutes}min
                  </span>
                </div>

                <div className="flex justify-between">
                  <span className="text-blue-700">Valor estimado:</span>
                  <span className="font-semibold text-blue-900">
                    R$ {(reservation.total_price || 0).toFixed(2)}
                  </span>
                </div>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Observações (opcional)
              </label>
              <textarea
                value={notes}
                onChange={(e) => setNotes(e.target.value)}
                placeholder="Ex: Cliente saiu mais cedo, solicitou finalização antecipada..."
                className="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                rows={3}
              />
            </div>

            <div className="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
              <p className="text-sm text-yellow-800">
                <strong>Atenção:</strong> Ao confirmar, a reserva será finalizada automaticamente 
                e o valor será calculado com base no tempo decorrido.
              </p>
            </div>
          </div>

          <div className="flex gap-3">
            <button
              onClick={onClose}
              disabled={isProcessing}
              className="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors disabled:opacity-50"
            >
              Cancelar
            </button>
            <button
              onClick={handleConfirm}
              disabled={isProcessing}
              className="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50"
            >
              {isProcessing ? 'Finalizando...' : 'Finalizar Reserva'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
