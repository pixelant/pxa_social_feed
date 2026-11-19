import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { test } from 'node:test';
import vm from 'node:vm';

const source = (await readFile(new URL('../../Resources/Public/JavaScript/Backend/social-feed-administration-module.js', import.meta.url), 'utf8'))
  .replace(/^import .*;\n/gm, '')
  .replace('export default new ', 'new ');

function element(dataset = {}) {
  return { dataset, value: '', textContent: '', events: {}, addEventListener(name, handler) { this.events[name] = handler; }, dispatchEvent() {} };
}

function initialize() {
  const input = element();
  const browse = element();
  const title = element();
  const confirmation = element({ confirmationTitle: 'Delete', confirmationMessage: 'Delete fixture?' });
  confirmation.href = 'https://example.test/delete';
  const confirmDialog = element();
  const root = element({ browserUrl: '/typo3/browse?token=fixture' });
  root.querySelectorAll = (selector) => selector === '.delete-action, .confirmation-action' ? [confirmation] : [];
  root.querySelector = (selector) => ({
    '[data-identifier="browse-feeds-storage"]': browse,
    '[data-identifier="feeds-storage-input"]': input,
    '[data-identifier="feed-storage-title"]': title,
  })[selector];
  const messages = {};
  let opened;
  let navigated;
  const window = {
    location: { origin: 'https://example.test', href: 'https://example.test/typo3/', assign: (url) => { navigated = url; } },
    addEventListener: (name, listener) => { messages[name] = listener; },
  };
  vm.runInNewContext(source, {
    DocumentService: { ready: () => ({ then: (callback) => callback() }) },
    document: { querySelector: () => root }, window, URL, Date,
    Event: class {}, Severity: { warning: 1 },
    Modal: { types: { iframe: 'iframe' }, sizes: { large: 'large' }, advanced: (options) => { opened = options; }, confirm: () => confirmDialog, dismiss() {} },
    Notification: {}, Tab: {}, Tooltip: {},
  });
  return { input, browse, title, confirmation, confirmDialog, messages, opened: () => opened, navigated: () => navigated };
}

test('page browser applies TYPO3 page identifiers and rejects foreign or unrelated messages', () => {
  const ui = initialize();
  ui.browse.events.click({ preventDefault() {} });
  const browserUrl = new URL(ui.opened().content);
  assert.equal(browserUrl.searchParams.get('mode'), 'db');
  assert.equal(browserUrl.searchParams.get('bparams'), `${ui.input.dataset.insertTarget}|||pages`);
  const data = { actionName: 'typo3:elementBrowser:elementAdded', fieldName: ui.input.dataset.insertTarget, value: 'pages_42', label: '<Fixture>' };
  ui.messages.message({ origin: 'https://foreign.test', data });
  assert.equal(ui.input.value, '');
  ui.messages.message({ origin: 'https://example.test', data: { ...data, fieldName: 'other' } });
  assert.equal(ui.input.value, '');
  ui.messages.message({ origin: 'https://example.test', data: { ...data, value: 'sys_file_42' } });
  assert.equal(ui.input.value, '');
  ui.messages.message({ origin: 'https://example.test', data });
  assert.equal(ui.input.value, '42');
  assert.equal(ui.title.textContent, '<Fixture>');
});

test('record deletion waits for the TYPO3 confirmation event', () => {
  const ui = initialize();
  ui.confirmation.events.click({ preventDefault() {} });
  assert.equal(ui.navigated(), undefined);
  ui.confirmDialog.events['confirm.button.ok']();
  assert.equal(ui.navigated(), ui.confirmation.href);
});
