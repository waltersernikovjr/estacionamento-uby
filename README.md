# Desafio Full Stack - Estacionamento da Uby

A Uby está vendo uma oportunidade de implementar um estacionamento digital em Goimylandia, e para isso precisa de um sistema web e back-end para controle das vagas e expiração do estacionamento.

A equipe de análise colheu as informações de como a diretoria espera que o sistema funcione e encaminhou à equipe de desenvolvimento para começar o desenvolvimento da aplicação.

## Resumo da análise feita pelo Jefferson, um dos analistas envolvidos no projeto

Esta aplicação deverá representar um estacionamento digital. Ele envolverá inicialmente um operador do estacionamento e clientes para estacionar.

### Cadastro do Operador

O cadastro do operador do estacionamento deve conter:

* Nome
* CPF
* Email

O operador deverá poder cadastrar as vagas disponíveis e suas especificações, como:

* Número da vaga
* Preço
* Dimensões da vaga

### Cadastro do Cliente

Ao chegar um novo cliente, deverá ser possível realizar um cadastro que deve conter:

* Nome
* CPF
* RG
* Endereço
* Dados do carro:

  * Placa
  * Modelo
  * Cor
  * Ano

Após o login e confirmação via email do cadastro, deverão ser apresentadas as vagas disponíveis para ele.

Caso não existam vagas disponíveis, o cliente poderá entrar em contato com o operador via **chat**, para saber quando será liberada uma vaga.

Ao voltar para buscar o veículo, o cliente deverá visualizar o preço final do pagamento.

---

## Requisitos para o Desenvolvimento

### Docker

**Conteinerização:**

* Criar um Dockerfile para o backend (Laravel), outro para o frontend (React) e outro para o serviço de chat (Node).
* Utilizar **docker-compose** para orquestrar os containers (backend, frontend e banco de dados).

### Banco de Dados

* Utilizar **MySQL** como banco de dados principal.
* Garantir que o banco esteja rodando em um container.

---

## Backend (API)

Requisitos:

* PHP com **Laravel**
* Confirmação de cadastro via email

**Pontos adicionais:**

* Busca em API externa para validar CEP e auto preenchimento
* Sistema de cache para otimizar buscas

---

## Front-end

* JavaScript com **React**
* Autenticação com **JWT**

**Ponto adicional:**

* Login com Google

---

## Chat

* Implementado com **WebSocket**
* Nova API desenvolvida em **Node.js**

---

## Critérios de Avaliação

* Correto funcionamento dos endpoints
* Correto funcionamento do front-end
* Tratamento de erros
* Implementação de padrões de projeto (Design Patterns, SOLID, etc.)
* Documentação dos endpoints
* Código limpo e organizado
* Modelagem do banco de dados

---

## Entrega

A prova poderá ser entregue até: **27/11/2025 às 23:59:59**

### Como entregar a prova

Antes de começar o desenvolvimento:

1. Faça um **fork** do repositório do avaliador.
2. Faça um **clone** do repositório forkeado no seu ambiente de desenvolvimento.
3. Após terminar o desenvolvimento, **submeta sua prova** ao repositório forkeado.
4. **Abra uma Pull Request** solicitando a inclusão do seu código ao repositório do avaliador.

**Resumo:**

* Fork
* Clone
* Desenvolvimento
* Push para o Fork
* Pull Request para o repositório do avaliador

Seguindo estes passos não tem como errar, mas caso algo aconteça, contacte o seu avaliador!

---

**Boa sorte! Aguardamos pela sua prova 😄**
