# 🚗 Vehicle Auto-Sync System Guide

## Overview
This system automatically synchronizes vehicle data from the external API to the local database every 5 minutes.

## Files Created/Modified

### 1. Artisan Command
- **File**: `app/Console/Commands/SyncVehiclesFromApi.php`
- **Command**: `php artisan vehicles:sync`
- **Purpose**: Syncs vehicle data from API to database

### 2. Scheduler Configuration
- **File**: `routes/console.php`
- **Schedule**: Every 5 minutes
- **Features**: 
  - No overlapping executions
  - Runs in background
  - Logs to `storage/logs/vehicle-sync.log`

### 3. Scheduler Runner Script
- **File**: `start-scheduler.sh`
- **Purpose**: Runs Laravel scheduler continuously

## Usage

### Manual Sync
```bash
php artisan vehicles:sync
```

### Start Auto-Sync (Option 1 - Simple)
```bash
./start-scheduler.sh
```

### Start Auto-Sync (Option 2 - Using Cron)
Add this to your crontab:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### View Sync Logs
```bash
tail -f storage/logs/vehicle-sync.log
```

## Features

### ✅ What the sync does:
- Fetches latest vehicle data from API
- Updates existing vehicles
- Adds new vehicles
- Maps API data to local database structure
- Logs all activities
- Shows progress bar during manual runs
- Handles errors gracefully

### 🔄 Sync Details:
- **Frequency**: Every 5 minutes
- **API Endpoint**: `https://rlapp.rentluxuria.com/api/vehicles`
- **Timeout**: 30 seconds
- **Overlap Protection**: Yes
- **Error Handling**: Comprehensive logging

### 📊 Data Mapping:
- **Status**: Available, Rented, Maintenance, etc.
- **Categories**: Economy, Luxury, SUV, Sports, etc.
- **Pricing**: Daily, Weekly, Monthly rates
- **Vehicle Info**: Make, Model, Year, Color, etc.

## Monitoring

### Check if sync is working:
1. Run manual sync: `php artisan vehicles:sync`
2. Check logs: `tail storage/logs/vehicle-sync.log`
3. Verify database: Check `vehicles` table for recent updates

### Common Commands:
```bash
# Manual sync
php artisan vehicles:sync

# Force sync (bypasses recent sync checks)
php artisan vehicles:sync --force

# View all available commands
php artisan list

# Check scheduler status
php artisan schedule:list
```

## Troubleshooting

### If sync fails:
1. Check API connectivity
2. Verify API key is correct
3. Check logs for specific errors
4. Ensure database is accessible

### If scheduler not running:
1. Make sure `start-scheduler.sh` is running
2. Or set up proper cron job
3. Check if scheduler is listed: `php artisan schedule:list`

## Production Setup

For production environments, it's recommended to:
1. Set up a proper cron job instead of the shell script
2. Use a process manager like Supervisor
3. Monitor logs regularly
4. Set up alerts for sync failures

---
**Created**: $(date)
**Last Updated**: Vehicle sync runs automatically every 5 minutes 🎉 
