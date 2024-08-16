import DocumentService from '@typo3/core/document-service.js';
import Notification from '@typo3/backend/notification.js';
import Modal from '@typo3/backend/modal.js';
import $ from 'jquery';
import Severity from '@typo3/backend/severity.js';
import bootstrap from '@typo3/backend/bootstrap';
/*
 * file created by Abdellatif Landolsi <abdellatif@landolsi.de> for TYPO3 v12.4
 * Date: 2023-10-19
 */

class SocialFeedAdministrationModule {
  constructor() {
    this._isRunning = false;
    this._domElementsSelectors = {
      confirmationButton: '.delete-action,.confirmation-action',
      selectSocialType: '#select-type',
      socialTypeUrlKeep: '#type-url-',
      winStorageBrowser: '[data-identifier="browse-feeds-storage"]',
      feedsStorageInput: '[data-identifier="feeds-storage-input"]',
      feedsStorageTitle: '[data-identifier="feed-storage-title"]',
      copyRedirectUriButton: '.copy-redirect-uri-button',
      facebookLoginButton: '.facebook-login-link',
    };
    DocumentService.ready().then(() => {
      this.initialize();
    });
  }
  initialize() {
    if (this._isRunning === false) {
      this._bootstrap();
    }
    _isRunning = true;
  }

  _bootstrap() {
    this._deleteConfirmation();
    this._facebookLoginWindow();
    this._changeSocialType();
    this._winStorageBrowser();
    this._getRedirectUriButtonClick();
    this._initToolTip();
    this._activateTabs();
  }

  /**
   * Activate the tabs for the backend module
   *
   * @private
   */
  _activateTabs() {
    let triggerTabList = [].slice.call(document.querySelectorAll('#tabs a'));
    triggerTabList.forEach(function (triggerEl) {
      let tabTrigger = new bootstrap.Tab(triggerEl);

      triggerEl.addEventListener('click', function (event) {
        event.preventDefault();
        tabTrigger.show();
      });
    });
  }

  /**
   * If user try to delete something
   *
   * @private
   */
  _deleteConfirmation() {
    this._getDomElementIdentifier('confirmationButton').on('click', function (e) {
      e.preventDefault();

      var $this = $(this);
      var title = $this.data('confirmation-title') || 'Delete';
      var message = $this.data('confirmation-message') || 'Are you sure you want to delete this record ?';

      var url = $this.attr('href'),
        modal = Modal.confirm(title, message, Severity.warning);

      modal.on('confirm.button.cancel', function () {
        Modal.dismiss(modal);
      });

      modal.on('confirm.button.ok', function () {
        Modal.dismiss(modal);
        window.location.href = url;
      });
    });
  }

  /**
   * Show window with facebook login
   *
   * @private
   */
  _facebookLoginWindow() {
    this._getDomElementIdentifier('facebookLoginButton').on('click', function (e) {
      e.preventDefault();

      var $this = $(this);
      var w = 800;
      var h = 800;

      var y = window.top.outerHeight / 2 + window.top.screenY - h / 2;
      var x = window.top.outerWidth / 2 + window.top.screenX - w / 2;

      window.open($this.attr('href'), 'Facebook login', 'height=' + h + ',width=' + w + 'top=' + y + ', left=' + x);
    });
  }

  /**
   * Switch to different social type
   *
   * @private
   */
  _changeSocialType() {
    this._getDomElementIdentifier('selectSocialType').on('change', function () {
      var selectSocialType = $(this).find(':selected').val();

      window.location.href = $(_getDomElementIdentifier('socialTypeUrlKeep') + selectSocialType).val();
    });
  }

  /**
   * Copy redirect uri to clipboard
   * @private
   */
  _getRedirectUriButtonClick() {
    new clipboard(this._getDomElementIdentifier('copyRedirectUriButton'));
  }

  /**
   * Load browser pages window
   *
   * @private
   */
  _winStorageBrowser() {
    window.addEventListener('message', function (e) {
      if (!MessageUtility.MessageUtility.verifyOrigin(e.origin)) {
        throw 'Denied message sent by ' + e.origin;
      }

      if (typeof e.data.fieldName === 'undefined') {
        throw 'fieldName not defined in message';
      }

      if (typeof e.data.value === 'undefined') {
        throw 'value not defined in message';
      }

      const fieldElement = this._getInsertTarget(e.data.fieldName);
      if (fieldElement) {
        fieldElement.value = e.data.value;
      }

      const storageTitleElement = document.querySelector(this._getDomElementIdentifier('feedsStorageTitle'));
      if (storageTitleElement) {
        storageTitleElement.innerHTML = e.data.label;
      }
    });

    this._getDomElementIdentifier('winStorageBrowser').on('click', function () {
      var insertTarget = this._getDomElementIdentifier('feedsStorageInput'),
        randomIdentifier = Math.floor(Math.random() * 100000 + 1);

      insertTarget.attr('data-insert-target', randomIdentifier);
      this._openTypo3WinBrowser('db', randomIdentifier + '|||pages');
    });
  }

  /**
   * @private
   *
   * opens a popup window with the element browser
   *
   * @param mode
   * @param params
   */
  _openTypo3WinBrowser(mode, params) {
    const url = _getSetting('browserUrl') + '&mode=' + mode + '&bparams=' + params;
    Modal.advanced({
      type: Modal.types.iframe,
      content: url,
      size: Modal.sizes.large,
    });
  }

  /**
   * Get selector
   * @param elementIdentifier
   * @return {*|undefined}
   * @private
   */
  _getDomElementIdentifier(elementIdentifier) {
    return _domElementsSelectors[elementIdentifier] || undefined;
  }

  /**
   * Get insert target
   * @param reference
   * @return {HTMLElement|null}
   * @private
   */
  _getInsertTarget(reference) {
    return document.querySelector('[data-insert-target="' + reference + '"]');
  }

  /**
   * Get settings
   * @param key
   * @return {*|undefined}
   * @private
   */
  _getSetting(key) {
    return settings[key] || undefined;
  }
}

export default new SocialFeedAdministrationModule();
