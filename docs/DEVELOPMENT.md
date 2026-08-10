# Development and validation

## Fast local checks

Run `npm install` once, then `npm run check`. This validates required structure, parses every JSON file, parses every PHP file against PHP 7.4 syntax and an unavailable-API policy, checks Node syntax, and runs licensing/update unit tests.

After `npm run package`, run `npm run qa:php74` to install and exercise the packaged ZIPs on clean single-site and multisite WordPress instances using the official PHP 7.4.33 Playground runtime. The multisite command supplies a portless test hostname because WordPress multisite rejects hosts containing custom ports.

When PHP and Composer are available, run:

```text
composer install
composer lint
composer analyse
composer test
```

## WordPress environment

The supplied `docker-compose.yml` pins WordPress 7.0.2 on PHP 8.3 with MariaDB, WP-CLI, and the licensing service for a current-PHP smoke test. The release-floor blueprints explicitly run the packaged theme and plugin on PHP 7.4.33 and reject any lower runtime. Local credentials are intentionally non-production values. Do not expose this stack to an untrusted network.

Typical commands:

```text
docker compose up -d database wordpress licensing
docker compose run --rm wpcli core install --url=http://localhost:8080 --title="MSP Nexus QA" --admin_user=admin --admin_password=local-only-change-me --admin_email=qa@example.test --skip-email
docker compose run --rm wpcli theme activate msp-nexus
docker compose run --rm wpcli plugin activate msp-nexus-core
```

The workstation used to author the implementation did not have native Docker, PHP, Composer, or WP-CLI installed. The release was nevertheless activated and exercised on real WordPress through the official WordPress Playground CLI using PHP 7.4.33. Container-, Composer-, PHPCS-, PHPStan-, PHPUnit-, and Theme Check-specific workflows remain for a capable CI host and must not be represented as executed locally.

## Release packages

`npm run package` creates deterministic archives under `artifacts/` with a fixed source timestamp and a SHA-256 release manifest. Tests, dependency directories, local databases, logs, and build caches are excluded.
