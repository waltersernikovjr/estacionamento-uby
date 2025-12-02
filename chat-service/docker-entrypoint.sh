#!/bin/sh

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "${GREEN}🚀 Iniciando Chat Service...${NC}"

# Verificar e instalar dependências do npm
if [ ! -d "node_modules" ] || [ ! -f "node_modules/.package-lock.json" ]; then
    echo "${YELLOW}📦 Pasta node_modules não encontrada. Instalando dependências do npm...${NC}"
    npm ci --only=production
    echo "${GREEN}✅ Dependências instaladas com sucesso!${NC}"
else
    echo "${GREEN}✅ Dependências do npm já instaladas.${NC}"
fi

# Copiar .env.example para .env se não existir
if [ ! -f .env ]; then
    echo "${YELLOW}📝 Arquivo .env não encontrado. Copiando de .env.example...${NC}"
    cp .env.example .env
    echo "${GREEN}✅ Arquivo .env criado com sucesso!${NC}"
else
    echo "${GREEN}✅ Arquivo .env já existe.${NC}"
fi

# Aguardar MySQL estar pronto
echo "${YELLOW}⏳ Aguardando MySQL estar pronto...${NC}"
until nc -z ${DB_HOST:-mysql} ${DB_PORT:-3306}; do
    sleep 2
done
echo "${GREEN}✅ MySQL está pronto!${NC}"

echo "${GREEN}✨ Chat Service iniciado com sucesso!${NC}"
echo ""

# Executar comando passado
exec "$@"
