<!-- #section:elements.tab -->
<div class="tabbable">
    <ul class="nav nav-tabs" id="myTab">
        <li class="active">
            <a data-toggle="tab" href="#upload" aria-expanded="true">
                <i class="green ace-icon fa fa-file bigger-120"></i>
                Upload
            </a>
        </li>
        <li class="">
            <a data-toggle="tab" href="#form1" aria-expanded="false">
                <i class="green ace-icon fa fa-pencil-square bigger-120"></i>
                Form
            </a>
        </li>



    </ul>

    <div class="tab-content">
        <div id="form1"  class="tab-pane fade">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <form class="form-horizontal" role="form">
                        <!-- #section:elements.form -->
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
                        <div class="clearfix form-actions">
                            <div class="col-md-offset-3 col-md-9">
                                <button class="btn btn-info" type="button">
                                    <i class="ace-icon fa fa-check bigger-110"></i>
                                    Submit
                                </button>

                                &nbsp; &nbsp; &nbsp;
                                <button class="btn" type="reset">
                                    <i class="ace-icon fa fa-undo bigger-110"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>

                </div><!-- /.col -->
            </div>
        </div>

        <div id="upload" class="tab-pane fade active in">
            <a href="#">Download template</a>
            <form>
                <div class="row">
                <div class="form-group">
                    <div class="col-xs-12">
                        <input multiple="" type="file" id="id-input-file-3" />

                        <!-- /section:custom/file-input -->
                    </div>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    jQuery(function ($) {

        $('#id-input-file-3').ace_file_input({
            style: 'well',
            btn_choose: 'Drop files here or click to choose',
            btn_change: null,
            no_icon: 'ace-icon fa fa-cloud-upload',
            droppable: true,
            thumbnail: 'small'//large | fit
                    //,icon_remove:null//set null, to hide remove/reset button
                    /**,before_change:function(files, dropped) {
                     //Check an example below
                     //or examples/file-upload.html
                     return true;
                     }*/
                    /**,before_remove : function() {
                     return true;
                     }*/
            ,
            preview_error: function (filename, error_code) {
                //name of the file that failed
                //error_code values
                //1 = 'FILE_LOAD_FAILED',
                //2 = 'IMAGE_LOAD_FAILED',
                //3 = 'THUMBNAIL_FAILED'
                //alert(error_code);
            }

        }).on('change', function () {
            //console.log($(this).data('ace_input_files'));
            //console.log($(this).data('ace_input_method'));
        });


        //$('#id-input-file-3')
        //.ace_file_input('show_file_list', [
        //{type: 'image', name: 'name of image', path: 'http://path/to/image/for/preview'},
        //{type: 'file', name: 'hello.txt'}
        //]);
    })
</script>
<!-- /section:elements.tab -->