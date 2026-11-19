# TYPO3 13.4 stabilization

This branch targets TYPO3 13.4 LTS and PHP 8.2 or newer. TYPO3 11/12 support
belongs to the existing 4.x release line. TYPO3 14 is outside this branch's
compatibility declaration.

## Development workflow

The feature branch is `codex/typo3-13.4`, based on upstream `master` at
`0285730f0fe749d671a4547c7ffe308cf2a63ddd`. The checkout contains the full
upstream history and tags. Changes are grouped into Conventional Commits;
commit bodies record the reason, migration impact, and verification.

Install dependencies with `composer update`, then run:

```sh
composer validate --strict
composer tests:all
```

Functional tests use an isolated SQLite database. They must never be pointed
at an existing TYPO3 installation's database.

## Migration and verification

The implementation and verification results are recorded below as the
stabilization progresses. Real provider calls require the site's own access
credentials and must be verified before deployment.
