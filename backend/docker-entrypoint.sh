#!/bin/sh

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "${GREEN}🚀 Iniciando backend Laravel...${NC}"

# Verificar e instalar dependências do Composer
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "${YELLOW}📦 Pasta vendor não encontrada. Instalando dependências do Composer...${NC}"
    composer install --no-interaction --optimize-autoloader --no-dev
    echo "${GREEN}✅ Dependências instaladas com sucesso!${NC}"
else
    echo "${GREEN}✅ Dependências do Composer já instaladas.${NC}"
fi

# Copiar .env.example para .env se não existir
if [ ! -f .env ]; then
    echo "${YELLOW}📝 Arquivo .env não encontrado. Copiando de .env.example...${NC}"
    cp .env.example .env
    echo "${GREEN}✅ Arquivo .env criado com sucesso!${NC}"
else
    echo "${GREEN}✅ Arquivo .env já existe.${NC}"
fi

# Gerar chave da aplicação se não existir
if ! grep -q "APP_KEY=base64:" .env; then
    echo "${YELLOW}🔑 Gerando chave da aplicação...${NC}"
    php artisan key:generate --ansi
    echo "${GREEN}✅ Chave gerada com sucesso!${NC}"
fi

# Aguardar MySQL estar pronto
echo "${YELLOW}⏳ Aguardando MySQL estar pronto...${NC}"
until php artisan db:show 2>/dev/null; do
    sleep 2
done
echo "${GREEN}✅ MySQL está pronto!${NC}"

# Executar migrations
echo "${YELLOW}🗄️  Executando migrations...${NC}"
php artisan migrate --force
echo "${GREEN}✅ Migrations executadas!${NC}"

# Executar seeders (apenas se não houver usuários cadastrados)
USER_COUNT=$(php artisan tinker --execute="echo \App\Infrastructure\Persistence\Models\User::count();" 2>/dev/null | tail -1)
if [ -z "$USER_COUNT" ] || [ "$USER_COUNT" -eq 0 ]; then
    echo "${YELLOW}🌱 Executando seeders...${NC}"
    php artisan db:seed --force
    echo "${GREEN}✅ Seeders executados!${NC}"
else
    echo "${GREEN}✅ Database já possui dados ($USER_COUNT usuários).${NC}"
fi

# Limpar e otimizar cache
echo "${YELLOW}🧹 Limpando cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "${GREEN}✅ Cache limpo!${NC}"

# Criar storage link se não existir
if [ ! -L public/storage ]; then
    echo "${YELLOW}🔗 Criando storage link...${NC}"
    php artisan storage:link
    echo "${GREEN}✅ Storage link criado!${NC}"
fi

echo "${GREEN}✨ Backend iniciado com sucesso!${NC}"
echo ""

# Executar comando passado ou php-fpm
exec "$@"
