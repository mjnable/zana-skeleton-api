# Changelog

All Zana packages share one version number and are released together. A release that did
not change this project is not listed.

## 2.10.0 — 2026-11-01

### Changed

- `config/mediator.php` enables `ValidationBehavior`; `user:create` relies on it and shows invalid
  input field by field (FE-53).
- `AGENTS.md` for Zana 2.10.
- Requires the Zana packages at ^2.10.

## 2.9.0 — 2026-11-01

### Added

- The API skeleton (ADR-024, FE-50): logging, HTTP, routing, cache, security, mediator,
  serialization, validation, database and console, and the maker in development.
- An `Accounts` module: users in the database, `POST /api/tokens` (rate-limited), `GET /api/me`,
  and `user:create`.
- Errors in problem JSON for every client (`http.json_errors`), and `public/503.json` when the
  application cannot start.
- Tests that run without `.env` (`config/testing/`), `AGENTS.md`, and a CI workflow that runs the
  tests and `check`.
