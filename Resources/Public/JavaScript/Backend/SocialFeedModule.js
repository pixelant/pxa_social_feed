/**
 * Need for ElementBrowser
 */
var setFormValueFromBrowseWin;

define(['jquery',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/Backend/Severity',
    'TYPO3/CMS/Backend/Notification'
], function ($, Modal, Severity, Notification) {

    return (function ($, Modal, Severity, Notification) {

        /**
         * @private
         *
         * Hold the instance (Singleton Pattern)
         */
        var _socialFeedModuleInstance = null;

        /**
         * Main mdoule JS
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
                deleteButton: '.delete-action',
                selectSocialType: '#select-social-type',
                socialTypeUrlKeep: '#social-type-url-',
                toolTip: '.tooltip-hold',
                instagramTokenButton: '#get-instagram-toket',
                instagramTokenTip: '#get-instagram-hidden-tip',
                instagramGetUserIdButton: '#get-inst-user-id',
                winStorageBrowser: '[data-identifier="browse-feeds-storage"]',
                feedsStorageInput: '[data-identifier="feeds-storage-input"]',
                feedsStorageTitle: '[data-identifier="feed-storage-title"]',
                migrateRecordsWrapper: '.migrate-records-wrapper'
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
                _changeSocialType();
                _toolTip();
                _getInstagramToketClick();
                _loadInstagramUserIdButton();
                _winStorageBrowser();
            }

            /**
             * Initialize tool tip info
             * @private
             */
            function _toolTip() {
                $(_getDomElementIdentifier('toolTip')).tooltip();
            }

            /**
             * If user try to delete something
             *
             * @private
             */
            function _deleteConfirmation() {
                $(_getDomElementIdentifier('deleteButton')).on('click', function (e) {
                    e.preventDefault();
                    var url = $(this).attr('href'),
                        modal = Modal.confirm('Delete', 'Are you sure you want to delete this record ?', Severity.warning);

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
             * Switch to different social type
             * @private
             */
            function _changeSocialType() {
                $(_getDomElementIdentifier('selectSocialType')).on('change', function () {
                    var selectSocialType = $(this).find(':selected').val();

                    window.location.href = $(_getDomElementIdentifier('socialTypeUrlKeep') + selectSocialType).val();
                });
            }

            /**
             * Try to fetch instagram user id
             * @private
             */
            function _loadInstagramUserIdButton() {
                $(_getDomElementIdentifier('instagramGetUserIdButton')).on('click', function (e) {
                    e.preventDefault();

                    var $this = $(this),
                        configuration = $this.data('uid'),
                        ajaxUrl = TYPO3.settings.ajaxUrls['pixelant_pxasocialfeed_loadinstuserid'];

                    $this.prop('disabled', true);

                    $.ajax({
                        url: ajaxUrl,
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        data: {
                            'configuration': configuration
                        }
                    }).done(function (data) {
                        Notification[data.action](data.title, data.message, 10);
                        if (data.action === 'success') {
                            $this.parent().html(data.userUid);
                        }

                    }).fail(function (jqXHR) {
                        Notification.error('Fail', 'Failed ajax request', 10);
                    });
                });
            }

            /**
             * Instagram token get button
             * @private
             */
            function _getInstagramToketClick() {
                $(_getDomElementIdentifier('instagramTokenButton')).on('click', function () {
                    $(this).hide();
                    $(_getDomElementIdentifier('instagramTokenTip')).show();
                });
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
                return $('[data-insert-target="'+reference+'"]');
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
             * @public
             *
             * callback from TYPO3/CMS/Recordlist/ElementBrowser
             *
             * @param fieldReference
             * @param elValue
             * @param elName
             * @return void
             */
            setFormValueFromBrowseWin = function(fieldReference, elValue, elName) {
                var result;
                result = elValue.split('_');

                _getInsertTarget(fieldReference)
                    .val(result.pop())
                    .trigger('paste');

                $(_getDomElementIdentifier('feedsStorageTitle'))
                    .text(elName)
                    .closest('.table-fit').removeClass('hidden');

                $(_getDomElementIdentifier('migrateRecordsWrapper')).slideDown();
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

    })($, Modal, Severity, Notification);
});