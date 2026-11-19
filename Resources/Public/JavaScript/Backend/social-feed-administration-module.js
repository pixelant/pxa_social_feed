import DocumentService from '@typo3/core/document-service.js';
import Modal from '@typo3/backend/modal.js';
import Notification from '@typo3/backend/notification.js';
import Severity from '@typo3/backend/severity.js';
import { Tab, Tooltip } from 'bootstrap';

class SocialFeedAdministrationModule {
  constructor() {
    DocumentService.ready().then(() => this.initialize());
  }

  initialize() {
    this.root = document.querySelector('.pxa-social-feed-be-adminnistration');
    if (!this.root || this.root.dataset.initialized === 'true') {
      return;
    }
    this.root.dataset.initialized = 'true';

    this.root.querySelectorAll('.delete-action, .confirmation-action').forEach((button) => {
      button.addEventListener('click', (event) => {
        event.preventDefault();
        const modal = Modal.confirm(
          button.dataset.confirmationTitle || 'Delete',
          button.dataset.confirmationMessage || 'Are you sure you want to delete this record?',
          Severity.warning,
        );
        modal.addEventListener('confirm.button.ok', () => {
          Modal.dismiss();
          window.location.assign(button.href);
        });
      });
    });

    this.root.querySelector('#select-type')?.addEventListener('change', (event) => {
      const target = document.getElementById(`type-url-${event.target.value}`);
      if (target?.value) {
        window.location.assign(target.value);
      }
    });

    this.root.querySelectorAll('.facebook-login-link').forEach((link) => {
      link.addEventListener('click', (event) => {
        event.preventDefault();
        window.open(link.href, 'Facebook login', 'width=800,height=800');
      });
    });

    this.root.querySelectorAll('.copy-redirect-uri-button').forEach((button) => {
      button.addEventListener('click', async (event) => {
        event.preventDefault();
        const target = this.root.querySelector(button.dataset.clipboardTarget);
        if (target) {
          try {
            await navigator.clipboard.writeText(target.textContent);
          } catch {
            Notification.error('Copy failed', 'Please copy the displayed redirect URI manually.');
          }
        }
      });
    });

    this.root.querySelectorAll('#tabs a').forEach((link) => {
      link.addEventListener('click', (event) => {
        event.preventDefault();
        Tab.getOrCreateInstance(link).show();
      });
    });
    this.root.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
      Tooltip.getOrCreateInstance(element);
    });

    this.root.querySelector('[data-identifier="browse-feeds-storage"]')?.addEventListener('click', (event) => {
      event.preventDefault();
      const input = this.root.querySelector('[data-identifier="feeds-storage-input"]');
      if (!input || !this.root.dataset.browserUrl) {
        return;
      }
      input.dataset.insertTarget = `pxa-storage-${Date.now()}`;
      const url = new URL(this.root.dataset.browserUrl, window.location.href);
      url.searchParams.set('mode', 'db');
      url.searchParams.set('bparams', `${input.dataset.insertTarget}|||pages`);
      Modal.advanced({ type: Modal.types.iframe, content: url.toString(), size: Modal.sizes.large });
    });

    window.addEventListener('message', (event) => {
      if (event.origin !== window.location.origin || !event.data || typeof event.data.fieldName !== 'string') {
        return;
      }
      const input = this.root.querySelector('[data-identifier="feeds-storage-input"]');
      const selectedPage = /^(?:pages_)?(\d+)$/.exec(String(event.data.value));
      if (!input || !input.dataset.insertTarget || event.data.fieldName !== input.dataset.insertTarget
        || event.data.actionName !== 'typo3:elementBrowser:elementAdded' || !selectedPage) {
        return;
      }
      input.value = selectedPage[1];
      input.dispatchEvent(new Event('change', { bubbles: true }));
      const title = this.root.querySelector('[data-identifier="feed-storage-title"]');
      if (title) {
        title.textContent = event.data.label || '';
      }
    });
  }
}

export default new SocialFeedAdministrationModule();
