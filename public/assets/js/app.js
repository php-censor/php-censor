var PHPCensor = {
    intervals: {},
    widgets: {},

    init: function () {
        $(document).ready(function () {
            // Update latest builds every 5 seconds:
            PHPCensor.getBuilds();
            PHPCensor.intervals.getBuilds = setInterval(PHPCensor.getBuilds, 5000);

            // Update latest project builds every 10 seconds:
            if (typeof PROJECT_ID != 'undefined') {
                PHPCensor.intervals.getProjectBuilds = setInterval(PHPCensor.getProjectBuilds, 10000);
            }
        });

        $(window).on('builds-updated', function (e, data) {
            PHPCensor.updateHeaderBuilds(data);
        });
    },

    getBuilds: function () {
        $.ajax({
            url: APP_URL + 'build/ajax-queue',

            success: function (data) {
                $(window).trigger('builds-updated', [data]);
            },

            error: PHPCensor.handleFailedAjax
        });
    },

    getProjectBuilds: function () {
        $.ajax({
            url: APP_URL + 'project/ajax-builds/' + PROJECT_ID + '?branch=' + PROJECT_BRANCH + '&environment=' + PROJECT_ENVIRONMENT + '&per_page=' + PER_PAGE + '&page=' + PAGE,

            success: function (data) {
                $('#latest-builds').html(data);
            },

            error: PHPCensor.handleFailedAjax
        });
    },

    updateHeaderBuilds: function (data) {
        $('.app-pending-list').empty();
        $('.app-running-list').empty();

        if (!data.pending.count) {
            $('.app-pending').hide();
        } else {
            $('.app-pending').show();
            $('.app-pending .header').text(Lang.get('n_builds_pending', data.pending.count));

            $.each(data.pending.items, function (idx, build) {
                $('.app-pending-list').append(build.header_row);
            });
        }

        if (!data.running.count) {
            $('.app-running').hide();
        } else {
            $('.app-running').show();
            $('.app-running .header').text(Lang.get('n_builds_running', data.running.count));

            $.each(data.running.items, function (idx, build) {
                $('.app-running-list').append(build.header_row);
            });
        }

    },

    get: function (uri, success) {

        $.ajax({
            url: window.APP_URL + uri,

            success: function (data) {
                success();
            },

            error: PHPCensor.handleFailedAjax
        });
    },

    handleFailedAjax: function (xhr) {
        if (xhr.status == 401) {
            window.location.href = window.APP_URL + 'session/login';
        }
    }
};

PHPCensor.init();

function handleFailedAjax(xhr) {
    PHPCensor.handleFailedAjax(xhr);
}

/**
 * See https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function/bind
 * for the details of code below
 */
if (!Function.prototype.bind) {
    Function.prototype.bind = function (oThis) {
        if (typeof this !== "function") {
            // closest thing possible to the ECMAScript 5 internal IsCallable function
            throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
        }

        var aArgs = Array.prototype.slice.call(arguments, 1),
            fToBind = this,
            fNOP = function () {
            },
            fBound = function () {
                return fToBind.apply(this instanceof fNOP && oThis
                    ? this
                    : oThis,
                    aArgs.concat(Array.prototype.slice.call(arguments)));
            };

        fNOP.prototype = this.prototype;
        fBound.prototype = new fNOP();

        return fBound;
    };
}

/**
 * Used for delete buttons in the system, just to prevent accidental clicks.
 */
function confirmDelete(url, reloadAfter) {

    var dialog = new PHPCensorConfirmDialog({
        title: Lang.get('confirm_title'),
        message: Lang.get('confirm_message'),
        confirmBtnCaption: Lang.get('confirm_ok'),
        cancelBtnCaption: Lang.get('confirm_cancel'),
        /*
         confirm-btn click handler
         */
        confirmed: function (e) {
            var dialog = this;
            e.preventDefault();

            /*
             Call delete URL
             */
            $.ajax({
                url: url,
                success: function (data) {
                    if (reloadAfter) {
                        dialog.onClose = function () {
                            window.location.reload();
                        };
                    }

                    dialog.showStatusMessage(Lang.get('confirm_success'), 500);
                },
                error: function (data) {
                    dialog.showStatusMessage(Lang.get('confirm_failed') + data.statusText);

                    if (data.status == 401) {
                        handleFailedAjax(data);
                    }
                }
            });
        }
    });

    dialog.show();
    return dialog;
}

