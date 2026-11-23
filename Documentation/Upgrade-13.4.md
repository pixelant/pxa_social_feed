# Upgrade to TYPO3 13.4

The 5.x development line targets TYPO3 **13.4 LTS** and PHP **8.2–8.5**.
TYPO3 11/12 remain on the existing 4.x line. TYPO3 14 is outside the declared
compatibility range. This feature branch is not a published release.

## Upgrade an existing site

### Preparation and deployment

Back up the database, file storages, Composer files and site configuration.
Rehearse the upgrade on a staging copy with the site's template overrides.
Follow TYPO3's core upgrade path for the site's source version; this guide covers
the extension-specific steps and does not replace the core upgrade instructions.

Deploy TYPO3 13.4 with the current security patch and the compatible extension
version. Composer must also install `typo3/cms-scheduler` and `typo3/cms-install`.
Update and commit the consuming project's `composer.lock` as part of the build,
then deploy the resulting files or rebuilt container image. Composer is needed
in the build environment; it does not have to be installed in the running web
container. Replacing an image alone does not migrate the existing database.

After deployment, choose either the UI or console procedure below. Both run the
same extension migration. A UI-only administrator needs the prepared application
files or image from the person responsible for deployment.

### Upgrade through the backend UI

Use a system maintainer account with access to the Admin Tools / Install Tool.
An ordinary editor account is insufficient. Labels may differ with the backend
language.

1. Open **Admin Tools > Maintenance**, flush the TYPO3 caches and rebuild the
   PHP autoload information if that action is offered for the installation.
2. Open **Admin Tools > Maintenance > Analyze Database Structure**. Apply the
   required schema additions and changes. Postpone removal of obsolete tables
   and columns until the upgrade wizards have completed.
3. Open **Upgrade Wizard** in the Upgrade module and run the required TYPO3 core
   wizards for the site's source version. Run **Migrate Pxa Social Feed plugins
   to TYPO3 13.4** (`pxaSocialFeedMigratePlugin`) when offered. It appears only if
   old Social Feed plugin records still need migration.
4. Flush the TYPO3 caches again under **Maintenance**, then complete the shared
   checks below. No shell commands are required for these post-deployment steps.

