PHPCensor.widgets.lastBuilds = {
    interval: null,

    init: function () {
        $(document).ready(function () {
            PHPCensor.widgets.lastBuilds.load();
        });
    },

    load: function () {
        $.ajax({
            url: APP_URL + 'widget-last-builds',

            success: function (data) {
                $(('#widget-last_builds-container')).html(data);
                PHPCensor.widgets.lastBuilds.interval = setInterval(PHPCensor.widgets.lastBuilds.update, 10000);
            },

            error: PHPCensor.handleFailedAjax
        });
    },

    update: function () {
        $.ajax({
            url: APP_URL + 'widget-last-builds/update',

            success: function (data) {
                $('#timeline-box').html(data);
            },

            error: PHPCensor.handleFailedAjax
        });
    }
};

PHPCensor.widgets.lastBuilds.init();
