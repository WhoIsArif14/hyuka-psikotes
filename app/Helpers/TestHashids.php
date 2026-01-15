<?php

namespace App\Helpers;

use Hashids\Hashids;

class TestHashids
{
    private static ?Hashids $hashids = null;

    /**
     * Get Hashids instance
     */
    private static function getHashids(): Hashids
    {
        if (self::$hashids === null) {
            // Salt dari APP_KEY untuk keamanan
            $salt = config('app.key') . '-test-id-salt';
            // Min length 8 karakter untuk lebih aman
            self::$hashids = new Hashids($salt, 8);
        }

        return self::$hashids;
    }

    /**
     * Encode test ID menjadi hash string
     *
     * @param int $testId
     * @return string
     */
    public static function encode(int $testId): string
    {
        return self::getHashids()->encode($testId);
    }

    /**
     * Decode hash string menjadi test ID
     *
     * @param string $hash
     * @return int|null
     */
    public static function decode(string $hash): ?int
    {
        $decoded = self::getHashids()->decode($hash);

        // Hashids::decode mengembalikan array, ambil elemen pertama
        return !empty($decoded) ? $decoded[0] : null;
    }

    /**
     * Validasi apakah hash valid
     *
     * @param string $hash
     * @return bool
     */
    public static function isValid(string $hash): bool
    {
        return self::decode($hash) !== null;
    }
}
