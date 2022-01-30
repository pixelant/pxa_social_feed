/**
 * Need for ElementBrowser
 */
var setFormValueFromBrowseWin;

define([
	'jquery',
	'bootstrap',
	'TYPO3/CMS/Backend/Modal',
	'TYPO3/CMS/Backend/Severity',
	'TYPO3/CMS/Backend/Notification',
	'clipboard'
], function ($, bootstrap, Modal, Severity, Notification, clipboard) {

	return (function ($, bootstrap, Modal, Severity, Notification, clipboard) {

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
				facebookLoginButton: '.facebook-login-link'
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
				var triggerTabList = [].slice.call(document.querySelectorAll('#tabs a'))
				triggerTabList.forEach(function (triggerEl) {
					var tabTrigger = new bootstrap.Tab(triggerEl)

					triggerEl.addEventListener('click', function (event) {
						event.preventDefault()
						tabTrigger.show()
					})
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

					modal.on('confirm.button.cancel', function () {
						Modal.dismiss(modal);
					});

					modal.on('confirm.button.ok', function () {
						Modal.dismiss(modal);
						window.location.href = url;
					});
				})
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

					var y = window.top.outerHeight / 2 + window.top.screenY - (h / 2);
					var x = window.top.outerWidth / 2 + window.top.screenX - (w / 2);

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
				$(_getDomElementIdentifier('winStorageBrowser')).on('click', function () {
					var insertTarget = $(_getDomElementIdentifier('feedsStorageInput')),
						randomIdentifier = Math.floor((Math.random() * 100000) + 1);

					var width = $(this).data('popup-width') ? $(this).data('popup-width') : 700,
						height = $(this).data('popup-height') ? $(this).data('popup-height') : 750;

					insertTarget.attr('data-insert-target', randomIdentifier);
					_openTypo3WinBrowser('db', randomIdentifier + '|||pages', width, height);
				});
			}

			/**
			 * @private
			 *
			 * opens a popup window with the element browser
			 *
			 * @param mode
			 * @param params
			 * @param width
			 * @param height
			 */
			function _openTypo3WinBrowser(mode, params, width, height) {
				var openedPopupWindow, url;

				url = _getSetting('browserUrl')
					+ '&mode=' + mode + '&bparams=' + params;

				openedPopupWindow = window.open(
					url,
					'Typo3WinBrowser',
					'height=' + height + ',width=' + width + ',status=0,menubar=0,resizable=1,scrollbars=1'
				);
				openedPopupWindow.focus();
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
			 * @return {*|jQuery|HTMLElement}
			 * @private
			 */
			function _getInsertTarget(reference) {
				return $('[data-insert-target="' + reference + '"]');
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
					$('[data-toggle="tooltip"]').tooltip()
				});
			}

			/**
			 * @public
			 *
			 * callback from TYPO3/CMS/Recordlist/ElementBrowser
			 *
			 * @param fieldReference
			 * @param elValue
			 * @param elName
			 * @return void
			 */
			setFormValueFromBrowseWin = function (fieldReference, elValue, elName) {
				var result;
				result = elValue.split('_');

				_getInsertTarget(fieldReference)
					.val(result.pop())
					.trigger('paste');

				$(_getDomElementIdentifier('feedsStorageTitle'))
					.text(elName);
			};

			/**
			 * return public methods
			 */
			return {
				run: run
			}
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
			}
		}

	})($, bootstrap, Modal, Severity, Notification, clipboard);
});
