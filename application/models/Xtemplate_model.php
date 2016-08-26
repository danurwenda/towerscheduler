<?php

defined('BASEPATH')OR
        exit('No direct script access allowed');

/**
 * Model terkait table indikator.
 *
 * @author Administrator
 */
class Xtemplate_model extends CI_Model {

    public $table = 'chart';
    public $primary_key = 'chart_id';
    private $tower_num = 'tower_num';
    private $tower_type = 'tower_type';
    private $tower_ext = 'tower_ext';
    private $act_span = 'act_span';
    private $weight_span = 'weight_span';
    private $crossing_rem = 'crossing_rem';

    public function __construct() {
        parent::__construct();
        $CI = & get_instance();
        $CI->load->library('excel');
    }

    public function createReaderWorksheet($inputFileName) {
        /** Identify the type of $inputFileName * */
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        /** Create a new Reader of the type that has been identified * */
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
//we're only interested in cell values
        $objReader->setReadDataOnly(true);
        //list all sheet since we're going to read the first sheet only
        $worksheetList = $objReader->listWorksheetNames($inputFileName);
        $sheetname = $worksheetList[0];
        /** Advise the Reader of which WorkSheets we want to load * */
        $objReader->setLoadSheetsOnly($sheetname);

//LOAD File
        $objPHPExcel = $objReader->load($inputFileName);
//GET (filtered) sheet
        return $objPHPExcel->getActiveSheet();
    }

    //FILE INPUT
    private $input_span_start = 18;
    private $input_tower_num_column = 'D';
    private $input_tower_type_column = 'E';
    private $input_tower_type2_column = 'F';
    private $input_act_span_column = 'G';
    private $input_weight_span_column = 'H';
    private $input_crossing_rem_column = 'I';

    public function readInput($inputFileName) {
        $objWorksheet = $this->createReaderWorksheet($inputFileName);
        //read assumptions
        $conductor = $objWorksheet->getCell('F3')->getValue();
        $circuit = $objWorksheet->getCell('F4')->getValue();
        $sc = $objWorksheet->getCell('F5')->getValue();
        $w = $objWorksheet->getCell('F6')->getValue();
        $tension = $objWorksheet->getCell('F7')->getValue();
        $tarikan = $objWorksheet->getCell('F8')->getValue();
        $project = $objWorksheet->getCell('J3')->getValue();
        $conductorT = $objWorksheet->getCell('J4')->getValue();
        $ew1 = $objWorksheet->getCell('J5')->getValue();
        $ew2 = $objWorksheet->getCell('K5')->getValue();

        //array of span, must be one more than the  number of tower
        //each element consists of the actual span (column B) and crossing remarks (I)
        //the first span starts from row 17 and advancing 2 row every step
        $spans = [];
        //tower : [towernumber (column D), type (E), num (F), weightspan(H)]
        //starts from row 18
        $towers = [];

        //WE WILL STOP READING IF WE FIND AN EMPTY CELL IN COLUMN G
        $empty = false;
        $last_span = false;
        $spanRow = $this->input_span_start;
        while (!$empty) {
            $spanCell = $objWorksheet->getCell($this->input_act_span_column . $spanRow);
            $spanValue = $spanCell->getValue();
            if ($spanValue === NULL || $spanValue === '') {
                $empty = true;
            } else {
                //read crossing remarks
                $crossRemarksValue = $objWorksheet->getCell($this->input_crossing_rem_column . $spanRow)->getValue();
                $spans[] = [
                    $this->act_span => $spanValue,
                    $this->crossing_rem => $crossRemarksValue
                ];
                //TRY TO READ TOWER DATA
                $towerRow = $spanRow + 1;
                //WE WILL STOP READING IF WE FIND AN EMPTY CELL IN COLUMN E
                $towerTypeCell = $objWorksheet->getCell($this->input_tower_type_column . $towerRow);
                $towerTypeValue = $towerTypeCell->getValue();
                if ($towerTypeValue === NULL || $towerTypeValue === '') {
                    $empty = true;
                    $last_span = true;
                } else {
                    $towerNumValue = $objWorksheet->getCell($this->input_tower_num_column . $towerRow)->getValue();
                    $towerType2Value = $objWorksheet->getCell($this->input_tower_type2_column . $towerRow)->getValue();
                    $weightSpanValue = $objWorksheet->getCell($this->input_weight_span_column . $towerRow)->getValue();
                    $towers[] = [
                        $this->tower_num => $towerNumValue,
                        $this->tower_type => $towerTypeValue,
                        $this->tower_ext => $towerType2Value,
                        $this->weight_span => $weightSpanValue
                    ];
                    //advancing row
                    $spanRow+=2;
                }
            }
        }
        $ret = [
            'conductor' => $conductor,
            'circuit' => $circuit,
            'sc' => $sc,
            'w' => $w,
            'project' => $project,
            'tarikan' => $tarikan,
            'tension' => $tension,
            'spans' => $spans,
            'towers' => $towers,
            'conductorT' => $conductorT,
            'ew1' => $ew1,
            'ew2' => $ew2
        ];
        return $ret;
    }

    /**
     * Template 0 : File Input
     */
    private $template0_span_start = 18;
    private $template0_tower_ord_column = 'C';
    private $template0_tower_num_column = 'D';
    private $template0_tower_type_column = 'E';
    private $template0_tower_ext_column = 'F';
    private $template0_act_span_column = 'G';
    private $template0_weight_span_column = 'H';
    private $template0_crossing_rem_column = 'I';
    private $template0_tower_loc_column = 'J';
    private $template0_kelurahan_column = 'K';
    private $template0_kecamatan_column = 'L';
    private $template0_kabupaten_column = 'M';
    private $template0_span_columns = ['G', 'I', 'J', 'K', 'L', 'M'];
    private $template0_tower_columns = [ 'C', 'D', 'E', 'F', 'H'];

