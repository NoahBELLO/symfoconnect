#!/bin/bash
set -e

echo "=== Déploiement SymfoConnect ==="

# Installation des dépendances (sans dev)
composer install --no-dev --optimize-autoloader

# Exécution des migrations
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Warmup du cache
php bin/console cache:warmup --env=prod

# Compilation des assets
php bin/console asset-map:compile --env=prod

echo "=== Déploiement terminé ==="
