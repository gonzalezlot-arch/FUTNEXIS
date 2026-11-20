<?php

namespace App\Modules\Core\Support;

class IpAddress
{
    /**
     * Group an address for rate limiting: one IPv4 address, or the /64 network of an IPv6 address,
     * because a single IPv6 customer controls a whole /64 and could otherwise rotate addresses freely.
     */
    public static function bucket(?string $address): string
    {
        $packed = $address === null ? false : @inet_pton($address);

        if ($packed === false) {
            return 'unknown';
        }

        if (strlen($packed) === 4) {
            return (string) inet_ntop($packed);
        }

        if (str_starts_with($packed, str_repeat("\0", 10)."\xFF\xFF")) {
            return (string) inet_ntop(substr($packed, 12));
        }

        return bin2hex(substr($packed, 0, 8)).'/64';
    }
}
