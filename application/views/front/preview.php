
<div class="row">
    <!-- PAGE CONTENT BEGINS -->
    <form class="form-horizontal" role="form" id="input-form">
        <!-- #section:elements.form -->
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Source file </label>

            <div class="col-sm-9">
                <!--<div class="input-group">-->
                <select class="col-xs-10 col-sm-5" id="form-field-select-1" name ="file">
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
                <select class="col-xs-10 col-sm-5" id="form-field-select-1" name ="conductor">
                    <option value="single">single</option>
                    <option value="double">double</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Circuit </label>

            <div class="col-sm-9">
                <select class="col-xs-10 col-sm-5" id="form-field-select-1" name ="circuit">
                    <option value="2">2</option>
                    <option value="4">4</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> Fasa </label>

            <div class="col-sm-9">
                <select class="col-xs-10 col-sm-5" id="form-field-select-1" name ="fasa">
                    <option value="2">2</option>
                    <option value="3" selected>3</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-4">Sagging Coef.</label>

            <div class="col-sm-9">
                <input class="input-sm" name="sc" type="number" id="form-field-4">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-4">Berat kawat kg/m</label>

            <div class="col-sm-9">
                <input class="input-sm" name="w" type="number" id="form-field-4">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="form-field-2"> Mulai tarikan </label>

            <div class="col-sm-9">
                <select class="col-xs-10 col-sm-5" id="form-field-select-1" name ="tarikan">
                    <option value="awal" selected>Nomor kecil</option>
                    <option value="akhir">Nomor besar</option>
                </select>
            </div>
        </div>        
    </form>

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
                <td rowspan=2 >sutet</td>
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
                <td rowspan=2 >santet</td>
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
        //tooltip on action-buttons
        $('body').tooltip({
            selector: '[data-rel=tooltip]'
        });
        //event handler for action-buttons
        $('#input-table').on('click','a.delete',function (e) {
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
    })
</script>