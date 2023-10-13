jQuery(document).ready(function($) {
    var page = 1; // Initialize the page number

    $('#load-more').on('click', function(e) {
        e.preventDefault();

        var ajaxurl = $('#load-more-target').data('ajaxurl');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'load_more_action',
                page: page
            },
            success: function(response) {
                $('#load-more-target .row').append(response); // Append the new content
                page++; // Increment the page number
            }
        });
    });
});


jQuery(document).ready(function ($) {
    var offset = 1; 
    var $loadMoreButton = $('#load-more');
    var $loadMoreTarget = $('#load-more-target');
    // var ajax_url = $('[data="data-ajaxurl"]');
    var ajax_url = $('[data-ajaxurl]').data('ajaxurl');

    $('.show_hide').on('click', function () {
        $('#banner').toggle();
    });
    $loadMoreButton.on('click', function (e) {
        e.preventDefault();

        var post_id = $loadMoreButton.data('post-id');

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: {
                action: 'load_acf_card',
                offset: offset,
                post_id: post_id
            },
            success: function (response) {
                var data = JSON.parse(response);

                if (data.more) {
                    $loadMoreTarget.append(data.content);
                    offset = data.offset;
                } else {
                    $loadMoreButton.hide();
                }
            }
            
        });
    });
});