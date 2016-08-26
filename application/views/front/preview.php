
<div class="row">
    <div class="col-sm-6">
        <!-- PAGE CONTENT BEGINS -->
        <form class="form-horizontal" role="form" id="input-form">
            <!-- #section:elements.form -->
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Source file </label>

                <div class="col-sm-9">
                    <!--<div class="input-group">-->
                    <select class="col-xs-10 col-sm-5" id="file-select" name ="file">
                        <!-- list of uploaded file, order by date desc-->
                        <?php
                        foreach ($files as $file) {
                            echo '<option value="' . $file . '">' . $file . '</option>';
                        }
                        ?>
                    </select>
                    <span >
                        <button class="btn btn-sm btn-default btn-load" type="button">
                            <i class="ace-icon fa fa-upload bigger-110"></i>
                            Load
                        </button>
                        <button class="btn btn-sm btn-default btn-save" type="button">
                            <i class="ace-icon fa fa-save bigger-110"></i>
                            Save
                        </button>
                    </span>
                    <!--</div>-->
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">Jalur</label>

                <div class="col-sm-9">
                    <input class="input-xxlarge" name="project" type="text" id="project-input">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Conductor </label>

                <div class="col-sm-9">
                    <select class="col-xs-10 col-sm-5" id="conductor-select" name ="conductor">
                        <option value="1">single</option>
                        <option value="2">double</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Circuit </label>

                <div class="col-sm-9">
                    <select class="col-xs-10 col-sm-5" id="circuit-select" name ="circuit">
                        <option value="2">2</option>
                        <option value="4">4</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">Tension</label>

                <div class="col-sm-9">
                    <input class="input-sm" name="tension" type="number" id="tension-input">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">Sagging Coef.</label>

                <div class="col-sm-9">
                    <input class="input-sm" name="sc" type="number" id="sagging-input">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-4">Berat kawat kg/m</label>

                <div class="col-sm-9">
                    <input class="input-sm" name="w" type="number" id="mass-input">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> Mulai tarikan </label>

                <div class="col-sm-9">
                    <select class="col-xs-10 col-sm-5" id="tarikan-select" name ="tarikan">
                        <option value="kecil" selected>Nomor kecil</option>
                        <option value="besar">Nomor besar</option>
                    </select>
                </div>
            </div> 
        </form>

    </div>
    <div class="col-sm-6">
        <a href="#" class="btn btn-default btn-block btn-success" id="tower-sch">
            <i class="ace-icon fa fa-align-center bigger-230"></i>
            Tower Sch
        </a>
        <a href="#" class="btn btn-default btn-block btn-success" id ="mat-sch">
            <i class="ace-icon fa fa-flask bigger-230"></i>
            Material Sch
        </a>
        <a href="#" class="btn btn-default btn-block btn-success" id = "sag-sch">
            <i class="ace-icon fa fa-chevron-down bigger-230"></i>
            Sagging Sch
        </a>
        <a href="#" class="btn btn-default btn-block btn-success" id="drum-sch">
            <i class="ace-icon fa fa-database bigger-230"></i>
            Drum Sch
        </a>
    </div>
</div>
<!-- Excel-like div -->
<style>
    #input-table{table-layout: fixed}
    thead th{width: 10%;}
    thead th:last-child{width: 40%;}
    thead th:first-child{width: 90px;}
    td.ceditable{overflow-x: hidden}
