requirejs(['jquery', 'TYPO3/CMS/Backend/Modal', 'TYPO3/CMS/Backend/Severity'], function( $, Modal, Severity) {
    var PxaSocialMedia = {};

    PxaSocialMedia.init = function () {
        $(".delete-action").on("click", function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            return PxaSocialMedia.deleteAction(url);
        });
        $("#select-social-type").on("change", function() {
            var selectSocialType = $(this).find(":selected").val();
            window.location.href=$("#social-type-url-" + selectSocialType).val();
        });
        $("#get-instagram-toket").on("click", function(e){
            $(this).hide();
            $("#get-instagram-hidden-tip").show();
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