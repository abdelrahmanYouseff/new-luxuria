#!/bin/bash

echo "Starting Laravel Queue Worker..."
echo "Processing emails and jobs..."

php artisan queue:work --tries=3 --timeout=60 --sleep=3 --verbose
