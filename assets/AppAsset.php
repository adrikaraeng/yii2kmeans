<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'fontawesome/css/all.css',
        'fontawesome/css/brands.css',
        'fontawesome/css/fontawesome.css',
        'morrisjs/morris.css',
    ];
    public $js  = [
        'morrisjs/morris.js',
        'js/main.js',
        // 'js/regional.js',
        'js/raphael-min.js',
        'canvasjs-2.3.1/canvasjs.min.js',
        // 'highchart/code/highcharts.js',
        // 'highchart/code/modules/exporting.js',
        // 'highchart/code/hmodules/export-data.js',
        // 'highchart/code/hmodules/accessibility.js',
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
