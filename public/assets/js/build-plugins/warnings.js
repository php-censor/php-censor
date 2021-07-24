var warningsPlugin = ActiveBuild.UiPlugin.extend({
    id:    'build-warnings-chart',
    css:   'col-xs-12',
    title: Lang.get('quality_trend'),
    keys:  {
        'codeception-errors':            Lang.get('codeception_errors'),
        'php_code_sniffer-errors':       Lang.get('php_code_sniffer_errors'),
        'php_parallel_lint-errors':      Lang.get('php_parallel_lint_errors'),
        'php_tal_lint-errors':           Lang.get('php_tal_lint_errors'),
        'php_unit-errors':               Lang.get('php_unit_errors'),

        'behat-warnings':                Lang.get('behat_warnings'),
        'phan-warnings':                 Lang.get('phan_warnings'),
        'php_code_sniffer-warnings':     Lang.get('php_code_sniffer_warnings'),
        'php_cpd-warnings':              Lang.get('php_cpd_warnings'),
        'php_cs_fixer-warnings':         Lang.get('php_cs_fixer_warnings'),
        'php_docblock_checker-warnings': Lang.get('php_docblock_checker_warnings'),
        'php_mess_detector-warnings':    Lang.get('php_mess_detector_warnings'),
        'php_tal_lint-warnings':         Lang.get('php_tal_lint_warnings'),
        'sensiolabs_insight-warnings':   Lang.get('sensiolabs_insight_warnings'),
        'technical_debt-warnings':       Lang.get('technical_debt_warnings'),
    },
    data:            {},
    displayOnUpdate: false,
    rendered:        false,
    chartData:       null,

    register: function () {
        var self = this;

        var queries = [];
        for (var key in self.keys) {
            queries.push(ActiveBuild.registerQuery(key, -1, {num_builds: 10, key: key}));
        }

        $(window).on(Object.keys(self.keys).join(' '), function (data) {
            self.onUpdate(data);
        });

        $(window).on('build-updated', function (data) {
            if (!self.rendered && data.queryData && data.queryData.status > 1) {
                self.displayOnUpdate = true;
                for (var query in queries) {
                    queries[query]();
                }
            }
        });
    },

    render: function () {
        var self      = this;
        var container = $('<div id="build-warnings" style="width: 100%; height: 300px"></div>');

        container.append('<canvas id="build-warnings-linechart" style="width: 100%; height: 300px"></canvas>');

        $(document).on('shown.bs.tab', function () {
            $('#build-warnings-chart').hide();
            self.drawChart();
        });

        return container;
    },

    onUpdate: function (e) {
        var self   = this;
        var builds = e.queryData;

        if (!builds || !builds.length) {
            return;
        }

        for (var i in builds) {
            var buildId = builds[i]['build_id'];
            var metaKey = builds[i]['meta_key'];
            var metaVal = builds[i]['meta_value'];

            if (!self.data[buildId]) {
                self.data[buildId] = {};
            }

            self.data[buildId][metaKey] = metaVal;
        }

        if (self.displayOnUpdate) {
            self.displayChart();
        }
    },

    displayChart: function () {
        var self      = this;
        self.rendered = true;

        var colors = [
            '#FF0084',
            '#D33724',
            '#FF851B',
            '#F7BE64',
            '#B5BBC8',
            '#555299',
            '#7EDEDE',
            '#00A7D0',
            '#B5BBC8',
            '#001F3F',
        ];

        self.chartData = {
            labels:   [],
            datasets: []
        };

        var i = 0;
        for (var key in self.keys) {
            var show = false;

            for (var buildId in self.data) {
                if (0 < self.data[buildId][key]) {
                    show = true;
                }
            }

            if (show) {
                var color = colors.shift();

                self.chartData.datasets.push({
                    label:           self.keys[key],
                    borderColor:     color,
                    backgroundColor: color,
                    data:            []
                });

                for (var build in self.data) {
                    var value = parseInt(self.data[build][key]) || 0;
                    self.chartData.datasets[i].data.push(value);
                }

                i++;
            }
        }

        for (var build in self.data) {
            self.chartData.labels.push(Lang.get('build') + ' ' + build);
        }

        self.drawChart();
    },

    drawChart: function () {
        var self = this;

        if ($('#information').hasClass('active') && self.chartData) {
            $('#build-warnings-chart').show();

            var ctx                = $("#build-warnings-linechart").get(0).getContext("2d");

            if (window.buildWarningsChart != undefined) {
                window.buildWarningsChart.destroy();
            }
            window.buildWarningsChart = new Chart(ctx, {
                type: 'line',
                data: self.chartData,
                options: {
                    datasetFill: false,
                    multiTooltipTemplate: "<%=datasetLabel%>: <%= value %>"
                }
            });

            Chart.defaults.responsive = true;
        }
    }
});

ActiveBuild.registerPlugin(new warningsPlugin());
