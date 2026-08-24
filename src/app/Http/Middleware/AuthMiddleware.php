<?php
namespace App\Supplier\app\Http\Middleware;

use App\Supplier\app\Repositories\Me\SupplierRepository;
use App\Supplier\app\Services\Auth\AuthGuard;
use App\Supplier\app\Services\Auth\AuthService;
use App\Supplier\app\Services\Auth\JwtService;
use App\Supplier\core\Cookie\CookieManager;
use App\Supplier\core\Support\GlobalRegistry;
use DateTime;

class AuthMiddleware
{

    public function __construct(
        private AuthGuard $authGuard,
        private CookieManager  $cookieManager,
        private JwtService     $jwtService,
        private SupplierRepository $supplierRepository,
    )
    {
    }

    /** Supplier company name, fetched once and kept in a cookie so the rail
     *  does not cost an API round-trip on every page. */
    private function supplierName(int $supplierId): string
    {
        $cached = trim((string)($_COOKIE['supplier_display_name'] ?? ''));
        if ($cached !== '' || $supplierId <= 0) {
            return $cached;
        }

        $name = '';
        try {
            $profile = $this->supplierRepository->getMe($supplierId);
            $name = trim((string)($profile?->getName() ?? ''));
        } catch (\Throwable $e) {
            // API unavailable — the rail falls back to its default label.
        }

        if ($name !== '') {
            setcookie('supplier_display_name', $name, [
                'expires' => time() + 43200,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        return $name;
    }

    public function handle()
    {
        if (!$this->authGuard->ensureAccessToken()) {
            $this->cookieManager->unsetCookies();
            Redirect("/auth");
            exit;
        }

        // --- TU budujesz kontekst ---
        $access = $this->cookieManager->getAccessToken();

        if ($access) {
            $claims = $this->jwtService->getClaimsUnsafe($access); // albo jwtService->getClaimsUnsafe

            $supplierId = (int)($claims['supplier_id'] ?? 0);
            $personName = trim((string)($claims['usr_fn'] ?? '') . ' ' . (string)($claims['usr_ln'] ?? ''));
            $supplierName = $this->supplierName($supplierId);

            GlobalRegistry::set('user', [
                'id' => (int)($claims['usr_id'] ?? 0),
                'supplier_id' => $supplierId,
                'first_name' => (string)($claims['usr_fn'] ?? ''),
                'last_name' => (string)($claims['usr_ln'] ?? ''),
                'displayName' => $personName !== '' ? $personName : $supplierName,
                'role' => $supplierName,
                'lang_code' => (string)($claims['usr_lncd'] ?? 'pl'),
                'is_integrated' => (bool)($claims['is_integrated'] ?? false),
            ]);
            GlobalRegistry::set('lang_code', (string)($claims['usr_lncd'] ?? getUserLanguage()));
        }
    }
}