/**
 * PHPCensorConfirmDialog constructor options object
 * @type {{message: string, title: string, confirmBtnCaption: string, cancelBtnCaption: string, confirmed: Function}}
 */
var PHPCensorConfirmDialogOptions = {
    message: 'Are you sure?',
    title: 'Confirmation',
    confirmBtnCaption: 'Ok',
    cancelBtnCaption: 'Cancel',
    confirmed: function (e) {
        this.close();
    }
};

var PHPCensorConfirmDialog = Class.extend({
    /**
     * @private
     * @var {bool} Determines whether the dialog has been confirmed
     */
    confirmed: false,

    /**
     * @param {PHPCensorConfirmDialogOptions} options
     */
    init: function (options) {

        options = options ? $.extend(PHPCensorConfirmDialogOptions, options) : PHPCensorConfirmDialogOptions;

        if (!$('#confirm-dialog').length) {
            /*
             Add the dialog html to a page on first use. No need to have it there before first use.
             */
            $('body').append(
                '<div class="modal fade" id="confirm-dialog">'
                + '<div class="modal-dialog">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>'
                + '<h4 class="modal-title"></h4>'
                + '</div>'
                + '<div class="modal-body">'
                + '<p></p>'
                + '</div>'
                + '<div class="modal-footer">'
                + '<button id="confirm-cancel" type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>'
                + '<button id="confirm-ok" type="button" class="btn btn-danger"></button>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>'
            );
        }

        /*
         Define dialog controls
         */
        this.$dialog = $('#confirm-dialog');
        this.$cancelBtn = this.$dialog.find('#confirm-cancel');
        this.$confirmBtn = this.$dialog.find('#confirm-ok');
        this.$title = this.$dialog.find('h4.modal-title');
        this.$body = this.$dialog.find('div.modal-body');

        /*
         Initialize its values
         */
        this.$title.html(options.title ? options.title : PHPCensorConfirmDialogOptions.title);
        this.$body.html(options.message ? options.message : PHPCensorConfirmDialogOptions.message);
        this.$confirmBtn.html(
            options.confirmBtnCaption ? options.confirmBtnCaption : PHPCensorConfirmDialogOptions.confirmBtnCaption
        );

        this.$cancelBtn.html(
            options.cancelBtnCaption ? options.cancelBtnCaption : PHPCensorConfirmDialogOptions.cancelBtnCaption
        );

        /*
         Events
         */
        this.confirmBtnClick = options.confirmed;

        /*
         Re-bind handlers
         */
        this.$confirmBtn.unbind('click');
        this.$confirmBtn.click(this.onConfirm.bind(this));

        this.$confirmBtn.unbind('hidden.bs.modal');

        /*
         Bind the close event of the dialog to the set of onClose* methods
         */
        this.$dialog.on('hidden.bs.modal', function () {
            this.onClose()
        }.bind(this));
        this.$dialog.on('hidden.bs.modal', function () {
            if (this.confirmed) {
                this.onCloseConfirmed();
            } else {
                this.onCloseCanceled();
            }
        }.bind(this));

        /*
         Restore state if was changed previously
         */
        this.$cancelBtn.show();
        this.$confirmBtn.show();
        this.confirmed = false;
    },

    /**
     * Show dialog
     */
    show: function () {
        this.$dialog.modal('show');
    },

    /**
     * Hide dialog
     */
    close: function () {
        this.$dialog.modal('hide');
    },

    onConfirm: function (e) {
        this.confirmed = true;
        $(this).attr('disabled', 'disabled');
        this.confirmBtnClick(e);
    },

    /**
     * Called only when confirmed dialog was closed
     */
    onCloseConfirmed: function () {
    },

    /**
     * Called only when canceled dialog was closed
     */
    onCloseCanceled: function () {
    },

    /**
     * Called always when the dialog was closed
     */
    onClose: function () {
    },

    showStatusMessage: function (message, closeTimeout) {
        this.$confirmBtn.hide();
        this.$cancelBtn.hide();

        /*
         Status message
         */
        this.$body.html(message);

        if (closeTimeout) {
            window.setTimeout(function () {
                /*
                 Hide the dialog
                 */
                this.close();
            }.bind(this), closeTimeout);
        }
    }
});

var Lang = {
    get: function () {
        var args = Array.prototype.slice.call(arguments);
        var string = args.shift();

        if (STRINGS[string]) {
            args.unshift(STRINGS[string]);
            return sprintf.apply(sprintf[0], args);
        }

        return string;
    }
};
