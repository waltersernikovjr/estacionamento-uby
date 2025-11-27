FROM node:18-alpine

WORKDIR /app

# Instalar netcat para healthcheck
RUN apk add --no-cache netcat-openbsd

COPY package*.json ./

RUN npm install --production

COPY . .

# Copiar entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 3001

USER node

# Usar entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["npm", "start"]
