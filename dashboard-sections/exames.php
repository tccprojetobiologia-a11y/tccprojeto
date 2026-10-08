<?php
require_once __DIR__ . '/../exames.php';

if (!function_exists('getDashboardExamesHtml')) {
    function getDashboardExamesHtml()
    {
        return getExamesHtml();
    }
}
