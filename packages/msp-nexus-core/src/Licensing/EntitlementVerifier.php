<?php
/**
 * Verifies and normalizes signed offline entitlement caches.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Licensing;

final class EntitlementVerifier
{
    /** @param array<string, mixed> $payload
     *  @return array<string, mixed>|null
     */
    public function verified(array $payload): ?array
    {
        $entitlement = $payload['signed_entitlement'] ?? null;
        $signature = (string) ($payload['entitlement_signature'] ?? '');
        if (! is_array($entitlement) || '' === $signature || 1 !== ($entitlement['schema'] ?? null)) {
            return null;
        }
        $default_key = defined('MSP_NEXUS_MANIFEST_PUBLIC_KEY') ? (string) constant('MSP_NEXUS_MANIFEST_PUBLIC_KEY') : '';
        $public_key = (string) apply_filters('msp_nexus_manifest_public_key', $default_key);
        if (! (new ManifestVerifier())->verify($entitlement, $signature, $public_key)) {
            return null;
        }
        if (($entitlement['site_url'] ?? '') !== untrailingslashit(home_url('/')) || strtotime((string) ($entitlement['cache_expires_at'] ?? '')) <= time()) {
            return null;
        }
        return $entitlement;
    }
}
