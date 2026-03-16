# -------------------------------------------------------------------
# Stage 1: Build the React SPA
# -------------------------------------------------------------------
FROM node:20-alpine AS builder

WORKDIR /app

ARG VITE_API_URL
ENV VITE_API_URL=${VITE_API_URL}

COPY admin/package.json admin/package-lock.json ./

RUN npm ci

COPY admin/ .

RUN npx vite build

# -------------------------------------------------------------------
# Stage 2: Serve with nginx
# -------------------------------------------------------------------
FROM nginx:1.27-alpine AS production

RUN rm /etc/nginx/conf.d/default.conf

COPY .docker/images/admin/nginx.conf /etc/nginx/conf.d/default.conf

COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
