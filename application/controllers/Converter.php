<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Converter extends Member_Controller {

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
    public function upload() {
        $data['title'] = 'Upload File';
        $this->template->display('upload', $data);
    }

    public function index() {
        redirect('converter/upload');
    }

    public function do_upload() {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xlsx';
        if ($save = $this->input->post('savename'))
            $config['file_name'] = $save;
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());

            $this->template->display('upload', $error);
        } else {
            $data = array('upload_data' => $this->upload->data());

            redirect('converter/preview');
        }
    }
    
    public function uploaded_files(){
        echo json_encode($this->scan_dir());
    }

    private function scan_dir($dir='./uploads') {
        $ignored = array('.', '..', '.svn', '.htaccess');

        $files = array();
        foreach (scandir($dir) as $file) {
            if (is_file($dir . '/' . $file)) {
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

        $data['files'] = $this->scan_dir();
        $this->template->display('preview', $data);
    }

    public function load_file($file) {
        $this->load->library('excel');
        $inputFileName = 'uploads/' . $file;
        $this->load->model('xtemplate_model');
        $ret = $this->xtemplate_model->readInput($inputFileName);

        echo json_encode($ret);
    }

    public function save_file($filename) {
        $conductor = $this->input->post('conductor');
        $project = $this->input->post('project');
        $circuit = $this->input->post('circuit');
        $tarikan = $this->input->post('tarikan');
        $haspel = $this->input->post('haspel');
        $tension = $this->input->post('tension');
        $sc = $this->input->post('sc');
        $w = $this->input->post('w');
        $spans = json_decode($this->input->post('spans'), true);
        $towers = json_decode($this->input->post('towers'), true);

        //cleanse $filename, jangan sampai ada up folder
        //cek 1 : jika mengandung slash, cuma ambil setelah slash
        $slash = strrpos($filename, '/');
        if ($slash !== false) {
            $filename = substr($filename, 1 + $slash);
        }
        //cek 2 : backslash
        $bslash = strrpos($filename, '\\');
        if ($bslash !== false) {
            $filename = substr($filename, 1 + $bslash);
        }
        //extension
        if (!endsWith($filename, '.xlsx'))
            $filename = $filename . '.xlsx';
        $data = [
            'filename' => 'uploads/' . $filename,
            'conductor' => $conductor,
            'project' => $project,
            'circuit' => $circuit,
            'tarikan' => $tarikan,
            'tension' => $tension,
            'haspel' => $haspel,
            'sc' => $sc,
            'w' => $w,
            'spans' => $spans,
            'towers' => $towers
        ];

        $this->load->model('xtemplate_model');
        $ret = ['success' => $this->xtemplate_model->save_input($data)];
        echo json_encode($ret);
    }

    /**
     * Template 1 : Tower Schedule
     */
    public function tower_sch() {
        //file input
        $file = 'uploads/' . $this->input->post('file');
        //kirim ke model
        $this->load->model('xtemplate_model');
        $this->xtemplate_model->generate_tower_schedule(['file' => $file]);
    }

    /**
     * Template 2 : Material Schedule
     */
    public function mat_sch() {
        //file input
        $file = 'uploads/' . $this->input->post('file');
        //kirim ke model
        $this->load->model('xtemplate_model');
        $this->xtemplate_model->generate_material_schedule(['file' => $file]);
    }

    /**
     * Template 3 : Sagging Schedule
     */
    public function sag_sch() {
        //file input
        $file = 'uploads/' . $this->input->post('file');
        //kirim ke model
        $this->load->model('xtemplate_model');
        $this->xtemplate_model->generate_sagging_schedule(['file' => $file]);
    }

    /**
     * Template 4 : Drum Schedule
     */
    public function drum_sch() {
        //file input
        $file = 'uploads/' . $this->input->post('file');
        //kirim ke model
        $this->load->model('xtemplate_model');
        $this->xtemplate_model->generate_drum_schedule(['file' => $file]);
    }

}
