# Zana API skeleton

A starting point for a JSON API on [Zana](https://github.com/mjnable/zana-docs): modules, users
with bearer tokens, errors in problem JSON, a database, tests, and instructions for AI coding agents.

```
composer create-project mj-zana/skeleton-api my-api
cd my-api
php bin/console migrate
php bin/console user:create you@example.com "Your Name" --role=admin
composer serve
```

`composer create-project` writes `.env` with a fresh secret, which signs the tokens. Then:

```
curl -X POST http://localhost:8000/api/tokens -H "Content-Type: application/json" \
     -d '{"email": "you@example.com", "password": "…"}'
curl http://localhost:8000/api/me -H "Authorization: Bearer <token>"
```

## What is where

| Path | |
|---|---|
| `src/Bootstrap.php` | The framework's providers |
| `config/` | Settings; `APP_A__B` in the environment or `.env` overrides `a.b` |
| `config/testing/` | The tests' own secret and an in-memory database, so tests run without `.env` |
| `modules/Accounts/` | Users: `POST /api/tokens`, `GET /api/me`, `user:create` |
| `public/` | The document root: `index.php`, and `503.json` when the application cannot start |
| `tests/` | `composer test` runs them and the tests in `modules/*/Tests` |
| `AGENTS.md` | The rules and workflow for AI coding agents (`CLAUDE.md` and `.github/` point to it) |

## The API

| Request | Answer |
|---|---|
| `POST /api/tokens` `{"email", "password"}` | `{"token", "token_type": "Bearer", "expires_in"}`; 401 for wrong credentials, 422 for missing fields, 429 after ten attempts a minute |
| `GET /api/me` with `Authorization: Bearer <token>` | `{"data": {"id", "email", "name", "roles"}}`; 401 without a valid token |

Every error is problem JSON (RFC 9457), with `errors` per field for a 422 (`http.json_errors` in
`config/http.php`). Roles and their permissions are in `config/security.php`; protect a handler
with `#[RequiresAuthentication]` or `#[RequiresPermission('…')]` and `AuthorizationMiddleware` as
route middleware, as `ShowProfileAction` does.

## Adding a feature

The maker is installed for development and follows the module conventions:

```
php bin/console make:module Orders
php bin/console make:entity Orders Order
php bin/console make:repository Orders Order
php bin/console make:feature Orders PlaceOrder --field=product:string --field=quantity:int --http
php bin/console migrate
php bin/console check
```

`make:feature … --http` writes the command, its handler, its API action and route, and its tests;
the business logic is yours. See [working with AI coding agents](https://github.com/mjnable/zana-docs/blob/main/ai-agents.md).

## Going live

1. Set `APP_ENV=production`, `APP_APP__URL` and the database in `.env` on the server, and keep the
   generated `APP_SECURITY__SECRET` secret.
2. `php bin/console migrate`, `php bin/console cache:warm`, then `php bin/console doctor` to check
   the server.
3. Without SSH: `php bin/console release:build --folder=api` builds the upload archives (see the
   [deployment guide](https://github.com/mjnable/zana-docs/blob/main/deployment.md)).

After upgrading Zana, write `AGENTS.md` again with `php bin/console make:agents --force`, then add
back any section of your own; `tests/AgentsFileTest.php` reminds you.

## License

MIT.
