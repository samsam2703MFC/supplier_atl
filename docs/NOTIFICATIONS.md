# Notification e-mail des nouvelles commandes

`bin/notify_new_orders.php` interroge l'API TF Buddy avec le compte
fournisseur, repère les commandes **NEW** jamais vues (état local dans
`var/notify_state.json`) et envoie un e-mail récapitulatif — par défaut à
**centrale@atelierby.be** — avec clé de commande, magasin, date, valeur
nette et lien direct vers le portail.

L'API expose bien `POST /orders/{id}/email`, mais aucun déclencheur
automatique à la création de commande : ce cron est le contournement côté
portail tant que le backend n'offre pas de notification native.

## Mise en place (serveur)

1. Renseigner les variables d'environnement du script — par exemple dans
   `/etc/cron.d/supplier-notify` :

   ```cron
   SUPPLIER_LOGIN=le_login_fournisseur
   SUPPLIER_PASSWORD=le_mot_de_passe
   NOTIFY_EMAIL=centrale@atelierby.be
   PORTAL_URL=https://VOTRE-DOMAINE/supplier
   # API_BASE_URL=https://atelierby.tfbuddy.com/api/v1   (défaut)

   */10 * * * * www-data php /chemin/vers/supplier/bin/notify_new_orders.php >> /var/log/supplier-notify.log 2>&1
   ```

2. Vérifier que PHP CLI peut envoyer du courrier (`mail()` → sendmail/postfix
   configuré sur le serveur).

3. Premier essai à la main :

   ```bash
   SUPPLIER_LOGIN=… SUPPLIER_PASSWORD=… php bin/notify_new_orders.php
   ```

## Comportement

- Sans nouvelle commande : aucun e-mail, sortie « Aucune nouvelle commande ».
- Échec d'envoi : l'état n'est pas mis à jour → nouvel essai au passage suivant.
- L'état retient les 500 derniers identifiants notifiés (`var/notify_state.json`).
