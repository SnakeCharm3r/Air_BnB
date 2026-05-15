#!/bin/bash

# Lodge POS Development Script
# Starts both React dev server and Laravel in development mode

set -e

echo "🏨 Starting Lodge POS Development Server..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "\033[0;31m[ERROR]\033[0m Please run this script from the Lodge POS root directory"
    exit 1
fi

# Check if dependencies are installed
if [ ! -d "node_modules" ]; then
    print_status "Installing Node.js dependencies..."
    npm install
fi

if [ ! -d "vendor" ]; then
    print_status "Installing PHP dependencies..."
    composer install
fi

# Start development servers
print_status "Starting development servers..."
print_warning "Press Ctrl+C to stop both servers"

# Use concurrently to run both servers
npm run start