If the backend cannot be opened, use the standalone Install Tool at
`/typo3/install.php` (relative to the installation's base URL) with its configured
Install Tool password. Follow the hosting setup's procedure to enable access.
See the [TYPO3 13.4 maintenance documentation](https://docs.typo3.org/m/typo3/reference-coreapi/13.4/en-us/Administration/Tools/Maintenance/Index.html)
for the database analyzer and cache tools. Rebuilding autoload information in
the UI is available only in classic mode; Composer deployments generate it in
the build environment.

### Upgrade through the console

Run these commands from the TYPO3 project's root in the deployed environment,
using the PHP version and database configuration of that installation:

```sh
vendor/bin/typo3 extension:setup
vendor/bin/typo3 upgrade:list
```

Run the required TYPO3 core upgrade wizards for the site's source version, then
run the extension migration and clear caches:

```sh
vendor/bin/typo3 upgrade:run pxaSocialFeedMigratePlugin
vendor/bin/typo3 cache:flush
```

For containers, execute the commands inside the running application container.
Do not run them during an image build against the live site's database. The
extension wizard is repeatable and makes no changes if no old plugin records
remain.

### Checks after either upgrade procedure

1. Keep the extension's Minimum TypoScript included. Reconcile local overrides
   with the changes below. Open and save a migrated content element in the
   backend and verify its selected configurations and rendering mode.
2. Configure a writable **default FAL storage** for imported images. Verify the
   existing scheduler task, its selected configurations and notification addresses.
3. Complete the provider and browser checks below before production deployment.
   The migration preserves stored tokens; a TYPO3 upgrade alone does not require
   regenerating them. If a provider requires a new login, start it from the
   upgraded backend as described below.

## Content and template changes

- The General Plugin record (`CType=list`, `list_type=pxasocialfeed_showfeed`)
  becomes the dedicated `CType=pxasocialfeed_showfeed`. Adjust sitepackage
  TypoScript conditions, Page TSconfig permissions, allowed content types and
  content-element overrides which refer to `list_type` or `tt_content.list.20`.
- `switchableControllerActions` is replaced by `settings.loadType`, with values
  `standard` and `ajax`. The wizard retains the selection and all other FlexForm
  settings. It includes hidden, deleted and workspace records and is repeatable.
  Invalid XML is retained verbatim; repair such pre-existing FlexForms manually.
- The AJAX page type remains **7378121**. Its Extbase plugin is now **Ajax** and
  its parameter namespace is `tx_pxasocialfeed_ajax`. Replace custom AJAX URLs
  with `f:uri.action(pluginName: 'Ajax', pageType: 7378121, ...)` so TYPO3 creates
  a valid signed URL. The response remains JSON with `success` and `html`.
- Dates use PHP date syntax: the default changes from `%d-%m-%y` to `d-m-y`.
  Convert custom strftime-style date formats as well.
- Backend overrides use the Core `Module` layout and `Content` section.
  The old backend layout, page-module hook and SignalSlot helper are removed.
  Existing PSR-14 event classes remain available.
- Provider message text is HTML-escaped before URLs, hashtags and mentions are
  linked. Custom templates must not depend on executing HTML from imported posts.
- OAuth generation must start in the backend. The callback requires the matching,
  single-use, token-specific session state; previously opened login windows must
  be restarted. Allow the callback URI shown in the backend in the Facebook app.
  The callback respects subdirectory installations and does not display tokens.
  Login generation uses its own provider so cached metadata clients cannot drop
  the callback URI.

## Changes covered by this branch

Administration uses ModuleTemplate responses, ES modules, Bootstrap controls,
TYPO3 modal events and the current page-browser message format. Models match
Extbase's typed properties; DBAL 4 queries and scheduler action/severity enums
replace removed APIs. Editor filtering uses resolved backend groups instead of
the removed `groupList` property. TCA/FlexForms and the page-module preview use
the TYPO3 13 APIs; Fluid helpers no longer use CompileWithRenderStatic.

Import fixes initialize Twitter v2 sources with their configuration, dispatch the
existing Twitter events, use the correct Facebook page token, handle optional
provider fields and reuse FAL images with their detected extension. Graph requests
use TYPO3's HTTP client. Text-only cards and the Twitter v2 icon render correctly.
Factories are tested as real objects instead of mocking the factories themselves.

## Reproduce the checks (extension development)

These commands test the extension itself; they are not the site upgrade
procedure. Run from a fresh extension checkout with PHP, Composer, SQLite and
Node.js 22+:

```sh
composer update --prefer-dist
composer validate --strict
composer tests:all
node --test Tests/JavaScript/*.test.mjs
```

Composer installs development dependencies under `.Build/`. Functional tests
create isolated SQLite databases; do not point their database settings at an
existing installation. `composer.lock` is intentionally untracked for this
extension library. For the minimum dependency run use a separate checkout and
`composer update --prefer-lowest --prefer-stable`.

The CI matrix tests current dependencies on PHP 8.2, 8.3, 8.4 and 8.5, plus the
lowest Composer-resolvable secure dependencies on PHP 8.2. JavaScript behavior
checks run in a separate Node job.

## Verification on 19 November 2026

Tests ran in isolated official TYPO3 PHP containers with TYPO3 **13.4.40**,
Testing Framework **9.7.0** and real TYPO3 bootstrap, database and rendering.
All PHP files, including root extension files, passed syntax checks.
`composer validate --strict` passed and Composer reported no security advisories.

| PHP | Dependencies | Unit tests | Functional tests |
| --- | --- | --- | --- |
| 8.2.35 | Lowest resolvable | 111 / 137 assertions | 27 / 168 assertions; vendor deprecation below |
| 8.2.35 | Current | 111 / 137 assertions | 27 / 168 assertions |
| 8.3.36 | Current | 111 / 137 assertions | 27 / 168 assertions |
| 8.4.27 | Current | 111 / 137 assertions | 27 / 168 assertions |
| 8.5.12 | Current | 111 / 137 assertions | 27 / 168 assertions |

Current dependencies included DBAL **4.4.5**, Fluid **4.6.2**, PHPUnit **11.5.58**
and OAuth2 Client **2.9.1**. The lowest run resolved DBAL **4.4.4**, Fluid **4.6.1**,
PHPUnit **11.5.50** and OAuth2 Client **2.8.0**. That run emits a PHP deprecation
from old `webmozart/assert` (`static` in callables); it has no test failures or
warnings. Current dependencies produce no test deprecations on PHP 8.2–8.5.

The 27 functional cases cover backend index and create forms, dependency
injection, preview, registered upgrade wizard, editor group restrictions, token lookup/update, scheduler
fields, repeatable migration, both frontend partials in standard/AJAX mode,
provider import/update fixtures, Twitter v2 HTTP request construction, Facebook
page-token selection and persisted FAL file references. Two Node tests verify
page selection messages and confirmation before deletion. Changed PHP files
were formatted with the TYPO3 coding standards on PHP 8.2.

## Follow-up verification on 20 November 2026

Opening the module with an existing Facebook or Instagram token exposed two
calls passing an `AccessToken` object to the provider's string parameter. Both
calls now pass the stored token string. The expiration display uses the returned
OAuth token's expiry; unknown expirations remain unknown and empty tokens do not
trigger a request. The previous tests had only rendered the module with YouTube
tokens and did not cover this failure path.

Eight additional unit cases cover typed calls, rejected/empty tokens, known and
unknown expiry, and the real Facebook provider with an HTTP fixture. Two new
backend rendering cases include stored Facebook and Instagram tokens. The full
suite passes with **119 unit tests / 166 assertions** and **29 functional tests /
188 assertions** on PHP **8.2.35** with lowest resolvable dependencies and PHP
**8.5.12** with current dependencies. The existing vendor deprecation in the
lowest run remains; the current run has no test deprecations.

Deploy the updated extension revision (including the Composer lock reference in
the consuming project), rebuild/redeploy the container and clear TYPO3/PHP caches.
This code fix requires no additional database migration wizard.

### Backend UI review

The demo review also exposed missing Bootstrap 5 tab classes and legacy status
backgrounds. Tabs now use `nav-item`, `nav-link`, current data attributes and
matching selection states. Token feedback uses theme-aware alerts and one
regeneration button. A successful provider response without an expiry gets an
informational message; it no longer produces an empty date or an error sentence
inside the number of remaining days. An unknown expiry alone does not require
regenerating the token.

The overview masks app secrets, API keys and access tokens, including Facebook
page and Twitter/X records. Credential values are not included in the overview's
HTML. Existing credential edit forms remain available to authorized editors.

Nine additional backend rendering cases cover absent/zero expiry, rejected
Facebook/Instagram tokens and the other provider types. All overview cases check
for credential disclosure and correct tab markup. The complete suite passes on
PHP **8.2.35** with lowest dependencies and PHP **8.5.12** with current dependencies:
**119 unit tests / 166 assertions** and **38 functional tests / 398 assertions**.
The lowest run retains the previously documented vendor deprecation. Local
browser previews with the real TYPO3 13.4.40 stylesheet verify known, unknown and
rejected-token states and switching between the two tabs.

## Site-specific acceptance still required

No real provider credentials were available. The local suite replaces external
HTTP responses with fixtures; it does not establish live API access or app
permissions. Before deployment verify:

- Facebook/Instagram OAuth, long-lived page tokens, app permissions and a real
  scheduled import with images. Check popup reload and clipboard in the browser.
- Twitter/X v1.1 and v2 access for the site's API subscription, and YouTube quota
  and channel configuration. Provider eligibility is independent of TYPO3 support.
- The site's custom templates, carousel/masonry JavaScript, backend page picker
  and both delivery modes in a browser. Test mail delivery using the site's
  configured transport and test its actual database engine; local tests used SQLite.
