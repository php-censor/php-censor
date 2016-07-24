/**
 * Initialization of frontend of the application goes here
 *
 * @author Pavel Pavlov <Pavel.Pavlov@alera.ru>
 */

$(function () {
    $('#latest-builds').on('latest-builds:reload', bindAppDeleteEvents);
    $('#latest-builds').trigger('latest-builds:reload');
});

function bindAppDeleteEvents () {
    $('.app-delete-build').on('click', function (e) {
        e.preventDefault();

        confirmDelete(e.target.href, 'Build').onClose = function () {
            window.location.reload();
        };

        return false;
    });

    $('.app-delete-user').on('click', function (e) {
        e.preventDefault();

        confirmDelete(e.target.href, 'User', true);

        return false;
    });
}
