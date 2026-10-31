# Changelog

All Zana packages share one version number and are released together. A release that did
not change this project is not listed.

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
