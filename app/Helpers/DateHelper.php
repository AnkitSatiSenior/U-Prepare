<?php
if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'd M Y')
    {
        if (empty($date)) {
            return '';
        }

        try {
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return '';
        }
    }
}
if (!function_exists('formatPrice')) {
    function formatPrice($price)
    {
        if (empty($price) && $price !== 0 && $price !== '0') {
            return '';
        }
        $priceStr = trim(strtoupper(strval($price)));
        if (strpos($priceStr, 'CR') !== false) {
            $num = floatval(str_replace('CR', '', $priceStr));
            $rupees = $num * 10000000;
        } else {
            $rupees = floatval(str_replace(',', '', $priceStr));
        }

        return ' ₹ ' . formatIndianCurrency(number_format($rupees, 2, '.', ''));
    }
}
if (!function_exists('formatPriceToCR')) {
    function formatPriceToCR($price)
    {
        // Convert null/empty string to 0
        if (empty($price) && $price !== 0 && $price !== '0') {
            $price = 0;
        }

        $price = (float) $price;

        if ($price >= 10000000) {
            // Crores
            return '₹ ' . number_format($price / 10000000, 2) . ' CR';
        }

        if ($price >= 100000) {
            // Lakhs
            return '₹ ' . number_format($price / 100000, 2) . ' Lakhs';
        }

        // Always show 0 with ₹
        return '₹ ' . number_format($price, 2);
    }
}
if (!function_exists('getDepartmentsStats')) {
    function getDepartmentsStats($scope = 'all')
    {
        $controller = app()->make(App\Http\Controllers\DashboardController::class);
        return $controller->getDepartmentsStatsOther($scope);
    }
}

if (!function_exists('formatIndianCurrency')) {
    function formatIndianCurrency($number)
    {
        $decimal = '';
        if (strpos($number, '.') !== false) {
            [$number, $decimal] = explode('.', $number);
            $decimal = '.' . $decimal;
        }

        $number = strrev($number);
        $formatted = '';
        for ($i = 0; $i < strlen($number); $i++) {
            if ($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) {
                $formatted .= ',';
            }
            $formatted .= $number[$i];
        }
        return strrev($formatted) . $decimal;
    }
}
if (!function_exists('canRoute')) {
    function canRoute(string $routeName): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $allowedRoutes = cache()->remember("role_routes_{$user->role_id}", now()->addMinutes(10), fn() => \App\Models\RoleRoute::where('role_id', $user->role_id)->pluck('route_name')->toArray());

        return in_array($routeName, $allowedRoutes);
    }
}
