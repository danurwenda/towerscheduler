$(function () {
    function initChart() {
        $('#master').highcharts('StockChart', {
            scrollbar: {
                enabled: true
            },
            navigator: {
                enabled: true
            },
            rangeSelector: {
                selected: 1
            }
        });
    }
    initChart();
//define class of NodeSeries, a wrapper for relation between tree node + series inside
    var NodeSeries = function (config) {
        this.id = config.id;
        this.name = config.name;
        this.children = config.children;
        this.data = config.data;
        this.yAxis = config.id + ' axis';
        this.axisOptions = {
            id: config.id + ' axis',
            visible: false
        };
    };
//show/hide all leave series in chart
    NodeSeries.prototype.showAllSeries = function (show) {
        if (show) {
            if (this.children) {
                $.each(this.children, function (i, id) {
                    addSeries(id);
                })
            } else {
                //base
                //show itself
                //check on chart
                var chart = $('#master').highcharts();
                var series = chart.get(this.id);
                if (series) {
                    //sudah ada
                    //show it
                    series.show();
                } else {
                    //belum ada, add to chart
                    chart.addAxis(this.axisOptions);
                    chart.addSeries(this);
                }
            }
        } else {
            //hide
            if (this.children) {
                $.each(this.children, function (i, id) {
                    mySeries[id].showAllSeries(false);
                })
            } else {
                var chart = $('#master').highcharts();
                var toHide = chart.get(this.id);
                toHide.hide();
            }
        }
    };
    var indikator_tree = new dhtmlXTreeObject("treeboxbox_tree", "100%", "100%", 0);
    indikator_tree.setImagePath(base_url + "assets/modules/ace/js/dhtml/codebase/imgs/dhxtree_skyblue/");
    indikator_tree.enableCheckBoxes(true);
    indikator_tree.enableThreeStateCheckboxes(true);
    indikator_tree.enableDragAndDrop(true, false);
    indikator_tree.setXMLAutoLoading("compare/get_child");
    indikator_tree.setDataMode('json');
    $('#reset-button').click(function () {
        //uncheck all checked nodes on tree
        var checked = indikator_tree.getAllChecked();

        $.each(checked.split(','), function (i, id) {
            indikator_tree.setCheck(id, false)
        })

        //ga mau susah, recreate chart
        $('#master').highcharts().destroy();
        initChart();
    });
//array of NodeSeries
    var mySeries = [];
//attach event
    indikator_tree.attachEvent("onCheck", function (id, state) {
        if (state) {
            addSeries(id);
        } else {
            //pasti sudah ada di series
            mySeries[id].showAllSeries(false);
        }
    });
//load first level
    indikator_tree.load("compare/get_root", "json");

//given an id, add all leaves from that id to the chart
    function addSeries(id) {
        //check apakah sudah ada di series
        if (mySeries[id]) {
            //sudah ada
            mySeries[id].showAllSeries(true);
        } else {
            //belum ada
            //tarik json
            $.getJSON(base_url + "compare/get_tree/" + id, function (d) {
                //d is the root, could be a leaf as well
                mySeries[id] = new NodeSeries(d)
                mySeries[id].showAllSeries(true);
            });
        }
    }
});