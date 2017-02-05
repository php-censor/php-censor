var phptalPlugin = ActiveBuild.UiPlugin.extend({
    id: 'build-phptal',
    css: 'col-xs-12',
    title: 'PHPTAL Lint',
    lastData: null,
    box: true,
    rendered: false,

    register: function() {
        var self = this;
        var query = ActiveBuild.registerQuery('phptallint-data', -1, {key: 'phptallint-data'})

        $(window).on('phptallint-data', function(data) {
            self.onUpdate(data);
        });

        $(window).on('build-updated', function() {
            if (!self.rendered) {
                query();
            }
        });
    },

    render: function() {
        return $('<table class="table table-hover" id="phptal-data">' +
            '<thead>' +
            '<tr>' +
            '   <th>' + Lang.get('file') + '</th>' +
            '   <th>' + Lang.get('line') + '</th>' +
            '   <th>' + Lang.get('message') + '</th>' +
            '</tr>' +
            '</thead><tbody></tbody></table>');
    },

    onUpdate: function(e) {
        if (!e.queryData) {
            $('#build-phptal').hide();
            return;
        }

        this.rendered = true;
        this.lastData = e.queryData;

        var errors = this.lastData[0].meta_value;
        var tbody = $('#phptal-data tbody');
        tbody.empty();

        if (errors.length == 0) {
            $('#build-phptal').hide();
            return;
        }

        for (var i in errors) {
            var file = errors[i].file;

            if (ActiveBuild.fileLinkTemplate) {
                var fileLink = ActiveBuild.fileLinkTemplate.replace('{FILE}', file);
                fileLink = fileLink.replace('{LINE}', errors[i].line);

                file = '<a target="_blank" href="'+fileLink+'">' + file + '</a>';
            }

            var row = $('<tr>' +
                '<td>'+file+'</td>' +
                '<td>'+errors[i].line+'</td>' +
                '<td>'+errors[i].message+'</td></tr>');

            if (errors[i].type == 'error') {
                row.addClass('danger');
            }

            tbody.append(row);
        }

        $('#build-phptal').show();
    }
});

ActiveBuild.registerPlugin(new phptalPlugin());
