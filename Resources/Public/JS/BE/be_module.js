requirejs(['jquery', 'TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Severity', 'TYPO3/CMS/Backend/Notification'], function( $, Modal, Severity, Notification) {
    var PxaSocialMedia = {};

    PxaSocialMedia.init = function () {
        // delete action
        $(".delete-action").on("click", function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            return PxaSocialMedia.deleteAction(url);
        });

        // change type of socia feed
        $("#select-social-type").on("change", function() {
            var selectSocialType = $(this).find(":selected").val();
            window.location.href=$("#social-type-url-" + selectSocialType).val();
        });

        // on access token link click
        $("#get-instagram-toket").on("click", function(e){
            $(this).hide();
            $("#get-instagram-hidden-tip").show();
        });

        // ajax to get Instagram ID
        $('.tooltip-hold').tooltip();

        $('#get-inst-user-id').on('click', function (e) {
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
            }).done(function(data) {

                Notification[data.action](data.title, data.message, 10);
                if(data.action == 'success') {
                    $this.parent().html(data.userUid);
                }

            }).fail(function (jqXHR) {
                Notification.error('Fail', 'Failed ajax request', 10);
            });
        });

    };

    PxaSocialMedia.deleteAction = function(url) {
        var modal = Modal.confirm('Delete', 'Are you sure you want to delete this record ?', Severity.warning);

        modal.on('confirm.button.cancel', function(){
            Modal.dismiss(modal);
        });

        modal.on('confirm.button.ok', function(){
            Modal.dismiss(modal);
            window.location.href=url;
        });
    };

    $(PxaSocialMedia.init);
    return PxaSocialMedia;
});