    public function save_input($data) {
        $objPHPExcel = PHPExcel_IOFactory::load('templates/input/tower_template.xlsx');
        //get first sheet
        $objSheet = $objPHPExcel->getSheet(0);
        //write data
        $spans = $data['spans'];
        $towers = $data['towers'];
        $objSheet->setCellValue('F3',$data['conductor']);
        $objSheet->setCellValue('F4',$data['circuit']);
        $objSheet->setCellValue('F5',$data['sc']);
        $objSheet->setCellValue('F6',$data['w']);
        $objSheet->setCellValue('F7',$data['tension']);
        $objSheet->setCellValue('F8',$data['tarikan']);
        $objSheet->setCellValue('J3',$data['project']);
        $spanRow = $this->template0_span_start;
        
        //already 2 tower rows and 3 span rows
        //making space (assuming there're more than 2 towers)
        $objSheet->insertNewRowBefore($spanRow + 4, 2 * (count($towers) - 2));
        //unmerge tower_columns
        $this->unmerge($objSheet, $spanRow + 3, 2 * (count($towers)) + $spanRow, $this->template0_tower_columns);
        
        //start writing span
        //the cell for first span is already available
        //as well as the cell for first tower and last span
        for ($i = 0; $i < count($spans); $i++) {
            set_time_limit(5);
            //SPAN
            $span = $spans[$i];
            $objSheet->setCellValue($this->template0_act_span_column.$spanRow, $span[$this->act_span]);
            $objSheet->setCellValue($this->template0_crossing_rem_column.$spanRow, $span[$this->crossing_rem]);
            //TOWER
            $towerRow = $spanRow + 1;
            $tower = $towers[$i];
            $objSheet->setCellValue($this->template0_tower_num_column . $towerRow, $tower[$this->tower_num]);
            $objSheet->setCellValue($this->template0_tower_type_column . $towerRow, $tower[$this->tower_type]);
            $objSheet->setCellValue($this->template0_tower_ext_column . $towerRow, $tower[$this->tower_ext]);
            $objSheet->setCellValue($this->template0_weight_span_column . $towerRow, $tower[$this->weight_span]);

            //advancing row (merge on span cols if necessary), see condition below
            $spanRow+=2;
            //always merge on tower cols
            $this->merge2($objSheet, $spanRow - 1, $this->template0_tower_columns);
            //checking last tower
            if ($i < (count($towers) - 1)) {
                //merge on span cols
                $this->merge2($objSheet, $spanRow, $this->template0_span_columns);
                //fill formulas
                //tower order
                $objSheet->setCellValue($this->template0_tower_ord_column . ($spanRow + 1), $i + 2);
            } else {
                //langsung isi span terakhir
                $i++;
                $span = $spans[$i];
                $objSheet->setCellValue($this->template0_act_span_column . $spanRow, $span[$this->act_span]);
                $objSheet->setCellValue($this->template0_crossing_rem_column . $spanRow, $span[$this->crossing_rem]);
            }            
        }
        
        //all data written
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save($data['filename']);
        return true;
    }

    /**
     * Template 1 : Tower Schedule
     */
    //TEMPLATE 1 : TOWER SCHEDULING
    private $template1_span_start = 13;
    private $template1_date = 'Q7';
    private $template1_tower_ord_column = 'B';
    private $template1_tower_num_column = 'C';
    private $template1_tower_type_column = 'D';
    private $template1_tower_ext_column = 'E';
    private $template1_act_span_column = 'F';
    private $template1_cum_span_column = 'G';
    private $template1_tension_span_column = 'H';
    private $template1_equiv_span_column = 'I';
    private $template1_weight_span_column = 'J';
    private $template1_wind_span_column = 'K';
    private $template1_wtwd_ratio_column = 'L';
    private $template1_crossing_rem_column = 'M';
    private $template1_span_columns = ['F', 'G', 'H', 'I', 'M'];
    private $template1_tower_columns = ['B', 'C', 'D', 'E', 'J', 'K', 'L', 'N', 'O', 'P', 'Q'];

