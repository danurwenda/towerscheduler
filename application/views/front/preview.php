
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
                    </span>
                    <!--</div>-->
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
            <tr>
                <td rowspan=2 >60.00</td>
                <td rowspan=2 >SUTET</td>
            </tr>
            <tr class="tower-row">
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
                <td rowspan=2  class="center num-col"></td>
                <td rowspan=2 >1</td>
                <td rowspan=2 >Ddr4</td>
                <td rowspan=2 >+0</td>
                <td rowspan=2 >123</td>
            </tr>
            <tr class="span-row">
                <td rowspan=2>118.68</td>
                <td rowspan=2 >SUTT</td>
            </tr>
            <tr>               
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
<script>
    function renumber() {
        $('.tower-row').each(function (i, e) {
            $(this).children('.num-col').html(i + 1)
        })
    }
    jQuery(function ($) {
        //load selected excel files (if possible)
        $('.btn-load').click(function (e) {
            $.getJSON(
                    base_url + 'converter/load_file/' + $('select[name="file"]').val(),
//                    {file: },
                    function (data) {
                        $.each(data, function (k, v) {
                            $('#input-form [name="' + k + '"]').val(v)
                        })
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
        $('#tower-sch').click(function(e){
            //create form tipu2
            var form = $('<form method="POST" action="' + base_url+'converter/tower_sch' + '">');
            //add params
            $.each($('#input-form').serializeArray(), function(k, v) {
                form.append($('<input type="hidden" name="' + v.name +
                        '" value="' + v.value + '">'));
            });
            $('body').append(form);
            form.submit();
        })
        //generate reports button : MATERIAL SCHEDULE
        $('#mat-sch').click(function(e){
            //create form tipu2
            var form = $('<form method="POST" action="' + base_url+'converter/mat_sch' + '">');
            //add params
            $.each($('#input-form').serializeArray(), function(k, v) {
                form.append($('<input type="hidden" name="' + v.name +
                        '" value="' + v.value + '">'));
            });
            $('body').append(form);
            form.submit();
        })
        //generate reports button : SAGGING SCHEDULE
        $('#sag-sch').click(function(e){
            //create form tipu2
            var form = $('<form method="POST" action="' + base_url+'converter/sag_sch' + '">');
            //add params
            $.each($('#input-form').serializeArray(), function(k, v) {
                form.append($('<input type="hidden" name="' + v.name +
                        '" value="' + v.value + '">'));
            });
            $('body').append(form);
            form.submit();
        })
        //generate reports button : DRUM SCHEDULE
        $('#drum-sch').click(function(e){
            //create form tipu2
            var form = $('<form method="POST" action="' + base_url+'converter/drum_sch' + '">');
            //add params
            $.each($('#input-form').serializeArray(), function(k, v) {
                form.append($('<input type="hidden" name="' + v.name +
                        '" value="' + v.value + '">'));
            });
            $('body').append(form);
            form.submit();
        })
    })
</script>