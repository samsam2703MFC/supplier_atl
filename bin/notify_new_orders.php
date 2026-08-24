<?php
/**
 * Notification e-mail des nouvelles commandes fournisseur.
 *
 * À lancer par cron (voir docs/NOTIFICATIONS.md). Le script se connecte à
 * l'API TF Buddy avec le compte fournisseur, liste les commandes NEW, et
 * envoie un e-mail récapitulatif pour celles jamais vues (état local dans
 * var/notify_state.json). Sans nouvelle commande, il ne fait rien.
 *
 * Variables d'environnement :
 *   SUPPLIER_LOGIN / SUPPLIER_PASSWORD  identifiants du compte fournisseur (requis)
 *   NOTIFY_EMAIL      destinataire            (défaut : centrale@atelierby.be)
 *   NOTIFY_FROM       expéditeur              (défaut : noreply@<hostname>)
 *   API_BASE_URL      API TF Buddy            (défaut : https://atelierby.tfbuddy.com/api/v1)
 *   PORTAL_URL        base du portail pour les liens (ex. https://mon-domaine/supplier)
 */

if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }

$apiBase   = rtrim(getenv('API_BASE_URL') ?: 'https://atelierby.tfbuddy.com/api/v1', '/');
$login     = getenv('SUPPLIER_LOGIN') ?: '';
$password  = getenv('SUPPLIER_PASSWORD') ?: '';
$notifyTo  = getenv('NOTIFY_EMAIL') ?: 'centrale@atelierby.be';
$notifyFrom= getenv('NOTIFY_FROM') ?: ('noreply@' . (gethostname() ?: 'supplier-portal'));
$portalUrl = rtrim(getenv('PORTAL_URL') ?: '', '/');
$stateFile = __DIR__ . '/../var/notify_state.json';

function out(string $msg): void { echo '[' . date('Y-m-d H:i:s') . "] $msg\n"; }

function api(string $method, string $url, ?array $payload = null, ?string $token = null): array
{
    $ch = curl_init($url);
    $headers = ['Content-Type: application/json'];
    if ($token) { $headers[] = 'Authorization: Bearer ' . $token; }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 30,
    ]);
    if ($payload !== null) { curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)); }
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($body === false) { return ['_code' => 0, '_error' => $err]; }
    $json = json_decode($body, true);
    return is_array($json) ? $json + ['_code' => $code] : ['_code' => $code, '_raw' => $body];
}

if ($login === '' || $password === '') {
    out('SUPPLIER_LOGIN / SUPPLIER_PASSWORD manquants — abandon.');
    exit(1);
}

// 1. Connexion
$auth = api('POST', "$apiBase/material-suppliers/auth/login", ['login' => $login, 'password' => $password]);
$token = $auth['access_token'] ?? null;
if (!$token) {
    out('Connexion refusée (' . ($auth['_code'] ?? '?') . ') : ' . ($auth['description'] ?? 'réponse inattendue'));
    exit(1);
}

// supplier_id depuis les claims du JWT
$claims = json_decode(base64_decode(strtr(explode('.', $token)[1] ?? '', '-_', '+/')) ?: '{}', true) ?: [];
$supplierId = (int) ($claims['supplier_id'] ?? 0);
if ($supplierId <= 0) {
    out('supplier_id introuvable dans le jeton — abandon.');
    exit(1);
}

// 2. Commandes NEW
$resp = api('GET', "$apiBase/material-suppliers/$supplierId/orders?status=NEW", null, $token);
$orders = $resp['data'] ?? [];
if (!is_array($orders)) { $orders = []; }
out(count($orders) . ' commande(s) NEW côté API.');

// 3. Filtrer celles déjà notifiées
$state = is_file($stateFile) ? (json_decode((string) file_get_contents($stateFile), true) ?: []) : [];
$seen = $state['seen'] ?? [];
$fresh = array_values(array_filter($orders, fn ($o) => !in_array($o['id'] ?? null, $seen, true)));
if (!$fresh) {
    out('Aucune nouvelle commande à notifier.');
    exit(0);
}

// 4. E-mail récapitulatif
$lines = [];
foreach ($fresh as $o) {
    $key   = $o['order_key'] ?? ('#' . ($o['id'] ?? '?'));
    $shop  = $o['shop_name'] ?? ($o['shop']['name'] ?? '—');
    $date  = $o['order_date'] ?? $o['created_at'] ?? '—';
    $value = $o['approximate_value_net'] ?? $o['value_net'] ?? null;
    $line  = "• $key — $shop — $date" . ($value !== null ? ' — ' . number_format((float) $value, 2, ',', ' ') . ' € net' : '');
    if ($portalUrl && !empty($o['id'])) { $line .= "\n  $portalUrl/orders/" . $o['id']; }
    $lines[] = $line;
}
$count   = count($fresh);
$subject = $count === 1 ? 'Nouvelle commande fournisseur' : "$count nouvelles commandes fournisseur";
$bodyTxt = "Bonjour,\n\n"
    . ($count === 1 ? "Une nouvelle commande vient d'être reçue :\n\n" : "$count nouvelles commandes viennent d'être reçues :\n\n")
    . implode("\n\n", $lines)
    . "\n\n— Portail Fournisseur L'Atelier by\n";
$headers = "From: $notifyFrom\r\nContent-Type: text/plain; charset=UTF-8\r\n";

if (!mail($notifyTo, '=?UTF-8?B?' . base64_encode($subject) . '?=', $bodyTxt, $headers)) {
    out("Échec d'envoi du mail à $notifyTo — l'état n'est pas mis à jour, nouvel essai au prochain passage.");
    exit(1);
}
out("E-mail envoyé à $notifyTo ($count commande(s)).");

// 5. Mémoriser (borné aux 500 derniers ids)
foreach ($fresh as $o) { if (!empty($o['id'])) { $seen[] = $o['id']; } }
$state['seen'] = array_slice(array_values(array_unique($seen)), -500);
$state['last_run'] = date('c');
@mkdir(dirname($stateFile), 0775, true);
file_put_contents($stateFile, json_encode($state, JSON_PRETTY_PRINT));
exit(0);