    public function generate_tower_schedule($data) {
        $inputFileName = $data['file'];
        //baca file input
        $data = $this->readInput($inputFileName);
        //write data to output
        $templateFileName = 'templates/output/template1.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templateFileName);
        //get first sheet
        $objSheet = $objPHPExcel->getSheet(0);
        //informational
        $objSheet->setCellValue('N2', $data['project']);
        $objSheet->setCellValue('N4', $data['conductorT']);
        $objSheet->setCellValue('N5', $data['ew1']);
        $objSheet->setCellValue('O5', $data['ew2']);
        $spans = $data['spans'];
        $towers = $data['towers'];
        $spanRow = $this->template1_span_start;

        //making space
        $objSheet->insertNewRowBefore($spanRow + 2, 2 * (count($towers) - 1));
        //unmerge tower_columns
        $this->unmerge($objSheet, $spanRow + 1, 2 * (count($towers)) + $spanRow, $this->template1_tower_columns);

        //start writing span
        //the cell for first span is already available
        //as well as the cell for first tower and last span
        for ($i = 0; $i < count($spans); $i++) {
            set_time_limit(5);
            //SPAN
            $span = $spans[$i];
            $objSheet->setCellValue("$this->template1_act_span_column$spanRow", $span[$this->act_span]);
            $objSheet->setCellValue("$this->template1_crossing_rem_column$spanRow", $span[$this->crossing_rem]);
            //TOWER
            $towerRow = $spanRow + 1;
            $tower = $towers[$i];
            $objSheet->setCellValue($this->template1_tower_num_column . $towerRow, $tower[$this->tower_num]);
            $objSheet->setCellValue($this->template1_tower_type_column . $towerRow, $tower[$this->tower_type]);
            $objSheet->setCellValue($this->template1_tower_ext_column . $towerRow, $tower[$this->tower_ext]);
            $objSheet->setCellValue($this->template1_weight_span_column . $towerRow, $tower[$this->weight_span]);

            //advancing row (merge on span cols if necessary), see condition below
            $spanRow+=2;
            //always merge on tower cols
            $this->merge2($objSheet, $spanRow - 1, $this->template1_tower_columns);
            //checking last tower
            if ($i < (count($towers) - 1)) {
                //merge on span cols
                $this->merge2($objSheet, $spanRow, $this->template1_span_columns);
                //fill formulas
                //tower order
                $objSheet->setCellValue($this->template1_tower_ord_column . ($spanRow + 1), $i + 2);
                //span tension
                $objSheet->setCellValue($this->template1_tension_span_column . $spanRow, '=' . $this->template1_act_span_column . $spanRow);
                //equiv tension
                $objSheet->setCellValue($this->template1_equiv_span_column . $spanRow, '=' . $this->template1_act_span_column . $spanRow);
            } else {
                //langsung isi span terakhir
                $i++;
                $span = $spans[$i];
                $objSheet->setCellValue($this->template1_act_span_column . $spanRow, $span[$this->act_span]);
                $objSheet->setCellValue($this->template1_crossing_rem_column . $spanRow, $span[$this->crossing_rem]);
            }
            //cumulative span
            $objSheet->setCellValue($this->template1_cum_span_column . $spanRow, '=' . $this->template1_cum_span_column . ($spanRow - 2) . '+' . $this->template1_act_span_column . $spanRow);
            //wind span
            $objSheet->setCellValue($this->template1_wind_span_column . $towerRow, '=(' . $this->template1_act_span_column . ($spanRow - 2) . '+' . $this->template1_act_span_column . $spanRow . ')/2');
            //weight/wind ratio
            $objSheet->setCellValue($this->template1_wtwd_ratio_column . $towerRow, '=' . $this->template1_weight_span_column . $towerRow . '/' . $this->template1_wind_span_column . $towerRow);
        }
        //written date
        $objSheet->setCellValue($this->template1_date, 25569 + (time() / (3600 * 24)));

        //all data written
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        // We'll be outputting an excel file
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // It will be called tower_schedule.xlsx
        header('Content-Disposition: attachment; filename="tower_schedule.xlsx"');

        // Write file to the browser
        $objWriter->save('php://output');
    }

