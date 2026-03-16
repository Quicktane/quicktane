# -------------------------------------------------------------------
# Stage 1: Build the React SPA
# -------------------------------------------------------------------
FROM node:20-alpine AS builder

WORKDIR /app

ARG VITE_API_URL
ENV VITE_API_URL=${VITE_API_URL}

COPY storefront/package.json storefront/package-lock.json ./

RUN npm ci

COPY storefront/ .

RUN npx vite build

# -------------------------------------------------------------------
# Stage 2: Serve with nginx
# -------------------------------------------------------------------
FROM nginx:1.27-alpine AS production

RUN rm /etc/nginx/conf.d/default.conf

COPY .docker/images/storefront/nginx.conf /etc/nginx/conf.d/default.conf

COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
