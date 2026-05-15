#!/bin/bash

# Lodge POS Build Script
# This script builds both the React frontend and prepares the Laravel backend

set -e

echo "🏨 Building Lodge POS..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the Lodge POS root directory"
    exit 1
fi

# Install dependencies
print_status "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

print_status "Installing Node.js dependencies..."
npm ci

# Build React frontend
print_status "Building React frontend..."
npm run build

# Optimize Laravel
print_status "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
print_status "Setting permissions..."
chmod -R 755 storage bootstrap/cache

print_status "✅ Build completed successfully!"
echo ""
echo "To start the application:"
echo "  php artisan serve --port=8000"
echo ""
echo "To run in development mode:"
echo "  npm run dev"
