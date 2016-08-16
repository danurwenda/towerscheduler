<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta charset="utf-8" />
        <title>
            <?php echo $title; ?>
        </title>

        <meta name="description" content="user input" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

        <!-- bootstrap & fontawesome -->
        <?php echo css_asset('bootstrap.css', 'ace'); ?>
        <?php echo css_asset('font-awesome.css', 'ace'); ?>

        <!-- page specific plugin styles -->

        <!-- text fonts -->
        <?php echo css_asset('ace-fonts.css', 'ace'); ?>

        <!-- ace styles -->
        <?php echo css_asset('ace.css', 'ace', ['class' => "ace-main-stylesheet", 'id' => "main-ace-style"]); ?>
        <!-- deputi3 styles -->
        <?php echo css_asset('deputi3.css', 'pln'); ?>
        <!-- inline styles related to this page -->

        <!--[if !IE]> -->
        <script type="text/javascript">
            window.jQuery || document.write("<script src='<?php echo js_asset_url('jquery.js', 'ace'); ?>'>" + "<" + "/script>");
            var base_url = '<?php echo base_url(); ?>';
        </script>

        <!-- <![endif]-->
        <!-- ace settings handler -->
        <?php echo js_asset('ace-extra.js', 'ace'); ?>
    </head>

    <body class="no-skin">
        <!-- #section:basics/navbar.layout -->
        <div id="navbar" class="navbar navbar-default">
            <script type="text/javascript">
                try {
                    ace.settings.check('navbar', 'fixed')
                } catch (e) {
                }
            </script>

            <div class="navbar-container" id="navbar-container">
                <!-- #section:basics/sidebar.mobile.toggle -->
                <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
                    <span class="sr-only">Toggle sidebar</span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>
                </button>

                <!-- /section:basics/sidebar.mobile.toggle -->
                <div class="navbar-header pull-left">
                    <!-- #section:basics/navbar.layout.brand -->
                    <a href="<?php echo base_url();?>" class="navbar-brand">
                            <?php echo image_asset('PLN.jpg','pln',['width'=>100]);?>
                        
                    </a>
                    

                    <!-- /section:basics/navbar.layout.brand -->

                    <!-- #section:basics/navbar.toggle -->

                    <!-- /section:basics/navbar.toggle -->
                </div>
                <div class="center">
                    <div style="font-size:36px;padding-top:50px;">
                            Transmission Lines Analyzer
                        </div>
                </div>

             
            </div><!-- /.navbar-container -->
        </div>

        <!-- /section:basics/navbar.layout -->
        <div class="main-container" id="main-container">
            <script type="text/javascript">
                try {
                    ace.settings.check('main-container', 'fixed')
                } catch (e) {
                }
            </script>

            <!-- #section:basics/sidebar -->
            <div id="sidebar" class="sidebar                  responsive">
                <script type="text/javascript">
                    try {
                        ace.settings.check('sidebar', 'fixed')
                    } catch (e) {
                    }
                </script>

                <div class="sidebar-shortcuts" id="sidebar-shortcuts">

                </div><!-- /.sidebar-shortcuts -->

                <ul class="nav nav-list">
                    <li>
                        <a data-url="converter/upload" href="<?php echo site_url('converter');?>">
                            <i class="menu-icon fa fa-upload"></i>
                            <span class="menu-text"> Upload </span>
                        </a>

                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a data-url="converter/preview" href="<?php echo site_url('converter/preview');?>">
                            <i class="menu-icon fa fa-eye"></i>
                            <span class="menu-text"> Preview </span>
                        </a>

                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a data-url="auth/logout" href="<?php echo site_url('auth/logout');?>">
                            <i class="menu-icon fa fa-sign-out"></i>
                            <span class="menu-text"> Logout </span>
                        </a>

                        <b class="arrow"></b>
                    </li>

