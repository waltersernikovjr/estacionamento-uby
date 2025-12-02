import { useState, useEffect } from 'react';
import { Toaster, toast } from 'react-hot-toast';
import { useAuthStore } from '../../application/stores/authStore';
import { authApi } from '../../infrastructure/api/authApi';
import { operatorApi } from '../../infrastructure/api/operatorApi';
import { ParkingSpotManagementCard } from '../components/operator/ParkingSpotManagementCard';
import { SpotFormModal } from '../components/operator/SpotFormModal';
import { FinishReservationModal } from '../components/operator/FinishReservationModal';
import { ReservationCard } from '../components/parking/ReservationCard';
import { OperatorChatPanel } from '../components/chat/OperatorChatPanel';
import type { Operator, ParkingSpot, Reservation } from '../../domain/types';

export function OperatorDashboard() {
  const { user, clearAuth } = useAuthStore();
  const operator = user as Operator;

  const [activeTab, setActiveTab] = useState<'spots' | 'reservations'>('spots');
  const [spots, setSpots] = useState<ParkingSpot[]>([]);
  const [reservations, setReservations] = useState<Reservation[]>([]);
  const [stats, setStats] = useState({
    totalSpots: 0,
    availableSpots: 0,
    activeReservations: 0,
    todayRevenue: 0,
  });
  
  const [isLoading, setIsLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isFinishModalOpen, setIsFinishModalOpen] = useState(false);
  const [editingSpot, setEditingSpot] = useState<ParkingSpot | null>(null);
  const [pendingSpotUpdate, setPendingSpotUpdate] = useState<{ spotId: number; newStatus: string } | null>(null);
  const [activeReservationToFinish, setActiveReservationToFinish] = useState<Reservation | null>(null);
  const [filterStatus, setFilterStatus] = useState<'all' | 'active' | 'completed' | 'cancelled'>('all');
  const [searchPlate, setSearchPlate] = useState('');

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setIsLoading(true);
      const [spotsData, reservationsData, statsData] = await Promise.all([
        operatorApi.getAllSpots(),
        operatorApi.getAllReservations(),
        operatorApi.getStats(),
      ]);
      
      setSpots(Array.isArray(spotsData) ? spotsData : []);
      setReservations(Array.isArray(reservationsData) ? reservationsData : []);
      setStats(typeof statsData === 'object' && 'totalSpots' in statsData ? statsData : {
        totalSpots: 0,
        availableSpots: 0,
        activeReservations: 0,
        todayRevenue: 0,
      });
    } catch (err) {
      console.error('Erro ao carregar dados:', err);
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

  const handleEditSpot = (spot: ParkingSpot) => {
    setEditingSpot(spot);
    setIsModalOpen(true);
  };

  const handleDeleteSpot = async (spotId: number) => {
    if (!confirm('Tem certeza que deseja excluir esta vaga?')) return;
    
    try {
      await operatorApi.deleteSpot(spotId);
      toast.success('✅ Vaga excluída com sucesso!');
      await loadData();
    } catch (err) {
      console.error('Erro ao excluir vaga:', err);
      toast.error('❌ Erro ao excluir vaga. Verifique se não há reservas ativas.');
    }
  };

  const handleToggleStatus = async (spotId: number, newStatus: string) => {
    try {
      
      const activeReservation = await operatorApi.getActiveReservationBySpot(spotId);
      
      if (activeReservation) {
        
        setPendingSpotUpdate({ spotId, newStatus });
        setActiveReservationToFinish(activeReservation);
        setIsFinishModalOpen(true);
      } else {
        
        await operatorApi.updateSpot(spotId, { status: newStatus as 'available' | 'occupied' | 'reserved' | 'maintenance' });
        toast.success('✅ Status atualizado com sucesso!');
        await loadData();
      }
    } catch (err) {
      console.error('Erro ao atualizar status:', err);
      toast.error('❌ Erro ao verificar reserva ativa. Tente novamente.');
    }
  };

  const handleConfirmFinishReservation = async (notes?: string) => {
    if (!activeReservationToFinish || !pendingSpotUpdate) return;

    try {
      
      await operatorApi.finalizeReservation(activeReservationToFinish.id, notes);
      
      
      await operatorApi.updateSpot(pendingSpotUpdate.spotId, { 
        status: pendingSpotUpdate.newStatus as 'available' | 'occupied' | 'reserved' | 'maintenance'
      });
      
      
      toast.success('✅ Reserva finalizada com sucesso!');
      await loadData();
      
      
      setIsFinishModalOpen(false);
      setActiveReservationToFinish(null);
      setPendingSpotUpdate(null);
    } catch (err) {
      console.error('Erro ao finalizar reserva:', err);
      toast.error('❌ Erro ao finalizar reserva. Tente novamente.');
    }
  };

  const handleSpotSubmit = async (spotData: any) => {
    try {
      if (editingSpot) {
        await operatorApi.updateSpot(editingSpot.id, spotData);
      } else {
        await operatorApi.createSpot(spotData);
      }
      setIsModalOpen(false);
      await loadData();
    } catch (err) {
      console.error('Erro ao salvar vaga:', err);
      throw err;
    }
  };

  const handleSearchPlate = async () => {
    if (!searchPlate.trim()) {
      await loadData();
      return;
    }

    try {
      const results = await operatorApi.searchByPlate(searchPlate);
      setReservations(results);
    } catch (err) {
      console.error('Erro ao buscar placa:', err);
    }
  };

  const filteredReservations = filterStatus === 'all' 
    ? reservations 
    : reservations.filter(r => r.status === filterStatus);

  if (isLoading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">Carregando...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">
                Dashboard do Operador
              </h1>
              <p className="text-sm text-gray-600">
                Olá, {operator.name}
              </p>
            </div>
            <button
              onClick={handleLogout}
              className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors"
            >
              Sair
            </button>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Stats Cards */}
        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
          <div className="card">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Total de Vagas</p>
                <p className="text-3xl font-bold text-gray-900 mt-1">{stats.totalSpots}</p>
              </div>
              <div className="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                <span className="text-2xl">🅿️</span>
              </div>
            </div>
          </div>

          <div className="card">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Vagas Disponíveis</p>
                <p className="text-3xl font-bold text-green-600 mt-1">{stats.availableSpots}</p>
              </div>
              <div className="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <span className="text-2xl">✅</span>
              </div>
            </div>
          </div>

          <div className="card">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Reservas Ativas</p>
                <p className="text-3xl font-bold text-blue-600 mt-1">{stats.activeReservations}</p>
              </div>
              <div className="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <span className="text-2xl">🚗</span>
              </div>
            </div>
          </div>

          <div className="card">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">Receita Hoje</p>
                <p className="text-2xl font-bold text-yellow-600 mt-1">
                  R$ {(stats.todayRevenue || 0).toFixed(2)}
                </p>
              </div>
              <div className="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <span className="text-2xl">💰</span>
              </div>
            </div>
          </div>
        </div>

        {/* Tabs */}
        <div className="mb-6 border-b border-gray-200">
          <nav className="-mb-px flex space-x-8">
            <button
              onClick={() => setActiveTab('spots')}
              className={`py-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                activeTab === 'spots'
                  ? 'border-primary-500 text-primary-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Gerenciar Vagas
            </button>
            <button
              onClick={() => setActiveTab('reservations')}
              className={`py-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                activeTab === 'reservations'
                  ? 'border-primary-500 text-primary-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Ver Reservas
            </button>
          </nav>
        </div>

        {/* Spots Tab */}
        {activeTab === 'spots' && (
          <div>
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-2xl font-bold text-gray-900">Vagas de Estacionamento</h2>
            </div>

            {spots.length === 0 ? (
              <div className="card text-center py-12">
                <p className="text-gray-500 mb-4">Nenhuma vaga cadastrada no sistema ainda.</p>
                <p className="text-sm text-gray-400">As vagas físicas do estacionamento são gerenciadas pelo sistema.</p>
              </div>
            ) : (
              <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {spots.map((spot) => (
                  <ParkingSpotManagementCard
                    key={spot.id}
                    spot={spot}
                    onEdit={handleEditSpot}
                    onDelete={handleDeleteSpot}
                    onToggleStatus={handleToggleStatus}
                  />
                ))}
              </div>
            )}
          </div>
        )}

        {/* Reservations Tab */}
        {activeTab === 'reservations' && (
          <div>
            <div className="mb-6">
              <h2 className="text-2xl font-bold text-gray-900 mb-4">Todas as Reservas</h2>
              
              {/* Filters */}
              <div className="card mb-4">
                <div className="grid gap-4 md:grid-cols-2">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Filtrar por Status
                    </label>
                    <select
                      value={filterStatus}
                      onChange={(e) => setFilterStatus(e.target.value as any)}
                      className="input"
                    >
                      <option value="all">Todos</option>
                      <option value="active">Ativos</option>
                      <option value="completed">Concluídos</option>
                      <option value="cancelled">Cancelados</option>
                    </select>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Buscar por Placa
                    </label>
                    <div className="flex gap-2">
                      <input
                        type="text"
                        value={searchPlate}
                        onChange={(e) => setSearchPlate(e.target.value)}
                        placeholder="Digite a placa..."
                        className="input flex-1"
                        onKeyPress={(e) => e.key === 'Enter' && handleSearchPlate()}
                      />
                      <button
                        onClick={handleSearchPlate}
                        className="btn btn-primary"
                      >
                        Buscar
                      </button>
                      {searchPlate && (
                        <button
                          onClick={() => {
                            setSearchPlate('');
                            loadData();
                          }}
                          className="btn bg-gray-200 text-gray-700 hover:bg-gray-300"
                        >
                          Limpar
                        </button>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {filteredReservations.length === 0 ? (
              <div className="card text-center py-12">
                <p className="text-gray-500">Nenhuma reserva encontrada.</p>
              </div>
            ) : (
              <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {filteredReservations.map((reservation) => (
                  <ReservationCard
                    key={reservation.id}
                    reservation={reservation}
                    onCancel={() => {}}
                    onCheckout={async () => {
                      toast('Checkout deve ser feito pelo cliente', {
                        icon: 'ℹ️',
                        duration: 3000,
                        style: {
                          background: '#DBEAFE',
                          color: '#1E40AF',
                          border: '2px solid #3B82F6',
                          fontWeight: '600',
                        },
                      });
                    }}
                  />
                ))}
              </div>
            )}
          </div>
        )}
      </main>

      {/* Modal de Edição de Vaga */}
      <SpotFormModal
        isOpen={isModalOpen}
        spot={editingSpot}
        onClose={() => setIsModalOpen(false)}
        onSubmit={handleSpotSubmit}
      />

      {/* Modal de Finalização de Reserva */}
      <FinishReservationModal
        isOpen={isFinishModalOpen}
        reservation={activeReservationToFinish}
        onClose={() => {
          setIsFinishModalOpen(false);
          setActiveReservationToFinish(null);
          setPendingSpotUpdate(null);
        }}
        onConfirm={handleConfirmFinishReservation}
      />

      {/* Toast Notifications */}
      <Toaster position="top-right" />

      {/* Operator Chat Panel */}
      {operator && (
        <OperatorChatPanel
          operatorId={operator.id}
          operatorName={operator.name}
          token={localStorage.getItem('auth_token') || ''}
        />
      )}
    </div>
  );
}
