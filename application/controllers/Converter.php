<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Converter extends CI_Controller {

    public function __construct() {
        parent::__construct();
        //load library
        $this->load->library('template');
    }

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        $data['title'] = 'Upload File';
        $this->template->display('upload', $data);
    }

    public function do_upload() {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx';
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());

            $this->template->display('upload', $error);
        } else {
            $data = array('upload_data' => $this->upload->data());

            redirect('converter/preview');
        }
    }

    function scan_dir($dir) {
        $ignored = array('.', '..', '.svn', '.htaccess');

        $files = array();
        foreach (scandir($dir) as $file) {
            if (is_file($dir.'/'.$file)) {
                if (in_array($file, $ignored))
                    continue;
                $files[$file] = filemtime($dir . '/' . $file);
            }
        }

        arsort($files);
        $files = array_keys($files);

        return ($files) ? $files : false;
    }

    public function preview($d = null) {
        $data['title'] = 'Preview';
        //list of all files in /uploads

        $data['files'] = $this->scan_dir('./uploads');
        $this->template->display('preview', $data);
    }

}