</style>
<div class="row">
    <table id="input-table" class="table table-bordered delete ">
        <thead>
            <tr>
                <th class="center">Action</th>
                <th class="center">No</th>
                <th class="center">Tower num</th>
                <th class="center" colspan="2">Tower type</th>
                <th class="center">Act span</th>
                <th class="center">Weight span</th>
                <th class="center">Crossing remarks</th>
            </tr>  
        </thead>
        <tbody>
            <tr>
                <td colspan=5 rowspan=2 class="center bolder">START</td>
                <td></td>
                <td rowspan=2></td>
                <td ></td>  
            </tr>
            <tr id="first-span">
                <td rowspan=2 class="ceditable">60.00</td>
                <td rowspan=2 class="ceditable">SUTET</td>
            </tr>
            <tr class="tower-row tower-template hide">
                <td rowspan=2 class="action-col action-buttons">
                    <a title="insert new tower below" class="blue insert" href="#" data-rel="tooltip">
                        <i class="ace-icon fa fa-share bigger-130"></i>
                    </a>

                    <a class="green clone" title="clone this tower below" data-rel="tooltip" href="#">
                        <i class="ace-icon fa fa-clone bigger-130"></i>
                    </a>

                    <a class="red delete" title="delete tower" data-rel="tooltip" href="#">
                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                    </a></td>
                <td rowspan=2 class="center num-col"></td>
                <td rowspan=2 class="ceditable"></td>
                <td rowspan=2 class="ceditable"></td>
                <td rowspan=2 class="ceditable"></td>
                <td rowspan=2 class="ceditable"></td>
            </tr>
            <tr class="span-row span-template hide">
                <td rowspan=2 class="ceditable"></td>
                <td rowspan=2 class="ceditable"></td>
            </tr>
            <tr id="end-tower">  
                <td colspan=5 rowspan=2 class="center bolder">END</td>
                <td rowspan=2></td>
            </tr>
            <tr height="19">
                <td></td>
                <td></td>

            </tr>
        </tbody>
    </table>
</div>
<div class="hide" id="ceditable-container">
    <input onkeyup="enterHandler(event)" type="text" id="ceditable-input"/>
