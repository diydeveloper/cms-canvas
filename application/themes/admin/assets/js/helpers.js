function show_status(text, auto_hide, show_animation)
{
    // Init
    auto_hide = auto_hide !== 'undefined' ? auto_hide : false;
    show_animation = show_animation !== 'undefined' ? show_animation : false;

    var status = $('#ajax_status');

    // Show animated gif
    if (show_animation)
    {
        $('#ajax_status_animation').show();
    }
    else
    {
        $('#ajax_status_animation').hide();
    }

    // Update status text
    $('#ajax_status_text').html(text);

    // Show status box
    if ( ! status.is(':visible'))
    {
        status.fadeIn('slow');
    }

    // Hide status box
    if (auto_hide)
    {
        status.delay(1000).fadeOut('slow');
    }
}

function hide_status()
{
    var status = $('#ajax_status');

    status.fadeOut('slow');
}
