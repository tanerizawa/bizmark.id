# Multi-stage Dockerfile for Bizmark Backend
# Compatible with development and production deployment

# Stage 1: Build stage
FROM node:18-alpine AS builder

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies (including devDependencies for build)
RUN npm ci --include=dev

# Copy source code
COPY . .

# Build the application
RUN npm run build

# Remove dev dependencies to reduce size
RUN npm prune --production

# Stage 2: Production stage
FROM node:18-alpine AS production

# Install required system packages
RUN apk add --no-cache \
    dumb-init \
    curl \
    postgresql-client \
    && rm -rf /var/cache/apk/*

# Create app user
RUN addgroup -S appgroup && adduser -S appuser -G appgroup

# Set working directory
WORKDIR /app

# Copy built application from builder stage
COPY --from=builder --chown=appuser:appgroup /app/dist ./dist
COPY --from=builder --chown=appuser:appgroup /app/node_modules ./node_modules
COPY --from=builder --chown=appuser:appgroup /app/package*.json ./

# Copy additional required files
COPY --chown=appuser:appgroup scripts ./scripts

# Create necessary directories
RUN mkdir -p logs uploads temp \
    && chown -R appuser:appgroup logs uploads temp

# Switch to non-root user
USER appuser

# Expose port
EXPOSE 3001

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:3001/health || exit 1

# Use dumb-init for proper signal handling
CMD ["dumb-init", "node", "dist/main.js"]

# Development stage (optional, for local Docker development)
FROM node:18-alpine AS development

# Install development tools
RUN apk add --no-cache \
    git \
    curl \
    postgresql-client

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install all dependencies (including dev)
RUN npm ci

# Copy source code
COPY . .

# Create non-root user for development
RUN addgroup -S appgroup && adduser -S appuser -G appgroup \
    && chown -R appuser:appgroup /app

USER appuser

# Expose port
EXPOSE 3001

# Development command (with hot reload)
CMD ["npm", "run", "start:dev"]