    //TEMPLATE 2 : MATERIAL SCHEDULING
    private $template2_span_start = 13;
    private $template2_date = 'Y7';
    private $template2_tower_ord_column = 'B';
    private $template2_tower_num_column = 'C';
    private $template2_tower_type_column = 'D';
    private $template2_tower_ext_column = 'E';
    private $template2_act_span_column = 'F';
    private $template2_cum_span_column = 'G';
    private $template2_crossing_rem_column = 'H';
    private $template2_single_sus_column = 'I';
    private $template2_single_ten_column = 'K';
    private $template2_double_sus_column = 'J';
    private $template2_double_ten_column = 'L';
    private $template2_double_inv_column = 'M'; //not used
    private $template2_jumper_ins_column = 'N';
    private $template2_ten_gsw_column = 'O';
    private $template2_ten_opgw_column = 'P';
    private $template2_sus_gsw_column = 'Q';
    private $template2_sus_opgw_column = 'R';
    private $template2_damping_acsr_column = 'S';
    private $template2_damping_gsw_column = 'T';
    private $template2_damping_opgw_column = 'U';
    private $template2_rod_acsr_column = 'V';
    private $template2_rod_gsw_column = 'W';
    private $template2_rod_opgw_column = 'X';
    private $template2_spacer_line_column = 'Y';
    private $template2_spacer_jumper_column = 'Z';
    private $template2_span_columns = ['F', 'G', 'H', 'Y'];
    private $template2_tower_columns = ['B', 'C', 'D', 'E', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Z'];

    public function generate_material_schedule($datax) {
        $inputFileName = $datax['file'];
        //baca file input
        $data = $this->readInput($inputFileName);
        //write data to output
        $templateFileName = 'templates/output/template2.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templateFileName);
        //get first sheet
        $objSheet = $objPHPExcel->getSheet(0);
        //informational
        $objSheet->setCellValue('U2', $data['project']);
        $objSheet->setCellValue('U4', $data['conductorT']);
        $objSheet->setCellValue('U5', $data['ew1']);
        $objSheet->setCellValue('V5', $data['ew2']);
        $objSheet->setCellValue('A8', $data['circuit']);
        $spans = $data['spans'];
        $towers = $data['towers'];
        $spanRow = $this->template2_span_start;

        //making space
        $objSheet->insertNewRowBefore($spanRow + 2, 2 * (count($towers) - 1));
        //unmerge tower_columns
        $this->unmerge($objSheet, $spanRow + 1, 2 * (count($towers)) + $spanRow, $this->template2_tower_columns);

        //start writing span
        //the cell for first span is already available
        //as well as the cell for first tower and last span
        for ($i = 0; $i < count($spans); $i++) {
            set_time_limit(5);
            //SPAN
            $span = $spans[$i];
            $objSheet->setCellValue("$this->template2_act_span_column$spanRow", $span[$this->act_span]);
            $objSheet->setCellValue("$this->template2_crossing_rem_column$spanRow", $span[$this->crossing_rem]);
            //TOWER
            $towerRow = $spanRow + 1;
            $tower = $towers[$i];
            $objSheet->setCellValue($this->template2_tower_num_column . $towerRow, $tower[$this->tower_num]);
            $objSheet->setCellValue($this->template2_tower_type_column . $towerRow, $tower[$this->tower_type]);
            $objSheet->setCellValue($this->template2_tower_ext_column . $towerRow, $tower[$this->tower_ext]);

            //advancing row (merge on span cols if necessary), see condition below
            $spanRow+=2;
            //always merge on tower cols
            $this->merge2($objSheet, $spanRow - 1, $this->template2_tower_columns);
            //checking last tower
            if ($i < (count($towers) - 1)) {
                //merge on span cols
                $this->merge2($objSheet, $spanRow, $this->template2_span_columns);
                //fill formulas
                //tower order
                $objSheet->setCellValue($this->template2_tower_ord_column . ($spanRow + 1), $i + 2);
                //span jumper line
                $objSheet->setCellValue($this->template2_spacer_line_column . $spanRow, '=IF($A$9=2,ROUND(' . $this->template2_act_span_column . $spanRow . '/50,0)*$A$8*3,"")');
            } else {
                //langsung isi span terakhir
                $i++;
                $span = $spans[$i];
                $objSheet->setCellValue($this->template2_act_span_column . $spanRow, $span[$this->act_span]);
                $objSheet->setCellValue($this->template2_crossing_rem_column . $spanRow, $span[$this->crossing_rem]);
            }

            //cumulative span
            $objSheet->setCellValue($this->template2_cum_span_column . $spanRow, '=' . $this->template1_cum_span_column . ($spanRow - 2) . '+' . $this->template1_act_span_column . $spanRow);
            //single suspension
            $objSheet->setCellValue($this->template2_single_sus_column . $towerRow, '=IF(LEFT(' . $this->template2_tower_type_column . $towerRow . ',1)="a",IF(AND(COUNTIF(doublecross,' . $this->template2_crossing_rem_column . $spanRow . ')=0,COUNTIF(doublecross,' . $this->template2_crossing_rem_column . ($spanRow - 2) . ')=0),3*$A$8,""),"")');
            //double suspension
            $objSheet->setCellValue($this->template2_double_sus_column . $towerRow, '=IF(AND(LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',1)="a",OR(COUNTIF(doublecross,'
                    . $this->template2_crossing_rem_column . $spanRow .
                    ')>0,COUNTIF(doublecross,'
                    . $this->template2_crossing_rem_column . ($spanRow - 2) .
                    ')>0)),3*$A$8,"")');
            //single tension
            $objSheet->setCellValue($this->template2_single_ten_column . $towerRow, '=IF(AND(LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',1)<>"a",LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',3)<>"ddr",COUNTIF(doublecross,'
                    . $this->template2_crossing_rem_column . $spanRow .
                    ')=0,COUNTIF(doublecross,'
                    . $this->template2_crossing_rem_column . ($spanRow - 2) .
                    ')=0),6*$A$8,"")');
            //double tension
            $objSheet->setCellValue($this->template2_double_ten_column . $towerRow, '=IF(LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',1)<>"a",(IF(LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',3)="ddr",3*$A$8,IF(OR(COUNTIF(doublecross,'
                    . $this->template2_crossing_rem_column . $spanRow .
                    ')>0,COUNTIF(doublecross,'
                    . $this->template2_crossing_rem_column . ($spanRow - 2) .
                    ')>0),6*$A$8,""))),"")');
            //jumper insulator
            $objSheet->setCellValue($this->template2_jumper_ins_column . $towerRow, '=IF(LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',2)="cc",3,IF(OR(LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',2)="ee",LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',2)="dd"),6,""))');
            //tension gsw
            $objSheet->setCellValue($this->template2_ten_gsw_column . $towerRow, '=IF(LEFT(' . $this->template2_tower_type_column . $towerRow . ',1)<>"a", 2,"")');
            //tension opgw
            $objSheet->setCellValue($this->template2_ten_opgw_column . $towerRow, '=' . $this->template2_ten_gsw_column . $towerRow);
            //suspension gsw
            $objSheet->setCellValue($this->template2_sus_gsw_column . $towerRow, '=IF(LEFT(' . $this->template2_tower_type_column . $towerRow . ',1)="a", 1,"")');
            //suspension opgw
            $objSheet->setCellValue($this->template2_sus_opgw_column . $towerRow, '=' . $this->template2_sus_gsw_column . $towerRow);
            //damping acsr
            $objSheet->setCellValue($this->template2_damping_acsr_column . $towerRow, '=IF(OR('
                    . $this->template2_act_span_column . $spanRow .
                    '>450,'
                    . $this->template2_act_span_column . ($spanRow - 2) .
                    '>450),9*$A$8*$A$9,6*$A$8*$A$9)');
            //damping gsw
            $objSheet->setCellValue($this->template2_damping_gsw_column . $towerRow, '=IF(OR('
                    . $this->template2_act_span_column . $spanRow .
                    '>450,'
                    . $this->template2_act_span_column . ($spanRow - 2) .
                    '>450),3,2)');
            //damping osgw
            $objSheet->setCellValue($this->template2_damping_opgw_column . $towerRow, '=' . $this->template2_damping_gsw_column . $towerRow);
            //armour rod acsr
            $objSheet->setCellValue($this->template2_rod_acsr_column . $towerRow, '=IF(LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',1)="a", 3*$A$8*$A$9,"")');
            //armour rod gsw
            $objSheet->setCellValue($this->template2_rod_gsw_column . $towerRow, '=IF(LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',1)="a", 1,"")');
            //armour rod opgw
            $objSheet->setCellValue($this->template2_rod_opgw_column . $towerRow, '=' . $this->template2_rod_gsw_column . $towerRow);
            //jumper 200mm
            $objSheet->setCellValue($this->template2_spacer_jumper_column . $towerRow, '=IF(LEFT('
                    . $this->template2_tower_type_column . $towerRow .
                    ',1)<>"a", $A$8*3*2,"")');
        }

        //written date
        $objSheet->setCellValue($this->template2_date, 25569 + (time() / (3600 * 24)));
        //all data written
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        // We'll be outputting an excel file
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // It will be called material_schedule.xlsx
        header('Content-Disposition: attachment; filename="material_schedule.xlsx"');

        // Write file to the browser
        $objWriter->save('php://output');
    }

    /**
     * Template 3 : Sagging schedule
     */
    private $template3_span_start = 13;
    private $template3_date = 'Q7';
    //span columns
    private $template3_act_span_column = 'E';
    private $template3_cum_span_column = 'F';
    private $template3_wire_weight_column = 'G';
    private $template3_wire_tension_column = 'H';
    private $template3_t_column = 'I';
    private $template3_coef_column = 'J';
    private $template3_wl2_column = 'K';
    private $template3_8t_column = 'L';
    private $template3_sagging_column = 'M';
    private $template3_wire_ext_column = 'N';
    private $template3_wire_total_column = 'O';
    private $template3_span_columns = ['E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
    //tower column
    private $template3_tower_num_column = 'B';
    private $template3_tower_type_column = 'C';
    private $template3_tower_ext_column = 'D';
    private $template3_weight_span_column = 'P';
    private $template3_wind_span_column = 'Q';
    private $template3_wtwd_span_column = 'R';
    private $template3_tower_columns = ['B', 'C', 'D', 'P', 'Q', 'R'];

    public function generate_sagging_schedule($post) {
        $inputFileName = $post['file'];
        //baca file input
        $data = $this->readInput($inputFileName);
        //write data to output
        $templateFileName = 'templates/output/template3.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templateFileName);
        //get first sheet
        $objSheet = $objPHPExcel->getSheet(0);
        //informational
        $objSheet->setCellValue('M2', $data['project']);
        $objSheet->setCellValue('M4', $data['conductorT']);
        $objSheet->setCellValue('M5', $data['ew1']);
        $objSheet->setCellValue('N5', $data['ew2']);
        $spans = $data['spans'];
        $towers = $data['towers'];
        $tension = $data['tension'];
        $sagging_coef = $data['sc'];
        $w = $data['w'];
        $spanRow = $this->template3_span_start;

        //making space
        $objSheet->insertNewRowBefore($spanRow + 2, 2 * (count($towers) - 1));
        //unmerge tower_columns
        $this->unmerge($objSheet, $spanRow + 1, 2 * (count($towers)) + $spanRow, $this->template3_tower_columns);

        //start writing span
        //the cell for first span is already available
        //as well as the cell for first tower and last span
        for ($i = 0; $i < count($spans); $i++) {
            set_time_limit(5);
            //SPAN
            $span = $spans[$i];
            $objSheet->setCellValue("$this->template3_act_span_column$spanRow", $span[$this->act_span]);
            //TOWER
            $towerRow = $spanRow + 1;
            $tower = $towers[$i];
            $objSheet->setCellValue($this->template3_tower_num_column . $towerRow, $tower[$this->tower_num]);
            $objSheet->setCellValue($this->template3_tower_type_column . $towerRow, $tower[$this->tower_type]);
            $objSheet->setCellValue($this->template3_tower_ext_column . $towerRow, $tower[$this->tower_ext]);
            $objSheet->setCellValue($this->template3_weight_span_column . $towerRow, $tower[$this->weight_span]);

            //advancing row (merge on span cols if necessary), see condition below
            $spanRow+=2;
            //always merge on tower cols
            $this->merge2($objSheet, $spanRow - 1, $this->template3_tower_columns);
            //checking last tower
            if ($i < (count($towers) - 1)) {
                //merge on span cols
                $this->merge2($objSheet, $spanRow, $this->template3_span_columns);
                //fill formulas
                //span jumper line
                //$objSheet->setCellValue($this->template2_spacer_line_column . $spanRow, '=IF($A$9=2,ROUND(' . $this->template2_act_span_column . $spanRow . '/50,0)*$A$8*3,"")');
            } else {
                //langsung isi span terakhir
                $i++;
                $span = $spans[$i];
                $objSheet->setCellValue($this->template3_act_span_column . $spanRow, $span[$this->act_span]);
                //berat kawat
                $objSheet->setCellValue($this->template3_wire_weight_column . $spanRow, $w);
                //tension kawat
                $objSheet->setCellValue($this->template3_wire_tension_column . $spanRow, $tension);
                //koef kawat
                $objSheet->setCellValue($this->template3_coef_column . $spanRow, $sagging_coef);
            }
            //wind span
            $objSheet->setCellValue($this->template3_wind_span_column . $towerRow, '=(' . $this->template3_act_span_column . ($spanRow - 2) . '+' . $this->template3_act_span_column . $spanRow . ')/2');
            //weight/wind ratio
            $objSheet->setCellValue($this->template3_wtwd_span_column . $towerRow, '=' . $this->template3_weight_span_column . $towerRow . '/' . $this->template3_wind_span_column . $towerRow);
            //berat kawat
            $objSheet->setCellValue($this->template3_wire_weight_column . ($spanRow - 2), $w);
            //tension kawat
            $objSheet->setCellValue($this->template3_wire_tension_column . ($spanRow - 2), $tension);
            //tension*0.2 kawat
            $objSheet->setCellValue($this->template3_t_column . ($spanRow - 2), '=0.2*' . $this->template3_wire_tension_column . ($spanRow - 2));
            //koef kawat
            $objSheet->setCellValue($this->template3_coef_column . ($spanRow - 2), $sagging_coef);
            //WL^2
            $objSheet->setCellValue($this->template3_wl2_column . ($spanRow - 2), '=' .
                    $this->template3_act_span_column . ($spanRow - 2) . '*' .
                    $this->template3_act_span_column . ($spanRow - 2) . '*' .
                    $this->template3_wire_weight_column . ($spanRow - 2) . '');
            //8T
            $objSheet->setCellValue($this->template3_8t_column . ($spanRow - 2), '=8*' . $this->template3_t_column . ($spanRow - 2));
            //sagging
            $objSheet->setCellValue($this->template3_sagging_column . ($spanRow - 2), '=' . $this->template3_wl2_column . ($spanRow - 2) . '/' . $this->template3_8t_column . ($spanRow - 2) . '/1000');
            //sagging ext
            $objSheet->setCellValue($this->template3_wire_ext_column . ($spanRow - 2), '=8*' . $this->template3_sagging_column . ($spanRow - 2) . '*' . $this->template3_sagging_column . ($spanRow - 2) . '/300');
            //total wire length
            $objSheet->setCellValue($this->template3_wire_total_column . ($spanRow - 2), '=' . $this->template3_act_span_column . ($spanRow - 2) . '+' . $this->template3_wire_ext_column . ($spanRow - 2));
            //cumulative span
            $objSheet->setCellValue($this->template3_cum_span_column . $spanRow, '=' . $this->template3_cum_span_column . ($spanRow - 2) . '+' . $this->template3_act_span_column . $spanRow);
//            $objSheet->setCellValue($this->template3_wire_tension_column . $spanRow,$tension);
        }


        //written date
        $objSheet->setCellValue($this->template3_date, 25569 + (time() / (3600 * 24)));

        //all data written
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        // We'll be outputting an excel file
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // It will be called tower_schedule.xlsx
        header('Content-Disposition: attachment; filename="sagging_schedule.xlsx"');

        // Write file to the browser
        $objWriter->save('php://output');
    }

    /**
     * Template 4 : Drum Schedule
     */
    private $template4_date = 'AC7';

    public function generate_drum_schedule($file) {
        $inputFileName = $file['file'];
        //baca file input
        $data = $this->readInput($inputFileName);
        //write data to output
        $templateFileName = 'templates/output/template4.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templateFileName);
        $spans = $data['spans'];
        $towers = $data['towers'];
        //awal tarikan
        if($data['tarikan']==='besar'){
            //reverse
            $spans = array_reverse($spans);
            $towers = array_reverse($towers);
        }

        //template 4 ini berbeda, sebuah sheet hanya dapat menampung 
        //maximal 25 tower (termasuk start-finish)
        //jika ternyata jumlah tower dalam file input lebih dari 25, maka akan
        //dibuat clone dari sheet 1
        //dan dilanjutkan mengisinya di sheet tsb
        $towerNum = count($towers);
        $towerAll = $towerNum + 2;
        //tapi sheet baru ini dibutuhkan juga apabila pindah section
        //karena section sebelumnya belum sampai 25 tower sudah lebih dari 3600m
        //dan harus diputus tarikannya
//            $objClonedWorksheet->setTitle('Section ' . ($i + 1));
//            $this->template4_info($objClonedWorksheet, $data);
//            $objPHPExcel->addSheet($objClonedWorksheet);
        //dst
        //get first sheet
        $objSheet = $objPHPExcel->getSheet(0);
        $objClonedWorksheet = clone $objSheet; //reference to an empty sheet
        $this->template4_info($objSheet, $data);
        $tower_in_sheet = 2;
        $sheetcounter = 1;
        $towercounter = 2;
        //starting tower udah terisi
        for ($i = 0; $i < count($spans); $i++) {
            log_message('debug', $i);
            $span = $spans[$i];
            log_message('debug', json_encode($span));
            $tower = $towers[$i];
            log_message('debug', json_encode($tower));
            $prev_span = $i > 0 ? $spans[$i - 1] : null;
            $prev_tower = $i > 0 ? $towers[$i - 1] : null;
            //write tower to sheet
            $this->write_tower($objSheet, $tower, $tower_in_sheet, $towercounter);
            //write span
            $calculated_cum = $this->write_span($objSheet, $span, $tower_in_sheet, $data['w'], $data['tension']);
            log_message('debug', $calculated_cum . ' saat menuju $tc = ' . $towercounter . ' pada $i= ' . $i);
            //setelah write span, cek apakah masih bisa lanjut atau tarikan yang sekarang
            //harus diakhiri
            //cek nya dengan melihat nilai toleransi kumulatif di span yang barusan dibuat
            //dan juga melihat kondisi last tower dan next tower
            //jika next value dari kumulatif span di next span itu sudah jebol 3600, maka harus diakhiri
            //mengakhirinya bisa dengan putus tarikan, atau lanjut pakai midspan joint.
            //midspan joint hanya bisa dilakukan jika last_tower dan next_tower keduanya bertipe
            //suspension (AA) dan berjarak minimal (act_span) 100m

            if ($calculated_cum >= 3600) {
                log_message('debug', 'jebol ' . $calculated_cum . ' saat menuju $tc = ' . $towercounter);
                //masih ada harapan apabila prev_tower dan tower keduanya bertipe AA
                if (
                        startsWith(strtolower($prev_tower[$this->tower_type]), 'aa') &&
                        startsWith(strtolower($tower[$this->tower_type]), 'aa')
                ) {
                    //bikin midspan joint

                    break;
                } else {
                    //rollback
                    //- hapus $tower dari current sheet
                    //- hapus $span dari current sheet
                    //- kasih hiasan penutup
                    $this->close_tarikan($objSheet, $tower_in_sheet);
                    //create new sheet
                    $newSheet = clone $objClonedWorksheet;
                    //increment $sheetcounter
                    $newSheet->setTitle('Section ' . ++$sheetcounter);
                    $this->template4_info($newSheet, $data);
                    //add new sheet
                    $objPHPExcel->addSheet($newSheet);

                    //======================FLUSH INSTANCE=================
                    //http://stackoverflow.com/questions/35169797/phpexcel-getcalculatedvalue-not-returning-desired-results
                    PHPExcel_Calculation::getInstance(
                            $objPHPExcel
                    )->flushInstance();


                    //use new sheet
                    log_message('debug', 'pindah sheet');
                    $objSheet = $objPHPExcel->getSheet($sheetcounter - 1);
                    //reset counter $tower_in_sheet
                    $tower_in_sheet = 1;
                    //reset counter $towercounter
                    $towercounter = 1;
                    //write $prev_tower dan $prev_span as the first tower in the new sheet
                    $this->write_tower($objSheet, $prev_tower, $tower_in_sheet, $towercounter);
                    //write span (asumsinya ini ga mungkin langsung jebol 3.6km)
                    //
                    //roll the counter back to accomodate the $tower that just deleted
                    --$i;
                }
            } else
            //lanjut to next tower
            //tapi kita cek jangan2 sudah sheet yang sekarang sudah penuh
            if ($tower_in_sheet == 25) {
                //create new sheet
                //add new sheet
                //use new sheet
                //reset counter $tower_in_sheet
                //write $tower ke new sheet
//                break;
            }
            //increment counter
            $towercounter++;
            $tower_in_sheet++;


            //checking last tower
            if ($i < (count($towers) - 1)) {
                
            } else {
                //langsung isi span terakhir
                $i++;
                $span = $spans[$i];
                $this->write_span($objSheet, $span, $tower_in_sheet, $data['w'], $data['tension']);
                $this->close_tarikan($objSheet, $tower_in_sheet + 1, $towercounter, true);
            }
        }


        //all data written
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        // We'll be outputting an excel file
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // It will be called tower_schedule.xlsx
        header('Content-Disposition: attachment; filename="drum_schedule.xlsx"');

        // Write file to the browser
        $objWriter->save('php://output');
    }

    private function close_tarikan(PHPExcel_Worksheet $sheet, $tower_in_sheet, $towercounter = 0, $end_of_towers = false) {
        $penutup_style = [
            //fill
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'argb' => 'FFFABF8F',
                )
            ),
            //outer border only
            'borders' => array(
                'inside' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                ),
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE
                )
            )
        ];
        $last_cum_style = [
            //fill
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array(
                    'argb' => 'FF92D050',
                )
            ),
        ];

        //hapus last tower
        if ($tower_in_sheet <= 13) {
            //seek the appr column
            $col = inc_col('E', 2 * ($tower_in_sheet - 1));
            //write in the first row
            if ($end_of_towers) {
                $ccol = inc_col('E', 2 * ($tower_in_sheet - 2));
                $sheet->setCellValue($ccol . '17', 'END');
                $sheet->setCellValue($ccol . '18', 'END');
                $sheet->mergeCells($ccol . '18:' . ++$ccol . '18');
            } else {
                $this->_flush_tower($sheet, $col, 16);
            }
        }
        if ($tower_in_sheet == 13 || $tower_in_sheet == 14) {
            $col = 'E';
            //write tower without prev span (2nd row)
            $this->_flush_tower($sheet, $col, 34);
        }
        if ($tower_in_sheet > 13) {
            $col = inc_col('E', 2 * ($tower_in_sheet - 13));
            if ($end_of_towers) {
                if ($tower_in_sheet == 14) {
                    //write in the first row
                    $ccol = inc_col('E', 2 * ($tower_in_sheet - 2));
                    $sheet->setCellValue($ccol . '17', 'END');
                    $sheet->setCellValue($ccol . '18', 'END');
                    $sheet->mergeCells($ccol . '18:' . ++$ccol . '18');
                } else {
                    //write in the 2nd row
                    $ccol = inc_col('E', 2 * ($tower_in_sheet - 14));
                    $sheet->setCellValue($ccol . '35', 'END');
                    $sheet->setCellValue($ccol . '36', 'END');
                    $sheet->mergeCells($ccol . '36:' . ++$ccol . '36');
                }
            } else {
                $this->_flush_tower($sheet, $col, 34);
            }
        }
        //hapus last span
        if (1 < $tower_in_sheet && $tower_in_sheet <= 13) {
            //first row
            //actual span
            //seek the appr column
            $col = 'F';
            $prevcol = 'D';
            for ($i = 2; $i < $tower_in_sheet; $i++) {
                ++$col;
                ++$col;
                ++$prevcol;
                ++$prevcol;
            }
            //actual span
            $sheet->setCellValue($col . '19');
            //span tension
            $sheet->setCellValue($col . '21');
            $sheet->setCellValue($col . '22');
            $sheet->setCellValue($col . '23');
            //cum span
            $sheet->setCellValue($col . '20');
            $sheet->setCellValue($col . '24');
            //unmerge
            $sheet->unmergeCells($col . '19:' . inc_col($col, 1) . '19');
            $sheet->unmergeCells($col . '20:' . inc_col($col, 1) . '20');
            $sheet->unmergeCells($col . '21:' . inc_col($col, 1) . '21');
            $sheet->unmergeCells($col . '22:' . inc_col($col, 1) . '22');
            $sheet->unmergeCells($col . '23:' . inc_col($col, 1) . '23');
            $sheet->unmergeCells($col . '24:' . inc_col($col, 1) . '24');
            $sheet->unmergeCells($col . '25:' . inc_col($col, 1) . '25');
            $sheet->getStyle($col . "19:" . $col . "25")->applyFromArray($penutup_style);
        } else {
            //second row
            $col = 'F';
            $prevcol = 'D';
            for ($i = 1; $i < $tower_in_sheet - 13; $i++) {
                ++$col;
                ++$col;
                ++$prevcol;
                ++$prevcol;
            }
            $sheet->setCellValue($col . '37');
            $sheet->setCellValue($col . '39');
            $sheet->setCellValue($col . '40');
            $sheet->setCellValue($col . '41');
            //cum span
            $sheet->setCellValue($col . '38');
            $sheet->setCellValue($col . '42');
            if ($tower_in_sheet == 14) {
                //bkin hiasan  kolom penutup
                $sheet->getStyle("AD19:AD25")->applyFromArray($penutup_style);
            } else if ($tower_in_sheet < 26) {
                $next_col = inc_col($col, 1);
                //unmerge
                $sheet->unmergeCells($col . '37:' . $next_col . '37');
                $sheet->unmergeCells($col . '38:' . $next_col . '38');
                $sheet->unmergeCells($col . '39:' . $next_col . '39');
                $sheet->unmergeCells($col . '40:' . $next_col . '40');
                $sheet->unmergeCells($col . '41:' . $next_col . '41');
                $sheet->unmergeCells($col . '42:' . $next_col . '42');
                $sheet->unmergeCells($col . '43:' . $next_col . '43');
                //bkin hiasan  kolom penutup
                $sheet->getStyle($col . "37:" . $col . "43")->applyFromArray($penutup_style);
            }
        }
        //kasih hijau2 di cumulative terakhir
        if ($tower_in_sheet <= 14) {
            $col = 'F';

            //write in the first row
            $sheet->getStyle(inc_col($col, 2 * ($tower_in_sheet - 3)) . '24')->applyFromArray($last_cum_style);
        } else {
            $col = 'F';
            //write in the second row
            $sheet->getStyle(inc_col($col, 2 * ($tower_in_sheet - 15)) . '42')->applyFromArray($last_cum_style);
        }
    }

    private function write_span(PHPExcel_Worksheet $sheet, $span, $tower_in_sheet, $w, $tension) {
        log_message('debug', 'write span in ' . $sheet->getTitle() . ' with tis=' . $tower_in_sheet);
        if (1 == $tower_in_sheet) {
            $cum = 0;
        } else if (1 < $tower_in_sheet && $tower_in_sheet <= 13) {
            //first row
            //actual span
            //seek the appr column
            $col = 'F';
            $prevcol = 'D';
            for ($i = 2; $i < $tower_in_sheet; $i++) {
                ++$col;
                ++$col;
                ++$prevcol;
                ++$prevcol;
            }
            //actual span
            $sheet->setCellValue($col . '19', $span[$this->act_span]);
            //span tension
            $sheet->setCellValue($col . '21', $span[$this->act_span]);
            $sheet->setCellValue($col . '22', '=(8*(' . $w . '*' . $col . '21^2/(8000*0.2*' . $tension . '))^2)/300');
            $sheet->setCellValue($col . '23', '=' . $col . '22+' . $col . '21');
            //cum span
            $sheet->setCellValue($col . '20', '=' . $col . '19+' . $prevcol . '20');
            $sheet->setCellValue($col . '24', '=' . $col . '23+' . $prevcol . '24');
            $cum = $sheet->getCell($col . '24')->getCalculatedValue();
        } else {
            //second row
            $col = 'F';
            $prevcol = 'D';
            for ($i = 1; $i < $tower_in_sheet - 13; $i++) {
                ++$col;
                ++$col;
                ++$prevcol;
                ++$prevcol;
            }
            $sheet->setCellValue($col . '37', $span[$this->act_span]);
            $sheet->setCellValue($col . '39', $span[$this->act_span]);
            $sheet->setCellValue($col . '40', '=(8*(' . $w . '*' . $col . '39^2/(8000*0.2*' . $tension . '))^2)/300');
            $sheet->setCellValue($col . '41', '=' . $col . '40+' . $col . '39');
            if ($tower_in_sheet > 14) {
                //cum span
                $sheet->setCellValue($col . '38', '=' . $col . '37+' . $prevcol . '38');
                $sheet->setCellValue($col . '42', '=' . $col . '41+' . $prevcol . '42');
            }
            $cum = $sheet->getCell($col . '42')->getCalculatedValue();
        }
        log_message('debug', 'cum? ' . $cum);
        return $cum;
    }

    private function write_tower(PHPExcel_Worksheet $sheet, $tower, $tower_in_sheet, $towercounter) {
        if ($tower_in_sheet <= 13) {
            //seek the appr column
            $col = inc_col('E', 2 * ($tower_in_sheet - 1));

            //write in the first row
            $this->_write_tower($sheet, $col, 16, $tower, $towercounter);
        }
        if ($tower_in_sheet == 13) {
            $col = 'E';
            //write tower without prev span (2nd row)
            $this->_write_tower($sheet, $col, 34, $tower, $towercounter);
        }
        if ($tower_in_sheet > 13) {
            $col = inc_col('E', 2 * ($tower_in_sheet - 13));

            $this->_write_tower($sheet, $col, 34, $tower, $towercounter);
        }
    }

    private function _write_tower(PHPExcel_Worksheet $sheet, $col, $row, $tower, $towercounter) {
        $sheet->setCellValue($col . $row++, $towercounter);
        $sheet->setCellValue($col . $row++, $tower[$this->tower_num]);
        $sheet->setCellValue($col . $row, $tower[$this->tower_type]);
        $sheet->setCellValue( ++$col . $row, $tower[$this->tower_ext]);
    }

    private function _flush_tower(PHPExcel_Worksheet $sheet, $col, $row) {
        $sheet->setCellValue($col . $row++);
        $sheet->setCellValue($col . $row++);
        $sheet->setCellValue($col . $row);
        $sheet->setCellValue( ++$col . $row);
    }

    /**
     * Paling banyak ada 25 element di array $towers
     * @param PHPExcel_Worksheet $sheet
     * @param type $towers tower yang akan ditulis di sheet ini
     */
    public function create_towers(PHPExcel_Worksheet $sheet, $towers, $isFirst, $isLast, $counter) {
        if (!$isFirst) {
            $tower = $towers[0];
            //first tower
            $sheet->setCellValue('E16', $counter);
            $sheet->setCellValue('E17', $tower[$this->tower_num]);
            $sheet->setCellValue('E16', $counter);
        }
    }

    public function template4_info(PHPExcel_Worksheet $objSheet, $data) {
        //informational
        $objSheet->setCellValue('W2', $data['project']);
        $objSheet->setCellValue('W4', $data['conductorT']);
        $objSheet->setCellValue('W5', $data['ew1']);
        $objSheet->setCellValue('X5', $data['ew2']);

        //written date
        $objSheet->setCellValue($this->template4_date, 25569 + (time() / (3600 * 24)));
    }

    //helper function
    private function merge2(PHPExcel_Worksheet $sheet, $rownum, $cols) {
        foreach ($cols as $col) {
            $rownum1 = $rownum + 1;
            $sheet->mergeCells("$col$rownum:$col$rownum1");
            //kasih border bawah
            $sheet->getStyle($col . $rownum1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        }
    }

    private function unmerge(PHPExcel_Worksheet $sheet, $toprow, $botrow, $cols) {
        foreach ($cols as $col) {
            $sheet->unmergeCells("$col$toprow:$col$botrow");
        }
    }

}
