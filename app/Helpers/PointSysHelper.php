<?php

use App\Services\PointSysService;
use Illuminate\Support\Facades\Log;

if (!function_exists('findExistingPointSysCustomer')) {
    function findExistingPointSysCustomer(string $email): ?int
    {
        try {
            $pointSysService = new PointSysService();
            $maxCustomerId = 30;
            $startTime = microtime(true);

            for ($i = 1; $i <= $maxCustomerId; $i++) {
                if ((microtime(true) - $startTime) > 8) {
                    Log::warning('PointSys customer search timeout after 8 seconds', [
                        'email' => $email,
                        'customers_checked' => $i
                    ]);
                    break;
                }

                try {
                    $balance = $pointSysService->getCustomerBalance($i);

                    if ($balance && isset($balance['data']['email']) && $balance['data']['email'] === $email) {
                        return $i;
                    }

                    usleep(20000);
                } catch (\Exception $e) {
                    continue;
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error while searching for existing PointSys customer', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
