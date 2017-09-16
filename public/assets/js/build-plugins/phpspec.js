var phpspecPlugin = ActiveBuild.UiPlugin.extend({
    id: 'build-phpspec-errors',
    css: 'col-xs-12',
    title: Lang.get('phpspec'),
    lastData: null,
    displayOnUpdate: false,
    rendered: false,

    register: function() {
        var self = this;
        var query = ActiveBuild.registerQuery('phpspec', -1, {key: 'phpspec'})

        $(window).on('phpspec', function(data) {
            self.onUpdate(data);
        });

        $(window).on('build-updated', function() {
            if (!self.rendered) {
                self.displayOnUpdate = true;
                query();
            }
        });
    },

    render: function() {

        return $('<table class="table table-hover" id="phpspec-data">' +
            '<thead>' +
            '<tr>' +
            '   <th>' + Lang.get('status') + '</th>' +
            '   <th>' + Lang.get('suite') + '</th>' +
            '   <th>' + Lang.get('test') + '</th>' +
            '   <th>' + Lang.get('test_message') + '</th>' +
            '   <th>' + Lang.get('codeception_time') + '</th>' +
            '</tr>' +
            '</thead><tbody></tbody></table>');
    },

    onUpdate: function(e) {
        if (!e.queryData) {
            $('#build-phpspec-errors').hide();
            return;
        }

        this.rendered = true;
        this.lastData = e.queryData;

        var tests = this.lastData[0].meta_value;
        var tbody = $('#phpspec-data tbody');
        tbody.empty();

        for (var i in tests.suites) {
            var test_suite = tests.suites[i];

            for (var k in test_suite.cases) {
                var test_case = test_suite.cases[k];

                var row = $(
                    '<tr>' +
                    '<td>' + ((test_case.status == 'passed') ? '<span class="label label-success">' + Lang.get('success') + '</span>' : '<span class="label label-danger">' + Lang.get('failed') + '</span>') + '</td>' +
                    '<td>' + test_suite.name + '</td>' +
                    '<td>' + test_case.name + '</td>' +
                    '<td>' + (test_case.message ? test_case.message : '') + '</td>' +
                    '<td>' + test_case['time'] + '</td>' +
                    '</tr>'
                );

                tbody.append(row);
            }
        }
        
        // show plugin once preparation of grid is done
        $('#build-phpspec-errors').show();
    }
});

ActiveBuild.registerPlugin(new phpspecPlugin());
