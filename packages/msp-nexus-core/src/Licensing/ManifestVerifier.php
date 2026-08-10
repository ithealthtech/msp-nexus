<?php
/**
 * Ed25519 update-manifest verification.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Licensing;

final class ManifestVerifier
{
    /** @param array<string, mixed> $manifest */
    public function verify(array $manifest, string $signature, string $publicKey): bool
    {
        if (! function_exists('sodium_crypto_sign_verify_detached')) {
            return false;
        }
        $decodedSignature = base64_decode($signature, true);
        $decodedKey = base64_decode($publicKey, true);
        if (! is_string($decodedSignature) || ! is_string($decodedKey) || SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES !== strlen($decodedKey)) {
            return false;
        }
        return sodium_crypto_sign_verify_detached($decodedSignature, $this->canonical_json($manifest), $decodedKey);
    }

    /** @param mixed $value */
    private function canonical_json($value): string
    {
        if (is_array($value)) {
            if (array_values($value) === $value) {
                return '[' . implode(',', array_map(array($this, 'canonical_json'), $value)) . ']';
            }
            ksort($value, SORT_STRING);
            $pairs = array();
            foreach ($value as $key => $item) {
                $pairs[] = wp_json_encode((string) $key, JSON_UNESCAPED_SLASHES) . ':' . $this->canonical_json($item);
            }
            return '{' . implode(',', $pairs) . '}';
        }
        return (string) wp_json_encode($value, JSON_UNESCAPED_SLASHES);
    }
}
