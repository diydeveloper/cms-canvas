$(document).ready( function() {

    $('.choose_file').click( function() {
        var link = $(this);

        window.KCFinder = {
            callBack: function(url) {
                window.KCFinder = null;
                link.parent().find('.filename').html(url);
                link.parent().find('.hidden_file').val(url);
            }
        };
        var left = (screen.width/2)-(800/2);
        var top = (screen.height/2)-(600/2);
        window.open(THEME_URL + '/assets/js/kcfinder/browse.php?type=files',
            'kcfinder_image', 'status=0, toolbar=0, location=0, menubar=0, ' +
            'directories=0, resizable=1, scrollbars=0, width=800, height=600, top=' + top + ', left=' + left
        );
    });

    $('.remove_file').click( function() {
        var link = $(this);

        link.parent().find('.filename').html('No File Added');
        link.parent().find('.hidden_file').attr('value', '');
    });

});
