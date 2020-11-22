<?php

if (!function_exists('deprecate')) {
    function deprecate() {
        throw new \Exception('Deprecated.');
    }
}