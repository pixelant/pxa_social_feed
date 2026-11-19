# Changelog

## 5.0.0 — Unreleased

### Changed

- Target TYPO3 13.4 LTS and PHP 8.2–8.5; retain TYPO3 11/12 on the 4.x line.
- Port administration, dependency injection, models, DBAL queries, TCA,
  FlexForms, preview, scheduler and Fluid helpers to the TYPO3 13 APIs.
- Register a dedicated Social Feed content type and separate AJAX plugin.
  Include an upgrade wizard preserving existing content and load-mode settings.
- Modernize backend JavaScript and migrate tests to TYPO3 Testing Framework 9
  and PHPUnit 11, with SQLite integration tests and a PHP/dependency CI matrix.

### Fixed

- Restore backend module rendering, page selection, confirmations and editor
  group filtering after removal of legacy TYPO3 APIs.
- Repair Twitter v2 construction/events and Facebook page-token selection.
  Handle optional provider fields and reuse imported FAL images correctly.
- Render text-only cards, Twitter v2 icons and dates with Fluid 4.
- Escape imported message HTML and validate token-specific OAuth state; return
  PSR-7 callback responses without disclosing access tokens.

See [the upgrade and verification guide](Documentation/Upgrade-13.4.md) for
breaking configuration changes, executed checks and required site acceptance.
