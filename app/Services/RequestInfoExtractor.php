<?php

namespace App\Services;

use Illuminate\Http\Request;

class RequestInfoExtractor
{
    /**
     * Extract all visitor information from request
     */
    public function extract(Request $request): array
    {
        return [
            'ip_address' => $this->getIpAddress($request),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'full_url' => $request->fullUrl(),
            'device_type' => $this->getDeviceType($request->userAgent()),
            'browser' => $this->getBrowser($request->userAgent()),
            'browser_version' => $this->getBrowserVersion($request->userAgent()),
            'platform' => $this->getPlatform($request->userAgent()),
            'platform_version' => null,
            'fbclid' => $request->query('fbclid'),
            'gclid' => $request->query('gclid'),
            'utm_source' => $request->query('utm_source'),
            'utm_medium' => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_content' => $request->query('utm_content'),
            'utm_term' => $request->query('utm_term'),
        ];
    }

    /**
     * Get IP address with proxy support
     */
    public function getIpAddress(Request $request): string
    {
        // Check X-Forwarded-For header (for load balancers/proxies)
        if ($request->header('X-Forwarded-For')) {
            $ips = explode(',', $request->header('X-Forwarded-For'));
            return trim($ips[0]);
        }

        // Check X-Real-IP header
        if ($request->header('X-Real-IP')) {
            return $request->header('X-Real-IP');
        }

        // Fallback to Laravel's default
        return $request->ip();
    }

    /**
     * Detect device type from user agent
     */
    public function getDeviceType(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        $userAgent = strtolower($userAgent);

        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone/i', $userAgent)) {
            if (preg_match('/ipad|tablet|playbook|silk/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }

        if (preg_match('/bot|crawler|spider|curl|wget|python/i', $userAgent)) {
            return 'bot';
        }

        return 'desktop';
    }

    /**
     * Detect browser from user agent
     */
    public function getBrowser(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        $userAgent = strtolower($userAgent);

        if (preg_match('/edg|edge/i', $userAgent)) {
            return 'edge';
        }
        if (preg_match('/chrome/i', $userAgent)) {
            return 'chrome';
        }
        if (preg_match('/firefox/i', $userAgent)) {
            return 'firefox';
        }
        if (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            return 'safari';
        }
        if (preg_match('/opera|opera mini/i', $userAgent)) {
            return 'opera';
        }
        if (preg_match('/msie|trident/i', $userAgent)) {
            return 'ie';
        }

        return 'other';
    }

    /**
     * Get browser version
     */
    public function getBrowserVersion(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        // Chrome
        if (preg_match('/chrome\/(\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }

        // Firefox
        if (preg_match('/firefox\/(\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }

        // Safari
        if (preg_match('/version\/(\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }

        // Edge
        if (preg_match('/edg\/(\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }

        // Opera
        if (preg_match('/opera\/(\d+)/i', $userAgent, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Detect platform from user agent
     */
    public function getPlatform(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        $userAgent = strtolower($userAgent);

        if (preg_match('/windows nt 10/i', $userAgent)) {
            return 'windows';
        }
        if (preg_match('/windows nt 6\.3/i', $userAgent)) {
            return 'windows';
        }
        if (preg_match('/windows nt 6\.1/i', $userAgent)) {
            return 'windows';
        }
        if (preg_match('/macintosh|mac os x/i', $userAgent)) {
            if (preg_match('/iphone/i', $userAgent)) {
                return 'ios';
            }
            if (preg_match('/ipad/i', $userAgent)) {
                return 'ios';
            }
            return 'macos';
        }
        if (preg_match('/android/i', $userAgent)) {
            return 'android';
        }
        if (preg_match('/linux/i', $userAgent)) {
            return 'linux';
        }
        if (preg_match('/freebsd/i', $userAgent)) {
            return 'freebsd';
        }

        return 'other';
    }
}
