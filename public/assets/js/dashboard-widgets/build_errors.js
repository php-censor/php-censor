PHPCensor.widgets.buildErrors = {
    interval: null,

    init: function () {
        $(document).ready(function () {
            PHPCensor.widgets.buildErrors.load();
        });
    },

    load: function () {
        $.ajax({
            url: APP_URL + 'widget-build-errors',

            success: function (data) {
                $(('#widget-build_errors-container')).html(data);
                if (REALTIME_UI) {
                    PHPCensor.widgets.buildErrors.interval = setInterval(PHPCensor.widgets.buildErrors.update, 10000);
                }
            },

            error: PHPCensor.handleFailedAjax
        });
    },

    update: function () {
        $.ajax({
            url: APP_URL + 'widget-build-errors/update',

            success: function (data) {
                $(('#dashboard-build-errors')).html(data);
            },

            error: PHPCensor.handleFailedAjax
        });
    }
};

PHPCensor.widgets.buildErrors.init();
