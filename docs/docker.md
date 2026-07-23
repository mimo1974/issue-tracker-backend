# Docker image

The `Dockerfile` builds a single image containing Caddy and the Symfony application (`php:8.4-fpm` + the `caddy` binary), built in stages:

1. `vendor` — installs production Composer dependencies and runs the Symfony post-install scripts.
2. `caddy` — pulls the official `caddy:2-alpine` image so its binary can be copied into the final stage.
3. `app` — installs the `intl` and `opcache` PHP extensions, copies in the Caddy binary and `Caddyfile`, and copies the built app. `docker/entrypoint.sh` starts `php-fpm` and `caddy` as sibling processes and forwards `TERM`/`INT` to both for a clean shutdown.

Caddy serves `public/` and proxies PHP requests to `php-fpm` over FastCGI (`docker/caddy/Caddyfile`).

## Build

```bash
docker build -t issue-tracker-be .
```

## Run

```bash
docker run -d -p 8080:80 \
  -e APP_ENV=prod \
  -e APP_SECRET=<generate-a-secret> \
  issue-tracker-be
```

The app is then reachable at http://localhost:8080. Generate `APP_SECRET` with `php -r 'echo bin2hex(random_bytes(16));'`.

## Development image

The `dev` stage (built from `app`) is for local live editing: it regenerates a plain PSR-4 autoloader (the prod stage bakes an optimized, authoritative classmap that won't pick up new classes) and sets `opcache.validate_timestamps=1` so edited files are re-read instead of served from cache.

```bash
docker compose up -d
```

This builds the `dev` target and bind-mounts the repo into the container at `/var/www/html`, so edits to `src/`, `templates/`, `config/`, etc. take effect immediately — no rebuild needed for either new files or edited ones. The app is reachable at http://localhost:8080.

`vendor/` and `var/` are kept in named Docker volumes (not bind-mounted) so the container's own dependency install and cache aren't clobbered by the host directory. This means **after changing `composer.json`/`composer.lock`, recreate the `vendor` volume** so the container reinstalls:

```bash
docker compose down -v
docker compose up -d --build
```
