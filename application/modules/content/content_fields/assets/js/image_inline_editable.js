jq_admin_toolbar(document).ready( function() {

    jq_admin_toolbar('.cc_image_editable').live('click', function() {
        image = jq_admin_toolbar(this);
        hidden_input = image.next('.cc_hidden_editable');

        window.KCFinder = {
            callBack: function(url) {
                window.KCFinder = null;
                editable_id = hidden_input.attr('id');
                jq_admin_toolbar('#admin-save-status').html('Processing Image...').show();
                jq_admin_toolbar.post(ADMIN_URL + '/content/entries/pre-save-output', {'editable_id': editable_id, 'content': url}, function(response) {
                    if (response.status == 'success') {
                        // Replacing the content will update the hidden input with the new content
                        hidden_input.remove();
                        image.replaceWith(response.content);
                        jq_admin_toolbar('#admin-save-status').html('Processing Image...').delay(500).fadeOut();
                    } else {
                        jq_admin_toolbar('#admin-save-status').hide();
                        alert(response.message);
                    }
                }, 'json');
            }
        };
        var left = (screen.width/2)-(800/2);
        var top = (screen.height/2)-(600/2);
        window.open(BASE_HREF + 'application/themes/admin/assets/js/kcfinder/browse.php?type=images',
            'kcfinder_image', 'status=0, toolbar=0, location=0, menubar=0, ' +
            'directories=0, resizable=1, scrollbars=0, width=800, height=600, top=' + top + ', left=' + left
        );
    });

});