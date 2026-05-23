<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Deployment (GitHub Actions + Docker Hub + Portainer)

CI/CD runs on GitHub Actions: tests and Pint on every push/PR to `main`; on push to `main`, the workflow builds a Docker image, pushes it to Docker Hub (`chucanhquan/techsavvy-api`), and triggers a Portainer stack webhook to redeploy on the VPS.

### GitHub repository secrets

| Secret | Required | Description |
|--------|----------|-------------|
| `DOCKERHUB_USERNAME` | Yes (deploy) | Docker Hub username, e.g. `chucanhquan` |
| `DOCKERHUB_TOKEN` | Yes (deploy) | Docker Hub access token (Read/Write) from Account Settings → Security |
| `PORTAINER_WEBHOOK_URL` | For auto-deploy | Stack webhook URL from Portainer. If unset, the image is still pushed but deploy is skipped. |

Create the token at [hub.docker.com](https://hub.docker.com) → Account Settings → Security → New Access Token. Do not commit the token to the repository.

### One-time Portainer setup (VPS)

1. **Registry** — Not required for a **public** image on Docker Hub; the VPS can pull without registry credentials in Portainer.

2. **Stack** — Create a stack with compose files from this repo:
   - `docker-compose.yml`
   - `docker-compose.prod.yml`
   - Compose command (or equivalent in Portainer): `docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d`

3. **Server `.env`** (on the VPS, not committed) — include at least:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY=...`
   - Database and app settings from `.env.example`
   - `DOCKER_IMAGE=chucanhquan/techsavvy-api`
   - `IMAGE_TAG=latest`

4. **Webhook** — Stack → Webhooks → enable → copy the URL → add as GitHub secret `PORTAINER_WEBHOOK_URL`.

   Portainer must be reachable from GitHub Actions (public HTTPS URL or VPN). The webhook redeploy pulls the new image and recreates `app`, `scheduler`, and `worker`; MySQL (`db`) is unchanged.

### After deploy (migrations / cache)

The webhook does not run Artisan commands. After a release that includes migrations:

```bash
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
```

### Rollback

Set `IMAGE_TAG` on the server to a previous commit SHA (images are tagged `chucanhquan/techsavvy-api:<sha>` on Docker Hub), update the stack, or redeploy from Portainer.

### Local Docker

```bash
docker compose build
docker compose up -d
```

Uses local image tag `local/techsavvy-api:local` by default. Override with `DOCKER_IMAGE` and `IMAGE_TAG` if needed.
