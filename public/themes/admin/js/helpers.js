function show_status(text, auto_hide, show_animation)
{
    // Init
    auto_hide = auto_hide !== 'undefined' ? auto_hide : false;
    show_animation = show_animation !== 'undefined' ? show_animation : false;

    var status = $('#ajax_status');

    // Show animated gif
    if (show_animation) {
        $('#ajax_status_animation').show();
    } else {
        $('#ajax_status_animation').hide();
    }

    // Update status text
    $('#ajax_status_text').html(text);

    // Show status box
    if ( ! status.is(':visible')) {
        status.fadeIn('slow');
    }

    // Hide status box
    if (auto_hide) {
        status.delay(1000).fadeOut('slow');
    }
}

function hide_status()
{
    var status = $('#ajax_status');

    status.fadeOut('slow');
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $('#current_user').click(function() {
        if ($('#current_user_dropdown').is(":visible")) {
            $('#current_user_dropdown').hide();
            $('#current_user_box_pointer').hide();
            $('#current_user_box_pointer_white').hide();
        } else {
            $('#current_user_dropdown').show();
            $('#current_user_box_pointer').show();
            $('#current_user_box_pointer_white').show();
        }
    });

    $(document).mouseup( function (e) {
        if ($('#current_user_dropdown').is(":visible") 
            && $(e.target).parents('#current_user_dropdown').length == 0 
            && $(e.target).parents('#current_user').length == 0
        ) {
            $('#current_user_dropdown').hide();
            $('#current_user_box_pointer').hide();
            $('#current_user_box_pointer_white').hide();
        }
    });

    $('.notification .dropdown_close').click(function() {
        $(this).parent('.notification').remove();
    });

    $('.sortable').click( function() {
        sort = $(this);

        form = $('<form>').attr({
            method: 'post'
        });

        $('<input>').attr({
            type: 'hidden',
            name: 'orderBy[column]',
            value: sort.attr('rel')
        }).appendTo(form);

        $('<input>').attr({
            type: 'hidden',
            name: 'orderBy[sort]',
            value: (sort.hasClass('asc')) ? 'desc' : 'asc'
        }).appendTo(form);

        $('<input>').attr({
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }).appendTo(form);

        form.submit();

        return false;
    });

    $('.row_link').click(function(e) {
        if ($(e.target).closest(".no_row_link").length > 0) {
            e.stopPropagation();
        } else {
            window.document.location = $(this).data("href");
        }
    });

    $('.actions_link').click(function (e) {
        if ($(this).closest('a').siblings('.actions_dropdown').is(":visible")) {
            $(this).closest('a').removeClass('selected');
            $('.actions_dropdown').hide();
        } else {
            $('.actions_link').removeClass('selected');
            $('.actions_dropdown').hide();
            $(this).closest('a').addClass('selected');
            $(this).closest('a').siblings('.actions_dropdown').show();
        }
    });

    $(document).mouseup(function (e) {
        if ($('.actions_dropdown').is(":visible") 
            && $(e.target).closest('.actions_dropdown').length == 0
            && $(e.target).closest('a').hasClass('actions_link') == false
        ) {
            $('.actions_link').removeClass('selected');
            $('.actions_dropdown').hide();
        }
    });

    $('.delete_item').click(function() {
        if (confirm('Delete cannot be undone! Are you sure you want to do this item?')) {
            $('<form method="post" action="' + $(this).data('href') + '">'
                + '<input type="hidden" name="selected[]" value="' + $(this).data('id') + '" />'
                + '<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '" />'
                + '</form>'
            ).appendTo('body').submit();
        } else {
            return false;
        }
    });
});