<?php

/*
Plugin Name: Getro API Jobs
Description: Custom plugin for Getro API jobs [getro-company-jobs], [getro-all-jobs]
Version: 1.0.0
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

//Loading CSS
function getro_api_jobs__enqueue_scripts()
{
    // CSS
    wp_enqueue_style('getro-jobs-style', plugins_url('/assets/css/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'getro_api_jobs__enqueue_scripts');


// Time ago format
function get_job_timeago($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1) {
        return 'less than 1 second ago';
    }

    $a = array(
        12 * 30 * 24 * 60 * 60 => 'year',
        30 * 24 * 60 * 60 => 'month',
        24 * 60 * 60 => 'day',
        60 * 60 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;

        if ($d >= 1) {
            $r = round($d);
            return '' . $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
        }
    }
}

// Company and jobs display shortcode [getro-company-jobs]
include(plugin_dir_path(__FILE__) . 'inc/getro-api-company-jobs.php');

// All jobs display shortcode [getro-all-jobs]
include(plugin_dir_path(__FILE__) . 'inc/getro-api-all-jobs.php');