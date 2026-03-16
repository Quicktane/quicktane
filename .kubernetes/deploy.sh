#!/usr/bin/env bash
#
# Manual deployment script for Quicktane to Kubernetes
#
# Usage:
#   ./deploy.sh [IMAGE_TAG]
#
# Prerequisites:
#   - kubectl configured with cluster access
#   - Docker logged in to ghcr.io (echo $PAT | docker login ghcr.io -u USERNAME --password-stdin)
#   - Update .kubernetes/production/secrets.yaml with real values before first deploy
#
# Example:
#   ./deploy.sh v1.0.0
#   ./deploy.sh latest
#

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
TAG="${1:-latest}"
REGISTRY="ghcr.io/quicktane/quicktane"
NAMESPACE="quicktane"
API_URL="https://api.demo.quicktane.com"

echo "============================================"
echo "  Quicktane Kubernetes Deployment"
echo "  Tag: $TAG"
echo "============================================"
echo ""

# ------------------------------------------------------------------
# Step 1: Build & push PHP base image (if changed)
# ------------------------------------------------------------------
echo ">>> Building PHP base image..."
docker build \
  -f "$PROJECT_ROOT/.docker/images/php-base/Dockerfile" \
  -t "$REGISTRY/php-base:latest" \
  "$PROJECT_ROOT/.docker/images/php-base"

docker push "$REGISTRY/php-base:latest"
echo "  PHP base image pushed."
echo ""

# ------------------------------------------------------------------
# Step 2: Build Docker images
# ------------------------------------------------------------------
echo ">>> Building application images..."

echo "  [1/3] Building API image..."
docker build \
  -f "$PROJECT_ROOT/.docker/images/api/Dockerfile" \
  --build-arg "PHP_BASE_IMAGE=$REGISTRY/php-base:latest" \
  -t "$REGISTRY/api:$TAG" \
  "$PROJECT_ROOT"

echo "  [2/3] Building Storefront image..."
docker build \
  -f "$PROJECT_ROOT/.docker/images/storefront/Dockerfile" \
  --build-arg "VITE_API_URL=$API_URL" \
  -t "$REGISTRY/storefront:$TAG" \
  "$PROJECT_ROOT"

echo "  [3/3] Building Admin image..."
docker build \
  -f "$PROJECT_ROOT/.docker/images/admin/Dockerfile" \
  --build-arg "VITE_API_URL=$API_URL" \
  -t "$REGISTRY/admin:$TAG" \
  "$PROJECT_ROOT"

echo ""
echo ">>> All images built successfully."
echo ""

# ------------------------------------------------------------------
# Step 3: Push Docker images
# ------------------------------------------------------------------
echo ">>> Pushing Docker images to $REGISTRY..."

docker push "$REGISTRY/api:$TAG"
docker push "$REGISTRY/storefront:$TAG"
docker push "$REGISTRY/admin:$TAG"

echo ""
echo ">>> All images pushed successfully."
echo ""

# ------------------------------------------------------------------
# Step 4: Apply Kubernetes manifests
# ------------------------------------------------------------------
echo ">>> Applying Kubernetes manifests..."

kubectl apply -k "$SCRIPT_DIR/production"

echo ""
echo ">>> Waiting for infrastructure to be ready..."

kubectl -n "$NAMESPACE" wait --for=condition=ready pod -l app.kubernetes.io/name=mariadb --timeout=120s 2>/dev/null || true
kubectl -n "$NAMESPACE" wait --for=condition=ready pod -l app.kubernetes.io/name=redis --timeout=60s 2>/dev/null || true
kubectl -n "$NAMESPACE" wait --for=condition=ready pod -l app.kubernetes.io/name=meilisearch --timeout=60s 2>/dev/null || true

echo ""

# ------------------------------------------------------------------
# Step 5: Run migrations and seeders
# ------------------------------------------------------------------
echo ">>> Running migrations and seeders..."

# Delete previous job if it exists
kubectl -n "$NAMESPACE" delete job quicktane-migrate-seed --ignore-not-found

# Apply job with the correct image tag
sed "s|ghcr.io/quicktane/quicktane/api:latest|$REGISTRY/api:$TAG|g" \
  "$SCRIPT_DIR/production/jobs/migrate-seed.yaml" | kubectl apply -f -

echo "  Waiting for migrate-seed job to complete..."
kubectl -n "$NAMESPACE" wait --for=condition=complete job/quicktane-migrate-seed --timeout=300s

echo "  Migration logs:"
kubectl -n "$NAMESPACE" logs job/quicktane-migrate-seed --tail=20

echo ""

# ------------------------------------------------------------------
# Step 6: Update deployment images
# ------------------------------------------------------------------
echo ">>> Updating deployment images to $TAG..."

kubectl -n "$NAMESPACE" set image deployment/api api="$REGISTRY/api:$TAG"
kubectl -n "$NAMESPACE" set image deployment/queue-worker queue-worker="$REGISTRY/api:$TAG"
kubectl -n "$NAMESPACE" set image deployment/storefront storefront="$REGISTRY/storefront:$TAG"
kubectl -n "$NAMESPACE" set image deployment/admin admin="$REGISTRY/admin:$TAG"

echo ""
echo ">>> Waiting for rollouts..."

kubectl -n "$NAMESPACE" rollout status deployment/api --timeout=120s
kubectl -n "$NAMESPACE" rollout status deployment/queue-worker --timeout=60s
kubectl -n "$NAMESPACE" rollout status deployment/storefront --timeout=60s
kubectl -n "$NAMESPACE" rollout status deployment/admin --timeout=60s

echo ""
echo "============================================"
echo "  Deployment complete!"
echo ""
echo "  Storefront: https://demo.quicktane.com"
echo "  Admin:      https://demo.quicktane.com/admin"
echo "  API:        https://api.demo.quicktane.com"
echo "  Health:     https://api.demo.quicktane.com/api/health"
echo "============================================"
