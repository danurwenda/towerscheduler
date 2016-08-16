<!-- #section:elements.tab -->

<div id="upload">
    <a href="templates/input/tower_template.xlsx">Download template</a>
    <?php echo form_open_multipart('converter/do_upload',[],['savename'=>null]); ?>
    <div class="row">
        <?php echo isset($error) ? $error : ''; ?>
        <div class="form-group">
            <div class="col-xs-12">
                <input type="file" name="userfile" id="id-input-file-3" />

                <!-- /section:custom/file-input -->
            </div>
        </div>
    </div> </form>
<div class="clearfix form-actions">
    <div class="col-md-offset-3 col-md-9">
        <button class="btn btn-info" type="button">
            <i class="ace-icon fa fa-check bigger-110"></i>
            Next
        </button>

        &nbsp; &nbsp; &nbsp;
        <a href="converter/preview">
            <button class="btn btn-skip">
                <i class="ace-icon fa fa-arrow-right bigger-110"></i>
                Skip
            </button>
        </a>
    </div>
</div>

</div>


<script>
    jQuery(function ($) {
        $('.form-actions .btn-info').click(function (e) {
            var f2s=$('#id-input-file-3').data('ace_input_files')
            //cek ada file apa engga
            //kalau ada minta nama file
            if (f2s.length==1) {
                var uname = prompt('Masukkan nama file penyimpanan', 'Project ABC')
                if (uname != null)
                    $('form').find('input:hidden[name=savename]').val(uname);
                $('form').submit();
            }
        })
        $('#id-input-file-3').ace_file_input({
            allowExt: 'xlsx',
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