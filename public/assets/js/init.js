/**
 * @file init.js
 * Initialization of frontend of the application goes here
 *
 * @author Pavel Pavlov <Pavel.Pavlov@alera.ru>
 * @date 12/31/13
 * @time 3:44 AM
 * @license LICENSE.md
 *
 * @package PHPCensor
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
