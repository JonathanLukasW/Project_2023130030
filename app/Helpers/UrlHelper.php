<?php

use Illuminate\Support\Facades\Crypt;

if (! function_exists('encodeId')) {
    /**
     * Encrypts an integer ID for secure URL usage.
     * @param int $id
     * @return string
     */
    function encodeId(int $id): string
    {
        return Crypt::encryptString((string) $id);
    }
}

if (! function_exists('decodeId')) {
    /**
     * Decrypts an ID from a URL parameter back to an integer.
     * @param string $encodedId
     * @return int|null
     */
    function decodeId(string $encodedId): ?int
    {
        try {
            $decrypted = Crypt::decryptString($encodedId);
            return (int) $decrypted;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return null;
        }
    }
}