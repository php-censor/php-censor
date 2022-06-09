var coveragePlugin = ActiveBuild.UiPlugin.extend({
    id:              'build-php_unit-coverage-chart',
    css:             'col-xs-12',
    title:           Lang.get('coverage'),
    lastData:        null,
    displayOnUpdate: false,
    rendered:        false,
    chartData:       null,

    register: function () {
        var self  = this;
        var query = ActiveBuild.registerQuery('php_unit-coverage', -1, {num_builds: 20, key: 'php_unit-coverage'});

        $(window).on('php_unit-coverage', function (data) {
            self.onUpdate(data);
        });

        $(window).on('build-updated', function (data) {
            if (data.queryData && data.queryData.status > 1 && !self.rendered) {
                query();
            }
        });
    },

    render: function () {
        var self      = this;
        var container = $('<div id="php_unit-coverage" style="width: 100%; height: 300px"></div>');

        container.append('<canvas id="php_unit-coverage-chart" style="width: 100%; height: 300px"></canvas>');

        $(document).on('shown.bs.tab', function () {
            $('#build-php_unit-coverage-chart').hide();
            self.drawChart();
        });

        return container;
    },

    onUpdate: function (e) {
        if (!e.queryData || $.isEmptyObject(e.queryData)) {
            $('#build-php_unit-coverage').hide();
            return;
        }

        this.lastData = e.queryData;
        this.displayChart();
    },

    displayChart: function () {
        var self      = this;
        var builds    = this.lastData;
        self.rendered = true;

        self.chartData = {
            labels:   [],
            datasets: [
            {
                    label:           Lang.get('classes'),
                    borderColor:     "#555299",
                    backgroundColor: "#555299",
                    data:            [],
                    cubicInterpolationMode: 'monotone',
                    tension: 0.2
            },
                {
                    label:           Lang.get('methods'),
                    borderColor:     "#00A65A",
                    backgroundColor: "#00A65A",
                    data:            [],
                    cubicInterpolationMode: 'monotone',
                    tension: 0.2
            },
                {
                    label:           Lang.get('lines'),
                    borderColor:     "#8AA4AF",
                    backgroundColor: "#8AA4AF",
                    data:            [],
                    cubicInterpolationMode: 'monotone',
                    tension: 0.2
            }
            ]
        };

        for (var i in builds) {
            self.chartData.labels.push(Lang.get('build') + ' ' + builds[i].build_id);
            self.chartData.datasets[0].data.push(builds[i].meta_value.classes);
            self.chartData.datasets[1].data.push(builds[i].meta_value.methods);
            self.chartData.datasets[2].data.push(builds[i].meta_value.lines);
        }

        self.drawChart();
    },

    drawChart: function () {
        var self = this;

        if ($('#information').hasClass('active') && self.chartData && self.lastData) {
            $('#build-php_unit-coverage-chart').show();

            var ctx = $("#php_unit-coverage-chart").get(0).getContext("2d");

            if (window.chart != undefined) {
                window.chart.destroy();
            }
            window.chart = new Chart(ctx, {
                responsive: true,
                type: 'line',
                data: self.chartData,
                options: {
                    scaleOverride :       true,
                    scaleSteps :          10,
                    scaleStepWidth :      10,
                    scaleStartValue :     0,
                    datasetFill:          false,
                    multiTooltipTemplate: "<%=datasetLabel%>: <%= value %>",
                    maintainAspectRatio:  false
                }
            });

            Chart.defaults.responsive = true;
        }
    }
});

ActiveBuild.registerPlugin(new coveragePlugin());
