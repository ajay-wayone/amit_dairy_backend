<?php

namespace App\Services;

use App\Models\Gateway;
use Razorpay\Api\Api;

class GatewayService
{
    /**
     * Get gateway credentials by name.
     * Falls back to config/env values if no DB record exists.
     */
    public static function getConfig(string $name): ?array
    {
        $gateway = Gateway::where('name', $name)->first();

        if ($gateway) {
            // Record exists but is deactivated → block
            if (!$gateway->active) {
                return null;
            }
            return $gateway->getActiveCredentials();
        }

        // No DB record at all → fallback to .env
        return self::getEnvFallback($name);
    }

    /**
     * Fallback: read credentials from .env via config().
     */
    protected static function getEnvFallback(string $name): ?array
    {
        switch ($name) {
            case 'razorpay':
                $key = config('services.razorpay.key');
                $secret = config('services.razorpay.secret');
                if ($key && $secret) {
                    return ['key' => $key, 'secret' => $secret, 'mode' => 'env'];
                }
                return null;

            case 'stripe':
                $key = config('services.stripe.key');
                $secret = config('services.stripe.secret');
                if ($key || $secret) {
                    return ['key' => $key, 'secret' => $secret, 'mode' => 'env'];
                }
                return null;

            default:
                return null;
        }
    }

    /**
     * Get a ready-to-use Razorpay API instance.
     */
    public static function getRazorpayApi(): Api
    {
        $config = self::getConfig('razorpay');

        if (!$config || !$config['key'] || !$config['secret']) {
            throw new \RuntimeException('Razorpay credentials not configured.');
        }

        return new Api($config['key'], $config['secret']);
    }
}
