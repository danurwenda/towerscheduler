<?php
/**
 * Load PHPExcel 1.8.0
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . "/third_party/PHPExcel.php";

class Excel extends PHPExcel {

    public function __construct() {
        parent::__construct();
    }

}
