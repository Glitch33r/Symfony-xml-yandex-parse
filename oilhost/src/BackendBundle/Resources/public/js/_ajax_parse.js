$(function () {
    $('#parse-btn').click(function () {
        var overlay = $('.parse-loading-overlay'),
            button = $(this);

        $.ajax({
            url: '/app_dev.php/admin/parse_action',
            date: null,
            beforeSend: function () {
                overlay.addClass('visible');
            },
            error: function () {
                button.attr('disabled', 'disabled');
                overlay.removeClass('visible');
                $('<p class="text-danger">"Oh no, all is going wrong! Bad XML file!"</p>').appendTo('.content');
                setTimeout('location.reload()', 3000);
            },
            complete: function () {
                button.attr('disabled', 'disabled');
                $('<p class="text-success">Successfully loaded! You will be redirected to <a href="/oilhost/web/app_dev.php/admin/dashboard">Dashboard</a> in 3 seconds</p>').appendTo('.content');
                overlay.removeClass('visible');
                setTimeout("window.location.href = '/app_dev.php/admin/dashboard'", 3000);
            }

        });
        return false;
    });
});
