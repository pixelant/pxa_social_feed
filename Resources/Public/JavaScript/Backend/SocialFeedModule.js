define([
	'jquery',
	'bootstrap',
	'TYPO3/CMS/Backend/Modal',
	'TYPO3/CMS/Backend/Severity',
	'TYPO3/CMS/Backend/Notification',
	'TYPO3/CMS/Backend/Utility/MessageUtility',
	'clipboard',
], function ($, bootstrap, Modal, Severity, Notification, MessageUtility, clipboard) {
	return (function ($, bootstrap, Modal, Severity, Notification, MessageUtility, clipboard) {
    /**
     * @private
     *
     * Hold the instance (Singleton Pattern)
     */
    var _socialFeedModuleInstance = null;

    /**
     * Main module JavaScript
     *
     * @return {{init: init}}
     * @constructor
     */
    function SocialFeedModule(settings) {
      /**
       * If was initialized
       *
       * @type {boolean}
       * @private
       */
      var _isRunning = false;

      /**
       * HTML Identifiers
       * @type {{}}
       * @private
       */
      var _domElementsSelectors = {
        confirmationButton: '.delete-action,.confirmation-action',
        selectSocialType: '#select-type',
        socialTypeUrlKeep: '#type-url-',
        winStorageBrowser: '[data-identifier="browse-feeds-storage"]',
        feedsStorageInput: '[data-identifier="feeds-storage-input"]',
        feedsStorageTitle: '[data-identifier="feed-storage-title"]',
        copyRedirectUriButton: '.copy-redirect-uri-button',
        facebookLoginButton: '.facebook-login-link',
      };

      /**
       * run if not running yet
       *
       * @public
       */
      function run() {
        if (_isRunning === false) {
          _bootstrap();
        }

        _isRunning = true;
      }

      /**
       * Init
       * @private
       */
      function _bootstrap() {
        _deleteConfirmation();
        _facebookLoginWindow();
        _changeSocialType();
        _winStorageBrowser();
        _getRedirectUriButtonClick();
        _initToolTip();
        _activateTabs();
      }

      /**
       * Activate the tabs for the backend module
       *
       * @private
       */
      function _activateTabs() {
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
      function _deleteConfirmation() {
        $(_getDomElementIdentifier('confirmationButton')).on('click', function (e) {
          e.preventDefault();

          var $this = $(this);
          var title = $this.data('confirmation-title') || 'Delete';
          var message = $this.data('confirmation-message') || 'Are you sure you want to delete this record ?';

          var url = $this.attr('href'),
            modal = Modal.confirm(title, message, Severity.warning);

          modal.addEventListener('confirm.button.cancel', function () {
            Modal.dismiss(modal);
          });

          modal.addEventListener('confirm.button.ok', function () {
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
      function _facebookLoginWindow() {
        $(_getDomElementIdentifier('facebookLoginButton')).on('click', function (e) {
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
      function _changeSocialType() {
        $(_getDomElementIdentifier('selectSocialType')).on('change', function () {
          var selectSocialType = $(this).find(':selected').val();

          window.location.href = $(_getDomElementIdentifier('socialTypeUrlKeep') + selectSocialType).val();
        });
      }

      /**
       * Copy redirect uri to clipboard
       * @private
       */
      function _getRedirectUriButtonClick() {
        new clipboard(_getDomElementIdentifier('copyRedirectUriButton'));
      }

      /**
       * Load browser pages window
       *
       * @private
       */
      function _winStorageBrowser() {
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

          const fieldElement = _getInsertTarget(e.data.fieldName);
          if (fieldElement) {
            fieldElement.value = e.data.value.split('_').pop();
          }

          const storageTitleElement = document.querySelector(_getDomElementIdentifier('feedsStorageTitle'));
          if (storageTitleElement) {
            storageTitleElement.innerHTML = e.data.label;
          }
        });

        $(_getDomElementIdentifier('winStorageBrowser')).on('click', function () {
          var insertTarget = $(_getDomElementIdentifier('feedsStorageInput')),
            randomIdentifier = Math.floor(Math.random() * 100000 + 1);

          insertTarget.attr('data-insert-target', randomIdentifier);
          _openTypo3WinBrowser('db', randomIdentifier + '|||pages');
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
      function _openTypo3WinBrowser(mode, params) {
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
      function _getDomElementIdentifier(elementIdentifier) {
        return _domElementsSelectors[elementIdentifier] || undefined;
      }

      /**
       * Get insert target
       * @param reference
       * @return {HTMLElement|null}
       * @private
       */
      function _getInsertTarget(reference) {
        return document.querySelector('[data-insert-target="' + reference + '"]');
      }

      /**
       * Get settings
       * @param key
       * @return {*|undefined}
       * @private
       */
      function _getSetting(key) {
        return settings[key] || undefined;
      }

      /**
       * Tool tip
       * @private
       */
      function _initToolTip() {
        $(function () {
          $('[data-toggle="tooltip"]').tooltip();
        });
      }

      /**
       * return public methods
       */
      return {
        run: run,
      };
    }

    return {
      /**
       * @public
       * @static
       *
       * Implement the "Singleton Pattern".
       *
       * @return object
       */
      getInstance: function (settings) {
        if (_socialFeedModuleInstance === null) {
          _socialFeedModuleInstance = new SocialFeedModule(settings);
        }

        return _socialFeedModuleInstance;
      },
    };
  })($, bootstrap, Modal, Severity, Notification, MessageUtility, clipboard);
});
