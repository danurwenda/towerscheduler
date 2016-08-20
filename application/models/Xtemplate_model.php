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
    private $tower_type2 = 'tower_type2';
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
                    $this->act_span => [$this->input_act_span_column . $spanRow => $spanValue],
                    $this->crossing_rem => [$this->input_crossing_rem_column . $spanRow => $crossRemarksValue]
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
                        $this->tower_num => [$this->input_tower_num_column . $towerRow => $towerNumValue],
                        $this->tower_type => [$this->input_tower_type_column . $towerRow => $towerTypeValue],
                        $this->tower_type2 => [$this->input_tower_type2_column . $towerRow => $towerType2Value],
                        $this->weight_span => [$this->input_weight_span_column . $towerRow => $weightSpanValue],
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
            foreach ($span[$this->act_span] as $k => $v) {
                $objSheet->setCellValue("$this->template1_act_span_column$spanRow", $v);
            }
            foreach ($span[$this->crossing_rem] as $k => $v) {
                $objSheet->setCellValue("$this->template1_crossing_rem_column$spanRow", $v);
            }
            //TOWER
            $towerRow = $spanRow + 1;
            $tower = $towers[$i];
            foreach ($tower[$this->tower_num] as $k => $v) {
                $objSheet->setCellValue($this->template1_tower_num_column . $towerRow, $v);
            }
            foreach ($tower[$this->tower_type] as $k => $v) {
                $objSheet->setCellValue($this->template1_tower_type_column . $towerRow, $v);
            }
            foreach ($tower[$this->tower_type2] as $k => $v) {
                $objSheet->setCellValue($this->template1_tower_ext_column . $towerRow, $v);
            }
            foreach ($tower[$this->weight_span] as $k => $v) {
                $objSheet->setCellValue($this->template1_weight_span_column . $towerRow, $v);
            }

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
                foreach ($span[$this->act_span] as $k => $v) {
                    $objSheet->setCellValue($this->template1_act_span_column . $spanRow, $v);
                }
                foreach ($span[$this->crossing_rem] as $k => $v) {
                    $objSheet->setCellValue($this->template1_crossing_rem_column . $spanRow, $v);
                }
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
            foreach ($span[$this->act_span] as $k => $v) {
                $objSheet->setCellValue("$this->template2_act_span_column$spanRow", $v);
            }
            foreach ($span[$this->crossing_rem] as $k => $v) {
                $objSheet->setCellValue("$this->template2_crossing_rem_column$spanRow", $v);
            }
            //TOWER
            $towerRow = $spanRow + 1;
            $tower = $towers[$i];
            foreach ($tower[$this->tower_num] as $k => $v) {
                $objSheet->setCellValue($this->template2_tower_num_column . $towerRow, $v);
            }
            foreach ($tower[$this->tower_type] as $k => $v) {
                $objSheet->setCellValue($this->template2_tower_type_column . $towerRow, $v);
            }
            foreach ($tower[$this->tower_type2] as $k => $v) {
                $objSheet->setCellValue($this->template2_tower_ext_column . $towerRow, $v);
            }

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
                foreach ($span[$this->act_span] as $k => $v) {
                    $objSheet->setCellValue($this->template2_act_span_column . $spanRow, $v);
                }
                foreach ($span[$this->crossing_rem] as $k => $v) {
                    $objSheet->setCellValue($this->template2_crossing_rem_column . $spanRow, $v);
                }
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
            foreach ($span[$this->act_span] as $k => $v) {
                $objSheet->setCellValue("$this->template3_act_span_column$spanRow", $v);
            }
            //TOWER
            $towerRow = $spanRow + 1;
            $tower = $towers[$i];
            foreach ($tower[$this->tower_num] as $k => $v) {
                $objSheet->setCellValue($this->template3_tower_num_column . $towerRow, $v);
            }
            foreach ($tower[$this->tower_type] as $k => $v) {
                $objSheet->setCellValue($this->template3_tower_type_column . $towerRow, $v);
            }
            foreach ($tower[$this->tower_type2] as $k => $v) {
                $objSheet->setCellValue($this->template3_tower_ext_column . $towerRow, $v);
            }
            foreach ($tower[$this->weight_span] as $k => $v) {
                $objSheet->setCellValue($this->template3_weight_span_column . $towerRow, $v);
            }

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
                foreach ($span[$this->act_span] as $k => $v) {
                    $objSheet->setCellValue($this->template3_act_span_column . $spanRow, $v);
                }
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
