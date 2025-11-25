import { useState, useEffect } from 'react';
import { useAuthStore } from '../../application/stores/authStore';
import { authApi } from '../../infrastructure/api/authApi';
import { parkingApi } from '../../infrastructure/api/parkingApi';
import { vehicleApi } from '../../infrastructure/api/vehicleApi';
import { ParkingSpotCard } from '../components/parking/ParkingSpotCard';
import { ReservationCard } from '../components/parking/ReservationCard';
import { VehicleFormModal } from '../components/vehicles/VehicleFormModal';
import { VehicleSelectionModal } from '../components/parking/VehicleSelectionModal';
import type { ParkingSpot, Reservation, Vehicle, Customer } from '../../domain/types';

export function CustomerDashboard() {
  const { user, clearAuth } = useAuthStore();
  const customer = user as Customer;
  
  const [availableSpots, setAvailableSpots] = useState<ParkingSpot[]>([]);
  const [myReservations, setMyReservations] = useState<Reservation[]>([]);
  const [myVehicles, setMyVehicles] = useState<Vehicle[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isReserving, setIsReserving] = useState(false);
  const [error, setError] = useState('');
  const [activeTab, setActiveTab] = useState<'spots' | 'reservations' | 'vehicles'>('spots');
  const [isVehicleModalOpen, setIsVehicleModalOpen] = useState(false);
  const [isVehicleSelectionOpen, setIsVehicleSelectionOpen] = useState(false);
  const [selectedSpot, setSelectedSpot] = useState<ParkingSpot | null>(null);
  const [editingVehicle, setEditingVehicle] = useState<Vehicle | null>(null);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setIsLoading(true);
      setError('');
      
      const [spots, reservations, vehicles] = await Promise.all([
        parkingApi.getAvailableSpots().catch(() => []),
        parkingApi.getMyReservations().catch(() => []),
        vehicleApi.getMyVehicles().catch(() => []),
      ]);
      
      setAvailableSpots(spots);
      setMyReservations(reservations);
      setMyVehicles(vehicles);
    } catch (err) {
      console.error('Erro ao carregar dados:', err);
      setError('Erro ao carregar dados. Tente novamente.');
    } finally {
      setIsLoading(false);
    }
  };

  const handleLogout = async () => {
    try {
      await authApi.logout();
    } catch (err) {
      console.error('Erro ao fazer logout:', err);
    } finally {
      clearAuth();
      window.location.href = '/login';
    }
  };

  const handleReserve = async (spotId: number) => {
    if (myVehicles.length === 0) {
      alert('⚠️ Você precisa cadastrar um veículo primeiro!');
      setActiveTab('vehicles');
      return;
    }

    const spot = availableSpots.find(s => s.id === spotId);
    if (spot) {
      setSelectedSpot(spot);
      setIsVehicleSelectionOpen(true);
    }
  };

  const handleVehicleSelected = async (vehicleId: number) => {
    if (!selectedSpot) return;

    try {
      setIsReserving(true);
      setIsVehicleSelectionOpen(false);
      
      await parkingApi.createReservation({
        parking_spot_id: selectedSpot.id,
        vehicle_id: vehicleId,
      });
      
      alert('✅ Reserva criada com sucesso!');
      setSelectedSpot(null);
      loadData();
    } catch (err: any) {
      console.error('Erro ao criar reserva:', err);
      const errorMsg = err?.response?.data?.message || err?.message || 'Tente novamente';
      alert('❌ Erro ao criar reserva: ' + errorMsg);
    } finally {
      setIsReserving(false);
    }
  };

  const handleCancelReservation = async (id: number) => {
    if (!confirm('Deseja realmente cancelar esta reserva?')) return;

    try {
      await parkingApi.cancelReservation(id);
      alert('✅ Reserva cancelada!');
      loadData();
    } catch (err) {
      console.error('Erro ao cancelar:', err);
      alert('❌ Erro ao cancelar reserva');
    }
  };

  const handleCheckout = async (id: number) => {
    if (!confirm('Finalizar esta reserva e fazer checkout?')) return;

    try {
      await parkingApi.checkoutReservation(id);
      alert('✅ Checkout realizado com sucesso!');
      loadData();
    } catch (err) {
      console.error('Erro ao fazer checkout:', err);
      alert('❌ Erro ao fazer checkout');
    }
  };

  const handleAddVehicle = async (data: {
    license_plate: string;
    brand: string;
    model: string;
    color: string;
    vehicle_type: 'car' | 'motorcycle' | 'truck';
  }) => {
    try {
      if (editingVehicle) {
        await vehicleApi.updateVehicle(editingVehicle.id, data);
        alert('✅ Veículo atualizado com sucesso!');
      } else {
        await parkingApi.addVehicle(data);
        alert('✅ Veículo cadastrado com sucesso!');
      }
      setEditingVehicle(null);
      loadData();
    } catch (err: any) {
      console.error('Erro ao salvar veículo:', err);
      throw err;
    }
  };

  const handleEditVehicle = (vehicle: Vehicle) => {
    setEditingVehicle(vehicle);
    setIsVehicleModalOpen(true);
  };

  const handleCloseVehicleModal = () => {
    setIsVehicleModalOpen(false);
    setEditingVehicle(null);
  };

  const handleDeleteVehicle = async (id: number) => {
    if (!confirm('Deseja realmente excluir este veículo?')) return;

    try {
      await parkingApi.removeVehicle(id);
      alert('✅ Veículo excluído com sucesso!');
      loadData();
    } catch (err: any) {
      console.error('Erro ao excluir veículo:', err);
      
      const errorMessage = err?.response?.data?.message;
      
      if (errorMessage) {
        alert(`❌ ${errorMessage}`);
      } else if (err?.response?.status === 500) {
        alert('❌ Não foi possível excluir o veículo. Verifique se ele possui reservas associadas.');
      } else {
        alert('❌ Erro ao excluir veículo');
      }
    }
  };

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <div className="animate-spin rounded-full h-16 w-16 border-b-4 border-primary-600 mx-auto mb-4"></div>
          <p className="text-gray-600 font-medium">Carregando dados...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b-2 border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-3xl font-bold text-primary-600">🅿️ Estacionamento Uby</h1>
              <p className="text-sm text-gray-600 mt-1">
                Olá, <span className="font-semibold">{customer?.name}</span>!
              </p>
            </div>
            <button
              onClick={handleLogout}
              className="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
            >
              Sair →
            </button>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {error && (
          <div className="bg-red-50 border-2 border-red-300 text-red-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <span>⚠️</span>
            <span>{error}</span>
            <button
              onClick={loadData}
              className="ml-auto text-sm font-semibold underline hover:no-underline"
            >
              Tentar novamente
            </button>
          </div>
        )}

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="card bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200">
            <h3 className="text-sm font-semibold text-blue-700 mb-1">Vagas Disponíveis</h3>
            <p className="text-3xl font-bold text-blue-900">{availableSpots.length}</p>
          </div>
          
          <div className="card bg-gradient-to-br from-green-50 to-green-100 border-green-200">
            <h3 className="text-sm font-semibold text-green-700 mb-1">Minhas Reservas</h3>
            <p className="text-3xl font-bold text-green-900">
              {myReservations.filter(r => r.status === 'active').length}
            </p>
          </div>
          
          <div className="card bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200">
            <h3 className="text-sm font-semibold text-purple-700 mb-1">Meus Veículos</h3>
            <p className="text-3xl font-bold text-purple-900">{myVehicles.length}</p>
          </div>
        </div>

        {/* Tabs */}
        <div className="mb-6">
          <div className="border-b-2 border-gray-200">
            <nav className="-mb-px flex gap-6">
              <button
                onClick={() => setActiveTab('spots')}
                className={`pb-4 px-2 border-b-2 font-semibold text-sm transition-colors ${
                  activeTab === 'spots'
                    ? 'border-primary-600 text-primary-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                }`}
              >
                🅿️ Vagas Disponíveis
              </button>
              
              <button
                onClick={() => setActiveTab('reservations')}
                className={`pb-4 px-2 border-b-2 font-semibold text-sm transition-colors ${
                  activeTab === 'reservations'
                    ? 'border-primary-600 text-primary-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                }`}
              >
                📋 Minhas Reservas
              </button>
              
              <button
                onClick={() => setActiveTab('vehicles')}
                className={`pb-4 px-2 border-b-2 font-semibold text-sm transition-colors ${
                  activeTab === 'vehicles'
                    ? 'border-primary-600 text-primary-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                }`}
              >
                🚗 Meus Veículos
              </button>
            </nav>
          </div>
        </div>

        {/* Tab Content */}
        {activeTab === 'spots' && (
          <section>
            {availableSpots.length === 0 ? (
              <div className="card text-center py-12">
                <p className="text-gray-500 text-lg">
                  😔 Nenhuma vaga disponível no momento
                </p>
              </div>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {availableSpots.map((spot) => (
                  <ParkingSpotCard
                    key={spot.id}
                    spot={spot}
                    onReserve={handleReserve}
                    isReserving={isReserving}
                    userVehicles={myVehicles}
                  />
                ))}
              </div>
            )}
          </section>
        )}

        {activeTab === 'reservations' && (
          <section>
            {myReservations.length === 0 ? (
              <div className="card text-center py-12">
                <p className="text-gray-500 text-lg mb-4">
                  📋 Você ainda não tem reservas
                </p>
                <button
                  onClick={() => setActiveTab('spots')}
                  className="btn-primary"
                >
                  Ver vagas disponíveis
                </button>
              </div>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {myReservations.map((reservation) => (
                  <ReservationCard
                    key={reservation.id}
                    reservation={reservation}
                    onCancel={handleCancelReservation}
                    onCheckout={handleCheckout}
                  />
                ))}
              </div>
            )}
          </section>
        )}

        {activeTab === 'vehicles' && (
          <section>
            <div className="mb-6 flex justify-between items-center">
              <h2 className="text-xl font-bold text-gray-900">Meus Veículos</h2>
              <button
                onClick={() => setIsVehicleModalOpen(true)}
                className="btn-primary"
              >
                + Cadastrar Veículo
              </button>
            </div>

            {myVehicles.length === 0 ? (
              <div className="card text-center py-12">
                <p className="text-gray-500 text-lg mb-4">
                  🚗 Você ainda não cadastrou nenhum veículo
                </p>
                <button
                  className="btn-primary"
                  onClick={() => setIsVehicleModalOpen(true)}
                >
                  Cadastrar veículo
                </button>
              </div>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {myVehicles.map((vehicle) => (
                  <div key={vehicle.id} className="card">
                    <div className="flex justify-between items-start mb-3">
                      <h3 className="text-lg font-bold text-gray-900">
                        {vehicle.brand} {vehicle.model}
                      </h3>
                      <span className="text-2xl">
                        {vehicle.vehicle_type === 'car' && '🚗'}
                        {vehicle.vehicle_type === 'motorcycle' && '🏍️'}
                        {vehicle.vehicle_type === 'truck' && '🚚'}
                      </span>
                    </div>
                    <div className="space-y-1 text-sm mb-4">
                      <p className="text-gray-600">
                        <span className="font-semibold">Placa:</span> {vehicle.license_plate}
                      </p>
                      <p className="text-gray-600">
                        <span className="font-semibold">Cor:</span> {vehicle.color}
                      </p>
                      <p className="text-gray-600">
                        <span className="font-semibold">Tipo:</span> {
                          vehicle.vehicle_type === 'car' ? 'Carro' :
                          vehicle.vehicle_type === 'motorcycle' ? 'Moto' : 'Caminhão'
                        }
                      </p>
                    </div>
                    <div className="flex gap-2">
                      <button
                        onClick={() => handleEditVehicle(vehicle)}
                        className="flex-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-xl text-sm font-semibold hover:bg-blue-200 transition-colors"
                      >
                        Editar
                      </button>
                      <button
                        onClick={() => handleDeleteVehicle(vehicle.id)}
                        className="flex-1 px-3 py-2 bg-red-100 text-red-700 rounded-xl text-sm font-semibold hover:bg-red-200 transition-colors"
                      >
                        Excluir
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </section>
        )}
      </main>

      {/* Modal de Cadastro de Veículo */}
      <VehicleFormModal
        isOpen={isVehicleModalOpen}
        onClose={handleCloseVehicleModal}
        onSubmit={handleAddVehicle}
        editingVehicle={editingVehicle}
      />

      {/* Modal de Seleção de Veículo */}
      <VehicleSelectionModal
        isOpen={isVehicleSelectionOpen}
        onClose={() => {
          setIsVehicleSelectionOpen(false);
          setSelectedSpot(null);
        }}
        vehicles={myVehicles}
        reservedVehicleIds={myReservations
          .filter(r => r.status === 'active')
          .map(r => r.vehicle_id)
        }
        onSelect={handleVehicleSelected}
        spot={selectedSpot}
      />
    </div>
  );
}
