<?php
namespace App\Supplier\core\Cookie;

use App\Supplier\app\Models\Auth\JWTModel;

class CookieManager {

    public function setAuthCookie($accessToken, $expiryTokenDate)
    {
        $expires = strtotime($expiryTokenDate);

        $res_access = setcookie('supplier_access_token', $accessToken, [
            'expires' => $expires,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        if (!$res_access) return false;

        // Update superglobal so the new token is available in the current request
        $_COOKIE['supplier_access_token'] = $accessToken;

        $res_expiry = setcookie('supplier_access_token_expiry', $expiryTokenDate, [
            'expires' => $expires,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        if (!$res_expiry) return false;

        $_COOKIE['supplier_access_token_expiry'] = $expiryTokenDate;

        return true;
    }

    public function setRefreshCookie($refreshToken)
    {
        $refreshTtl = 60*60*24*30; // 30 dni

        $expiresAt = time() + $refreshTtl;

        setcookie('supplier_refresh_token', $refreshToken, [
            'expires' => $expiresAt,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        // Update superglobal so the new refresh token is available in the current request
        $_COOKIE['supplier_refresh_token'] = $refreshToken;

        // to cookie z datą jest opcjonalne — i tak możesz policzyć time()+TTL
        setcookie('supplier_refresh_token_expiry', date('Y-m-d H:i:s', $expiresAt), [
            'expires' => $expiresAt,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        return true;
    }

    public function unsetCookies()
    {
        setcookie('supplier_refresh_token', '', [
            'expires' => time() - 3600, // Poprawiony timestamp
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        setcookie('supplier_refresh_token_expiry', '', [
            'expires' => time() - 3600, // Poprawiony timestamp
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        setcookie('supplier_access_token', '', [
            'expires' => time() - 3600, // Poprawiony timestamp
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        setcookie('supplier_access_token_expiry', '', [
            'expires' => time() - 3600, // Poprawiony timestamp
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',]);
    }


    public function getAccessToken()
    {
        return $_COOKIE['supplier_access_token'] ?? null;
    }

    public function getRefreshToken()
    {
        return $_COOKIE['supplier_refresh_token'] ?? null;
    }

    public function getAccessTokenExpiryTime()
    {
        return $_COOKIE['supplier_access_token_expiry'] ?? null;
    }

    public function getRefreshTokenExpiryTime()
    {
        return $_COOKIE['supplier_refresh_token_expiry'] ?? null;
    }

}