import { io } from "socket.io-client";
import CreateVagas from "./application/CreateVaga";
import GetVagas from "./application/GetVagas";
import LoginCliente from "./application/LoginCliente";
import LoginOperador from "./application/LoginOperador";
import RegisterCliente from "./application/RegisterCliente";
import { RegisterOperador } from "./application/RegisterOperador";
import UpdateVaga from "./application/UpdateVaga";
import { DIProvider } from "./di/DIContext";
import { VagasStatus } from "./enum/VagaStatus";
import { Home } from "./feature/Home";
import { HttpClienteGateway, InmemoryClienteGateway } from "./gateway/ClienteGateway";
import { HttpOperadorGateway, InmemoryOperadorGateway } from "./gateway/OperadorGateway";
import { InmemoryVagaGateway } from "./gateway/VagaGateway";
import SocketClient from "./util/SocketClientUtil";

function App() {
  const container = new Map<string, any>();

  const clienteGateway = new InmemoryClienteGateway();
  const operadorGateway = new InmemoryOperadorGateway();
  const vagaGateway = new InmemoryVagaGateway([
    {
      id: 1,
      numeroDaVaga: 1,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 2,
      numeroDaVaga: 2,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 3,
      numeroDaVaga: 3,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 4,
      numeroDaVaga: 4,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.OCUPADA
    },
    {
      id: 5,
      numeroDaVaga: 5,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 6,
      numeroDaVaga: 6,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 7,
      numeroDaVaga: 7,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 8,
      numeroDaVaga: 8,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 9,
      numeroDaVaga: 9,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 10,
      numeroDaVaga: 10,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 11,
      numeroDaVaga: 11,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 12,
      numeroDaVaga: 12,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 13,
      numeroDaVaga: 13,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 14,
      numeroDaVaga: 14,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
    {
      id: 15,
      numeroDaVaga: 15,
      preco: 10,
      dimensao: "10x2",
      status: VagasStatus.LIVRE
    },
  ]);

  const httpOperadorGateway = new HttpOperadorGateway();
  const httpClienteGateway = new HttpClienteGateway();

  container.set('registerOperador', new RegisterOperador(httpOperadorGateway));
  container.set('getVagas', new GetVagas(vagaGateway));
  container.set('createVaga', new CreateVagas(vagaGateway));
  container.set('updateVaga', new UpdateVaga(vagaGateway));
  container.set('registerCliente', new RegisterCliente(httpClienteGateway));

  container.set('loginCliente', new LoginCliente(httpClienteGateway));
  container.set('loginOperador', new LoginOperador(httpOperadorGateway));
  container.set('socketClient', new SocketClient(io('ws://localhost:3000')))

  return (
    <DIProvider container={container}>
      <Home />
    </DIProvider >
  )
}

export default App
