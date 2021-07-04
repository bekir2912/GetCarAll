<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
         'css/select2.min.css',
         'css/site.css',
         'css/pace-theme-flash.css',
         'css/navbar-fixed-side.css',




        'plugins/fontawesome-free/css/all.min.css',
        'plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
        'plugins/icheck-bootstrap/icheck-bootstrap.min.css',
        'plugins/jqvmap/jqvmap.min.css',
        // 'dist/css/adminlte.min.css',
        'plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
        'plugins/daterangepicker/daterangepicker.css',
        'plugins/summernote/summernote-bs4.min.css',

        // 'vendors/bootstrap/dist/css/bootstrap.min.css',
        // 'vendors/font-awesome/css/font-awesome.min.css',
        // 'vendors/nprogress/nprogress.css',
        // 'vendors/iCheck/skins/flat/green.css',
        // 'vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css',
        // 'vendors/jqvmap/dist/jqvmap.min.css',
        // 'vendors/bootstrap-daterangepicker/daterangepicker.css',
        // 'production/css/maps/jquery-jvectormap-2.0.3.css',

        // 'build/css/custom.min.css',

    ];
    public $js = [
        'js/pace.min.js',
        'js/select2.min.js',
        'js/scripts.js',


//        'plugins/jquery/jquery.min.js',
        'plugins/jquery-ui/jquery-ui.min.js',
        'plugins/bootstrap/js/bootstrap.bundle.min.js',
        'plugins/chart.js/Chart.min.js',
        'plugins/sparklines/sparkline.js',
        'plugins/jqvmap/jquery.vmap.min.js',
        'plugins/jqvmap/maps/jquery.vmap.usa.js',
        'plugins/jquery-knob/jquery.knob.min.js',
        'plugins/moment/moment.min.js',
        'plugins/daterangepicker/daterangepicker.js',
        'plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
        'plugins/summernote/summernote-bs4.min.js',
        'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
        'dist/js/adminlte.js',
        'dist/js/demo.js',
//        'dist/js/pages/dashboard.js',








    //     'production/js/datepicker/daterangepicker.js',

    //     'vendors/jquery/dist/jquery.min.js',
    // 'vendors/bootstrap/dist/js/bootstrap.bundle.min.js',
    // 'vendors/fastclick/lib/fastclick.js',
    // 'vendors/nprogress/nprogress.js',
    // 'vendors/Chart.js/dist/Chart.min.js',
    // 'vendors/gauge.js/dist/gauge.min.js',
    // 'vendors/bootstrap-progressbar/bootstrap-progressbar.min.js',
    // 'vendors/iCheck/icheck.min.js',
    // 'vendors/skycons/skycons.js',
    // 'vendors/Flot/jquery.flot.js',
    // 'vendors/Flot/jquery.flot.pie.js',
    // 'vendors/Flot/jquery.flot.time.js',
    // 'vendors/Flot/jquery.flot.stack.js',
    // 'vendors/Flot/jquery.flot.resize.js',
    // 'vendors/flot.orderbars/js/jquery.flot.orderBars.js',
    // 'vendors/flot-spline/js/jquery.flot.spline.min.js',
    // 'vendors/flot.curvedlines/curvedLines.js',
    // 'vendors/DateJS/build/date.js',
    // 'vendors/jqvmap/dist/jquery.vmap.js',
    // 'vendors/jqvmap/dist/maps/jquery.vmap.world.js',
    // 'vendors/jqvmap/examples/js/jquery.vmap.sampledata.js',
    // 'vendors/moment/min/moment.min.js',
    // 'vendors/bootstrap-daterangepicker/daterangepicker.js',
    // 'build/js/custom.min.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        // 'yii\bootstrap\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];
}
