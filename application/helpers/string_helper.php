<?php
defined('BASEPATH') OR
    exit('No direct script access allowed');
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

function inc_col($col_str, $gap){
    $ret = $col_str;
    if($gap >0){
        for($i=0;$i<$gap;$i++){
            ++$ret;
        }
    }else{
        for($i=$gap;$i<0;$i++){
            --$ret;
        }
    }
    return $ret;
}