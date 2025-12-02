import { useState, useEffect, useRef } from 'react';
import { io, Socket } from 'socket.io-client';
import { toast } from 'react-hot-toast';

interface Message {
  id: string;
  senderId: number;
  senderName: string;
  senderType: 'customer' | 'operator';
  message: string;
  timestamp: string;
  recipientId?: number;
  customerId?: number;
}

interface Conversation {
  customerId: number;
  customerName: string;
  lastMessage: string;
  lastMessageTime: string;
  unreadCount: number;
  messages: Message[];
}

interface OperatorChatPanelProps {
  operatorId: number;
  operatorName: string;
  token: string;
}

export function OperatorChatPanel({ token }: OperatorChatPanelProps) {
  const [socket, setSocket] = useState<Socket | null>(null);
  const [conversations, setConversations] = useState<Map<number, Conversation>>(new Map());
  const [selectedCustomerId, setSelectedCustomerId] = useState<number | null>(null);
  const [newMessage, setNewMessage] = useState('');
  const [isConnected, setIsConnected] = useState(false);
  const [isOpen, setIsOpen] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    const newSocket = io('http://localhost:3001', {
      auth: { token },
      transports: ['websocket', 'polling'],
    });

    newSocket.on('connect', () => {
      setIsConnected(true);
      toast.success('Chat conectado');
      newSocket.emit('loadHistory', {});
    });

    newSocket.on('connect_error', () => {
      setIsConnected(false);
      toast.error('Erro ao conectar chat');
    });

    newSocket.on('disconnect', () => {
      setIsConnected(false);
    });

    newSocket.on('message', (message: Message) => {
      let customerId: number;
      if (message.senderType === 'customer') {
        customerId = message.senderId;
      } else if (message.senderType === 'operator') {
        customerId = message.recipientId ?? 0;
      } else {
        return;
      }
      
      if (!customerId || customerId === 0) {
        return;
      }

      setConversations(prev => {
        const newConversations = new Map(prev);
        const existing = newConversations.get(customerId);
        
        if (existing) {
          const messageExists = existing.messages.some(m => 
            m.timestamp === message.timestamp && m.message === message.message
          );
          
          if (!messageExists) {
            existing.messages.push(message);
            existing.lastMessage = message.message;
            existing.lastMessageTime = message.timestamp;
            if (message.senderType === 'customer' && selectedCustomerId !== customerId) {
              existing.unreadCount++;
            }
          }
        } else {
          newConversations.set(customerId, {
            customerId,
            customerName: message.senderType === 'customer' ? message.senderName : 'Cliente',
            lastMessage: message.message,
            lastMessageTime: message.timestamp,
            unreadCount: selectedCustomerId === customerId ? 0 : 1,
            messages: [message],
          });
        }
        
        return newConversations;
      });

      if (message.senderType === 'customer' && selectedCustomerId !== customerId) {
        toast(`Nova mensagem de ${message.senderName}`, {
          icon: '💬',
          duration: 3000,
        });
      }
    });

    newSocket.on('error', (error: any) => {
      toast.error(error.message || 'Erro no chat');
    });

    newSocket.on('messageHistory', (messages: Message[]) => {
      if (messages.length === 0) {
        return;
      }

      setConversations(prev => {
        const newConversations = new Map(prev);
        
        messages.forEach(msg => {
          const customerId = msg.customerId ?? 
            (msg.senderType === 'customer' ? msg.senderId : msg.recipientId ?? 0);
          
          if (!customerId || customerId === 0) {
            return;
          }
          
          let conversation = newConversations.get(customerId);
          
          if (!conversation) {
            conversation = {
              customerId,
              customerName: msg.senderType === 'customer' ? msg.senderName : 'Cliente',
              lastMessage: msg.message,
              lastMessageTime: msg.timestamp,
              unreadCount: 0,
              messages: [],
            };
            newConversations.set(customerId, conversation);
          }
          
          const messageExists = conversation.messages.some(m => 
            m.timestamp === msg.timestamp && m.message === msg.message
          );
          
          if (!messageExists) {
            conversation.messages.push(msg);
          }
        });
        
        newConversations.forEach(conv => {
          if (conv.messages.length > 0) {
            const lastMsg = conv.messages[conv.messages.length - 1];
            conv.lastMessage = lastMsg.message;
            conv.lastMessageTime = lastMsg.timestamp;
          }
        });
        
        return newConversations;
      });
    });

    setSocket(newSocket);

    return () => {
      newSocket.close();
    };
  }, [token]);

  useEffect(() => {
    scrollToBottom();
  }, [conversations, selectedCustomerId]);

  const handleSendMessage = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!newMessage.trim() || !socket || !isConnected || !selectedCustomerId) {
      toast.error('Selecione um cliente para enviar mensagem');
      return;
    }

    const selectedConversation = conversations.get(selectedCustomerId);
    if (!selectedConversation) return;

    const messageData = {
      recipientId: selectedCustomerId,
      recipientType: 'customer',
      message: newMessage.trim(),
    };

    socket.emit('sendMessage', messageData);
    setNewMessage('');
  };

  const selectConversation = (customerId: number) => {
    setSelectedCustomerId(customerId);
    
    if (socket && isConnected) {
      socket.emit('loadHistory', { customerId });
    }
    
    setConversations(prev => {
      const newConversations = new Map(prev);
      const conversation = newConversations.get(customerId);
      if (conversation) {
        conversation.unreadCount = 0;
      }
      return newConversations;
    });
  };

  const selectedConversation = selectedCustomerId ? conversations.get(selectedCustomerId) : null;
  const totalUnread = Array.from(conversations.values()).reduce((sum, conv) => sum + conv.unreadCount, 0);

  if (!isOpen) {
    return (
      <button
        onClick={() => setIsOpen(true)}
        className="fixed bottom-6 right-6 bg-orange-500 hover:bg-orange-600 text-white rounded-full p-4 shadow-lg transition-all z-50"
      >
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        {totalUnread > 0 && (
          <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold">
            {totalUnread}
          </span>
        )}
      </button>
    );
  }

  return (
    <div className="fixed bottom-6 right-6 w-[700px] bg-white rounded-lg shadow-2xl z-50 flex" style={{ height: '600px' }}>
      {/* Sidebar - Lista de Conversas */}
      <div className="w-64 border-r border-gray-200 flex flex-col">
        <div className="bg-orange-500 text-white p-4 rounded-tl-lg flex items-center justify-between">
          <div className="flex items-center gap-2">
            <div className={`w-3 h-3 rounded-full ${isConnected ? 'bg-green-400' : 'bg-red-400'}`} />
            <h3 className="font-semibold">Conversas</h3>
          </div>
          <button
            onClick={() => setIsOpen(false)}
            className="text-white hover:text-gray-200 transition-colors"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div className="flex-1 overflow-y-auto">
          {conversations.size === 0 ? (
            <div className="p-4 text-center text-gray-500 text-sm">
              <p>Nenhuma conversa ainda</p>
              <p className="mt-2 text-xs">Aguardando mensagens...</p>
            </div>
          ) : (
            Array.from(conversations.values()).map((conv) => (
              <button
                key={conv.customerId}
                onClick={() => selectConversation(conv.customerId)}
                className={`w-full p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors text-left ${
                  selectedCustomerId === conv.customerId ? 'bg-orange-50' : ''
                }`}
              >
                <div className="flex items-start justify-between">
                  <div className="flex-1 min-w-0">
                    <p className="font-semibold text-sm text-gray-900 truncate">
                      {conv.customerName}
                    </p>
                    <p className="text-xs text-gray-600 truncate mt-1">
                      {conv.lastMessage}
                    </p>
                    <p className="text-xs text-gray-400 mt-1">
                      {new Date(conv.lastMessageTime).toLocaleTimeString('pt-BR', {
                        hour: '2-digit',
                        minute: '2-digit',
                      })}
                    </p>
                  </div>
                  {conv.unreadCount > 0 && (
                    <span className="ml-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                      {conv.unreadCount}
                    </span>
                  )}
                </div>
              </button>
            ))
          )}
        </div>
      </div>

      {/* Área de Chat */}
      <div className="flex-1 flex flex-col">
        {selectedConversation ? (
          <>
            <div className="bg-gray-100 p-4 border-b border-gray-200 rounded-tr-lg">
              <h4 className="font-semibold text-gray-900">{selectedConversation.customerName}</h4>
              <p className="text-xs text-gray-600">Cliente ID: {selectedConversation.customerId}</p>
            </div>

            <div className="flex-1 overflow-y-auto p-4 bg-gray-50 space-y-3">
              {selectedConversation.messages.map((msg, idx) => {
                const isOwn = msg.senderType === 'operator';
                return (
                  <div
                    key={msg.id || idx}
                    className={`flex ${isOwn ? 'justify-end' : 'justify-start'}`}
                  >
                    <div
                      className={`max-w-[70%] rounded-lg p-3 ${
                        isOwn
                          ? 'bg-orange-500 text-white'
                          : 'bg-white text-gray-800 border border-gray-200'
                      }`}
                    >
                      <p className="text-xs font-semibold mb-1 opacity-80">
                        {isOwn ? 'Você' : msg.senderName}
                      </p>
                      <p className="text-sm break-words">{msg.message}</p>
                      <p className="text-xs mt-1 opacity-70">
                        {new Date(msg.timestamp).toLocaleTimeString('pt-BR', {
                          hour: '2-digit',
                          minute: '2-digit',
                        })}
                      </p>
                    </div>
                  </div>
                );
              })}
              <div ref={messagesEndRef} />
            </div>

            <form onSubmit={handleSendMessage} className="p-4 border-t border-gray-200 rounded-br-lg">
              <div className="flex gap-2">
                <input
                  type="text"
                  value={newMessage}
                  onChange={(e) => setNewMessage(e.target.value)}
                  placeholder={`Responder para ${selectedConversation.customerName}...`}
                  disabled={!isConnected}
                  className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                />
                <button
                  type="submit"
                  disabled={!newMessage.trim() || !isConnected}
                  className="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                  </svg>
                </button>
              </div>
              {!isConnected && (
                <p className="text-xs text-red-500 mt-2">Desconectado. Tentando reconectar...</p>
              )}
            </form>
          </>
        ) : (
          <div className="flex-1 flex items-center justify-center text-gray-500">
            <div className="text-center">
              <svg className="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              <p className="text-sm">Selecione uma conversa</p>
              <p className="text-xs mt-2">Escolha um cliente na lista à esquerda</p>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
