import { useState, useEffect } from 'react';
import type { Reservation } from '../../../domain/types';

interface ReservationCardProps {
  reservation: Reservation;
  onCancel: (id: number) => void;
  onCheckout?: (id: number) => void;
}

const statusLabel: Record<string, { label: string; color: string }> = {
  active: { label: 'Ativa', color: 'bg-green-100 text-green-800' },
  completed: { label: 'Finalizada', color: 'bg-blue-100 text-blue-800' },
  cancelled: { label: 'Cancelada', color: 'bg-red-100 text-red-800' },
};

export function ReservationCard({ reservation, onCancel, onCheckout }: ReservationCardProps) {
  const status = statusLabel[reservation.status] || statusLabel.active;
  const canCancel = reservation.status === 'active';
  const canCheckout = reservation.status === 'active';
  
  const [currentCost, setCurrentCost] = useState(0);
  const [elapsedTime, setElapsedTime] = useState('');

  const formatDate = (date: string) => {
    return new Date(date).toLocaleString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const calculateCurrentCost = () => {
    if (reservation.status !== 'active' || !reservation.entry_time) return;
    
    const entryTime = new Date(reservation.entry_time);
    const now = new Date();
    const diffMs = now.getTime() - entryTime.getTime();
    const diffHours = diffMs / (1000 * 60 * 60);
    
    const hours = Math.floor(diffHours);
    const minutes = Math.floor((diffHours - hours) * 60);
    setElapsedTime(`${hours}h ${minutes}min`);
    
    const hourlyPrice = reservation.parking_spot?.hourly_price || 5.0;
    const cost = diffHours * hourlyPrice;
    setCurrentCost(cost);
  };

  useEffect(() => {
    if (reservation.status === 'active') {
      calculateCurrentCost();
      const interval = setInterval(calculateCurrentCost, 10000);
      return () => clearInterval(interval);
    }
  }, [reservation]);

  const displayPrice = reservation.status === 'active' 
    ? currentCost 
    : (reservation.total_price || 0);
  
  const priceValue = Number(displayPrice) || 0;

  return (
    <div className="card">
      <div className="flex justify-between items-start mb-4">
        <div>
          <h3 className="text-lg font-bold text-gray-900">
            Vaga {reservation.parking_spot?.number || `#${reservation.parking_spot_id}`}
          </h3>
          <p className="text-sm text-gray-600">
            {reservation.vehicle?.brand} {reservation.vehicle?.model} - {reservation.vehicle?.license_plate}
          </p>
        </div>
        <span className={`px-3 py-1 rounded-full text-xs font-semibold ${status.color}`}>
          {status.label}
        </span>
      </div>

      <div className="space-y-2 mb-4 text-sm">
        <div className="flex justify-between">
          <span className="text-gray-600">Entrada:</span>
          <span className="text-gray-900">{formatDate(reservation.entry_time)}</span>
        </div>
        
        {reservation.status === 'active' && elapsedTime && (
          <div className="flex justify-between">
            <span className="text-gray-600">Tempo decorrido:</span>
            <span className="text-gray-900 font-semibold">{elapsedTime}</span>
          </div>
        )}
        
        {reservation.exit_time && (
          <div className="flex justify-between">
            <span className="text-gray-600">Saída:</span>
            <span className="text-gray-900">{formatDate(reservation.exit_time)}</span>
          </div>
        )}

        <div className="flex justify-between pt-2 border-t border-gray-200">
          <span className="font-semibold text-gray-700">
            {reservation.status === 'active' ? 'Valor atual:' : 'Total:'}
          </span>
          <span className={`font-bold ${reservation.status === 'active' ? 'text-orange-600' : 'text-primary-600'}`}>
            R$ {priceValue.toFixed(2)}
            {reservation.status === 'active' && (
              <span className="text-xs text-gray-500 ml-1">(em andamento)</span>
            )}
          </span>
        </div>
      </div>

      <div className="flex gap-2">
        {canCheckout && onCheckout && (
          <button
            onClick={() => onCheckout(reservation.id)}
            className="flex-1 px-4 py-2 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition-colors"
          >
            Finalizar
          </button>
        )}
        
        {canCancel && (
          <button
            onClick={() => onCancel(reservation.id)}
            className="flex-1 px-4 py-2 bg-red-100 text-red-700 rounded-xl font-semibold hover:bg-red-200 transition-colors"
          >
            Cancelar
          </button>
        )}
      </div>
    </div>
  );
}
