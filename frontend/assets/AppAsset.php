<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/select2.min.css',
        'fonts/icons/flaticon.css',
        'fonts/font-awesome/css/font-awesome.min.css',
//        'gta/css/bootstrap.min.css',
        'css/bootstrap.min.css',
        'css/bootstrap-datepicker.min.css',
        'owlcarousel/dist/assets/owl.carousel.min.css',
        'owlcarousel/dist/assets/owl.theme.default.css',
        'css/jquery.fancybox.min.css',
        'css/style.css',
        'css/diller-cars.css',
        'css/lk-style.css',
        'css/site.css',
        'gta/css/swiper.min.css',
        'gta/css/custom.css',
        'gta/css/style.css',
        'gta/css/responsive.css',
    ];
    public $js = [
//        'js/jquery-3.2.1.min.js',
        'js/bootstrap.min.js',
        'js/bootstrap-datepicker.min.js',
        'owlcarousel/dist/owl.carousel.min.js',
        'js/jquery.fancybox.min.js',
        'js/select2.min.js',
        'js/script.js',
        'gta/js/swiper.min.js',
        'gta/js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
//        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