</div>
<script>
    function renumber() {
        $('.tower-row:not(.tower-template)').each(function (i, e) {
            $(this).children('.num-col').html(i + 1)
        })
    }
    function enterHandler(e) {
        if (e.keyCode == 13)
        {
            $(e.target).trigger("enterKey");
        }
    }
    jQuery(function ($) {
        $('#input-table').on("enterKey", '#ceditable-input', function (e) {
            var ths = $(this)
            ths.blur()
            ths.parent().html(ths.val())
            //put ceditable back
            ths.appendTo('#ceditable-container')
        });
        $('#input-table').on('click', 'td.ceditable', function (e) {
            //prevent bubble
            if ($(this).find('#ceditable-input').length == 0) {
                //show editable input
                var ini = $(this).html(), input = $('#ceditable-input');
                $(this).html('')
                //set input value to previous container
                if (input.parent().hasClass('ceditable')) {
                    input.parent().html(input.val())
                }
                //relocate input
                input.val(ini).appendTo($(this)).focus()
            }
        })
        //save selected excel files
        $('.btn-save').click(function (e) {
            var filename = prompt("Save as", $('#input-form [name="file"]').val());

            if (filename != null) {
                //add towers & spans [array] data as hidden input
                var spans = [], towers = [];
                //first span, special handling
                var fs = {
                    act_span:$('#first-span').children().first().html(), 
                    crossing_rem:$('#first-span').children().last().html()};
                spans.push(fs);
                //the rest of span
                $('.span-row:not(.span-template)').each(function (i, e) {
                    spans.push({
                        act_span: $(this).children().first().html(),
                        crossing_rem: $(this).children().last().html()
                    })
                });
                //all towers
                $('.tower-row:not(.tower-template)').each(function (i, e) {
                    var tower_row = $(this)
                    towers.push(
                            {
                                tower_num: tower_row.children(':nth-child(3)').html(),
                                tower_type: tower_row.children(':nth-child(4)').html(),
                                tower_ext: tower_row.children(':nth-child(5)').html(),
                                weight_span: tower_row.children(':nth-child(6)').html()
                            }
                    )
                });
                var url = base_url + 'converter/save_file/' + filename,
                        form_data = $('#input-form').serializeArray();
                form_data.push({name: 'spans', value: JSON.stringify(spans)})
                form_data.push({name: 'towers', value: JSON.stringify(towers)})
                //POST data to server
                $.post(url, form_data, function (data, textStatus) {
                    //data contains the JSON object
                    //textStatus contains the status: success, error, etc

                }, "json");
            }
        })
        //load selected excel files (if possible)
        $('.btn-load').click(function (e) {
            $.getJSON(
                    base_url + 'converter/load_file/' + $('select[name="file"]').val(),
                    function (data) {
                        //populate form
                        $.each(data, function (k, v) {
                            $('#input-form [name="' + k + '"]').val(v)
                        })

                        //populate table
                        var first_span = data.spans[0]
                        $('#first-span').children().first().html(first_span.act_span)
                        $('#first-span').children().last().html(first_span.crossing_rem)
                        //flush previous rows
                        $('.tower-row:not(.hide)').remove()
                        $('.span-row:not(.hide)').remove()
                        //iterate
                        for (var i = 0; i < data.towers.length; i++) {
                            var tower = data.towers[i], span = data.spans[1 + i]
                                    //insert
                                    , tower_row = $('.tower-template').clone().removeClass('hide tower-template').insertBefore('#end-tower')
                                    , span_row = $('.span-template').clone().removeClass('hide span-template').insertBefore('#end-tower')

                            tower_row.children(':nth-child(3)').html(tower.tower_num)
                            tower_row.children(':nth-child(4)').html(tower.tower_type)
                            tower_row.children(':nth-child(5)').html(tower.tower_ext)
                            tower_row.children(':nth-child(6)').html(tower.weight_span)

                            span_row.children().first().html(span.act_span)
                            span_row.children().last().html(span.crossing_rem)
                        }
                        renumber()
                    })
        })
        //tooltip on action-buttons
        $('body').tooltip({
            selector: '[data-rel=tooltip]'
        });
        //event handler for action-buttons
        $('#input-table').on('click', 'a.delete', function (e) {
            if (confirm('Delete this tower?')) {
                //delete this tr and next tr as well
                var tower_row = $(this).closest('tr'), span_row = tower_row.next()
                tower_row.remove()
                span_row.remove()
                renumber()
            }
        })
        $('#input-table').on('click', 'a.clone', function (e) {
            //close this tooltip
            $(this).tooltip('hide')
            //delete this tr and next tr as well
            var tower_row = $(this).closest('tr'), span_row = tower_row.next()
                    , clone = tower_row.clone()
            //remove tooltip from currently hovered elmt
            clone.find('.tooltip').remove()
            //insert 
            clone.insertAfter(span_row)
            span_row.clone().insertAfter(clone)
            renumber()
        })
        $('#input-table').on('click', 'a.insert', function (e) {
            //close this tooltip
            $(this).tooltip('hide')
            //delete this tr and next tr as well
            var tower_row = $(this).closest('tr'), span_row = tower_row.next()
                    , clone = tower_row.clone()
            //remove tooltip from currently hovered elmt
            clone.find('.tooltip').remove()
            //insert 
            clone.children(':not(.action-buttons)').empty().parent().insertAfter(span_row)
            span_row.clone().children(':not(.action-buttons)').empty().parent().insertAfter(clone)
            renumber()
        })
        //assign row number for the first time
        renumber()

        //generate reports button : TOWER SCHEDULE
        $('#tower-sch').click(function (e) {
            //create form tipu2
            var form = $('<form method="POST" action="' + base_url + 'converter/tower_sch' + '">');
            //add params
            $.each($('#input-form').serializeArray(), function (k, v) {
                form.append($('<input type="hidden" name="' + v.name +
                        '" value="' + v.value + '">'));
            });
            $('body').append(form);
            form.submit();
            form.remove();
        })
        //generate reports button : MATERIAL SCHEDULE
        $('#mat-sch').click(function (e) {
            //create form tipu2
            var form = $('<form method="POST" action="' + base_url + 'converter/mat_sch' + '">');
            //add params
            $.each($('#input-form').serializeArray(), function (k, v) {
                form.append($('<input type="hidden" name="' + v.name +
                        '" value="' + v.value + '">'));
            });
            $('body').append(form);
            form.submit();
            form.remove();
        })
        //generate reports button : SAGGING SCHEDULE
        $('#sag-sch').click(function (e) {
            //create form tipu2
            var form = $('<form method="POST" action="' + base_url + 'converter/sag_sch' + '">');
            //add params
            $.each($('#input-form').serializeArray(), function (k, v) {
                form.append($('<input type="hidden" name="' + v.name +
                        '" value="' + v.value + '">'));
            });
            $('body').append(form);
            form.submit();
            form.remove();
        })
        //generate reports button : DRUM SCHEDULE
        $('#drum-sch').click(function (e) {
            //create form tipu2
            var form = $('<form method="POST" action="' + base_url + 'converter/drum_sch' + '">');
            //add params
            $.each($('#input-form').serializeArray(), function (k, v) {
                form.append($('<input type="hidden" name="' + v.name +
                        '" value="' + v.value + '">'));
            });
            form.appendTo('body').submit().remove();
        })
    })
</script>