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
        function SocialFeedModule() {

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
                instagramGetUserIdButton: '#get-inst-user-id'
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
             * Instagram toket get button
             * @private
             */
            function _getInstagramToketClick() {
                $(_getDomElementIdentifier('instagramTokenButton')).on('click', function () {
                    $(this).hide();
                    $(_getDomElementIdentifier('instagramTokenTip')).show();
                });
            }

            /**
             * Get elector
             * @param elementIdentifier
             * @return {*|undefined}
             * @private
             */
            function _getDomElementIdentifier(elementIdentifier) {
                return _domElementsSelectors[elementIdentifier] || undefined;
            }

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
            getInstance: function () {
                if (_socialFeedModuleInstance === null) {
                    _socialFeedModuleInstance = new SocialFeedModule();
                }

                return _socialFeedModuleInstance;
            }
        }

    })($, Modal, Severity, Notification);
});