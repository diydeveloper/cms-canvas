$(document).ready( function() {

    $('.choose_image').click( function() {
        var link = $(this);

        window.KCFinder = {
            callBack: function(url) {
                window.KCFinder = null;
                $.post(ADMIN_URL + '/content/entry/create-thumbnail', {'image_path': url}, function(image_path) {
                    link.parent().find('.image_thumbnail').attr('src', image_path);
                    link.parent().find('.hidden_file').val(url);
                });
            }
        };
        var left = (screen.width/2)-(1000/2);
        var top = (screen.height/2)-(600/2);
        window.open(THEME_URL + '/js/kcfinder/browse.php?type=images',
            'kcfinder_image', 'status=0, toolbar=0, location=0, menubar=0, ' +
            'directories=0, resizable=1, scrollbars=0, width=1000, height=600, top=' + top + ', left=' + left
        );
    });

    $('.remove_image').click( function() {
        var link = $(this);
        $.post(ADMIN_URL + '/content/entry/create-thumbnail', {'image_path': ' '}, function(image_path) {
            link.parent().find('.image_thumbnail').attr('src', image_path);
        });
        $(this).parent().find('.hidden_file').attr('value', '');
    });

});