<!--                    <li class="">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-desktop"></i>
                            <span class="menu-text">
                                Induk
                            </span>

                            <b class="arrow fa fa-angle-down"></b>
                        </a>

                        <b class="arrow"></b>

                        <ul class="submenu">
                            <li class="">
                                <a href="#" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-caret-right"></i>

                                    Anak1
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>

                                <b class="arrow"></b>

                                <ul class="submenu">
                                    <li class="">
                                        <a href="top-menu.html">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Cucu 1
                                        </a>

                                        <b class="arrow"></b>
                                    </li>

                                    <li class="">
                                        <a href="two-menu-1.html">
                                            <i class="menu-icon fa fa-caret-right"></i>
                                            Cucu 2
                                        </a>

                                        <b class="arrow"></b>
                                    </li>
                                </ul>
                            </li>

                            <li class="">
                                <a href="typography.html">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    Anak 2
                                </a>

                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>-->
                </ul> 

                <!-- #section:basics/sidebar.layout.minimize -->
                <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
                    <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
                </div>

                <!-- /section:basics/sidebar.layout.minimize -->
                <script type="text/javascript">
                    try {
                        ace.settings.check('sidebar', 'collapsed')
                    } catch (e) {
                    }
                </script>
            </div>

            <!-- /section:basics/sidebar -->
            <div class="main-content">
                <div class="main-content-inner">
                    <!-- #section:basics/content.breadcrumbs -->
                    <div class="breadcrumbs" id="breadcrumbs">
                        <script type="text/javascript">
                            try {
                                ace.settings.check('breadcrumbs', 'fixed')
                            } catch (e) {
                            }
                        </script>

                        <ul class="breadcrumb">
                            <li>
                                <i class="ace-icon fa fa-home home-icon"></i>
                                <a href="#">Home</a>
                            </li>
                            <li class="active">Dashboard</li>
                        </ul><!-- /.breadcrumb -->

                        <!-- #section:basics/content.searchbox -->
                        <!-- /section:basics/content.searchbox -->
                    </div>

                    <!-- /section:basics/content.breadcrumbs -->
                    <div class="page-content">
                        
                        <div class="page-header">
                            <h1>
                                Dashboard
                                <small>
                                    <i class="ace-icon fa fa-angle-double-right"></i>
                                    <?php echo $title?>
                                </small>
                            </h1>
                        </div><!-- /.page-header -->

                        <div class="row">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                <?php echo $_content; ?>

                                <!-- PAGE CONTENT ENDS -->
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.page-content -->
                </div>
            </div><!-- /.main-content -->

            <div class="footer">
                <div class="footer-inner">
                    <!-- #section:basics/footer -->
                    <div class="footer-content">
                        <span class="bigger-120">
                            <span class="blue bolder"><a href="http://danurwenda.com">Slrp</a></span>
                             &copy; 2016
                        </span>
                    </div>

                    <!-- /section:basics/footer -->
                </div>
            </div>

            <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
                <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
            </a>
        </div><!-- /.main-container -->

        <!-- basic scripts -->

        <!--[if !IE]> -->
        <script type="text/javascript">
            window.jQuery || document.write("<script src='../assets/js/jquery.js'>" + "<" + "/script>");
        </script>

        <!-- <![endif]-->

        <!--[if IE]>
<script type="text/javascript">
window.jQuery || document.write("<script src='../assets/js/jquery1x.js'>"+"<"+"/script>");
</script>
<![endif]-->
        <script type="text/javascript">
            if ('ontouchstart' in document.documentElement)
                document.write("<script src='../assets/js/jquery.mobile.custom.js'>" + "<" + "/script>");
        </script>
        <?php echo js_asset('bootstrap.js', 'ace'); ?>

        <!-- page specific plugin scripts -->
        <script>
            //kasih .active di sidebar
            jQuery(function($){
                console.log(window.location.href)
              $('.sidebar .nav-list a').each(function(i){
                  if(window.location.href.endsWith($(this).data('url'))){
                      $(this).closest('li').addClass('active')}else{
                      $(this).closest('li').removeClass('active')}
              })  
            })            
        </script>
        <!--[if lte IE 8]>
          <?php echo js_asset('excanvas.js', 'ace'); ?>
        <![endif]-->
        <?php echo js_asset('jquery-ui.custom.js', 'ace'); ?>
        <?php echo js_asset('jquery.ui.touch-punch.js', 'ace'); ?>
        <?php echo js_asset('jquery.easypiechart.js', 'ace'); ?>
        <?php echo js_asset('jquery.sparkline.js', 'ace'); ?>
        <?php echo js_asset('flot/jquery.flot.js', 'ace'); ?>
        <?php echo js_asset('flot/jquery.flot.pie.js', 'ace'); ?>
        <?php echo js_asset('flot/jquery.flot.resize.js', 'ace'); ?>

        <!-- ace scripts -->
        <?php echo js_asset('ace/elements.scroller.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.colorpicker.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.fileinput.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.typeahead.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.wysiwyg.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.spinner.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.treeview.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.wizard.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.aside.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.ajax-content.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.touch-drag.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.sidebar.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.sidebar-scroll-1.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.submenu-hover.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.widget-box.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.settings.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.settings-rtl.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.settings-skin.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.widget-on-reload.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.searchbox-autocomplete.js', 'ace'); ?>

        <!-- inline scripts related to this page -->
        

    </body>
</html>
