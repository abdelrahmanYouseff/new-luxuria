#!/bin/bash

# Laravel Scheduler Runner
# This script runs the Laravel scheduler every minute

echo "🚀 Starting Laravel Scheduler..."
echo "📅 Vehicle sync will run every 5 minutes"

while true; do
    php artisan schedule:run
    sleep 60
done
