<?php

namespace store\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [

        'booking_calendar/lib/dhtmlxScheduler/dhtmlxscheduler.js',
        'booking_calendar/lib/dhtmlxScheduler/ext/dhtmlxscheduler_limit.js',
        'booking_calendar/lib/dhtmlxScheduler/ext/dhtmlxscheduler_collision.js',
        'booking_calendar/lib/dhtmlxScheduler/ext/dhtmlxscheduler_timeline.js',
        'booking_calendar/lib/dhtmlxScheduler/ext/dhtmlxscheduler_editors.js',
        'booking_calendar/lib/dhtmlxScheduler/ext/dhtmlxscheduler_minical.js',
        'booking_calendar/lib/dhtmlxScheduler/ext/dhtmlxscheduler_tooltip.js',
        'booking_calendar/js/scripts.js',

        'js/pace.min.js',
        'js/select2.min.js',
        'js/scripts.js',


//        'booking_calendar/js/mock_backend.js',


        // 'plugins/jquery/jquery.min.js',
        'plugins/jquery-ui/jquery-ui.min.js',
        'plugins/bootstrap/js/bootstrap.bundle.min.js',
        'plugins/chart.js/Chart.min.js',
        'plugins/sparklines/sparkline.js',
        'plugins/jqvmap/jquery.vmap.min.js',
        'plugins/jqvmap/maps/jquery.vmap.usa.js',
        'plugins/jquery-knob/jquery.knob.min.js',
//        'plugins/moment/moment.min.js',
        'plugins/daterangepicker/daterangepicker.js',
        'plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
        'plugins/summernote/summernote-bs4.min.js',
        'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
        'dist/js/adminlte.js',
        'dist/js/demo.js',


        // 'dist/js/pages/dashboard.js',
    ];
    public $css = [

//
        'booking_calendar/lib/dhtmlxScheduler/dhtmlxscheduler_flat.css',
        'booking_calendar/css/styles.css',


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
    ];
    public $depends = [
        'yii\web\YiiAsset',
//         'yii\bootstrap\BootstrapAsset',
//         'yii\bootstrap\BootstrapPluginAsset',
//         'rmrevin\yii\fontawesome\AssetBundle',
    ];

}
