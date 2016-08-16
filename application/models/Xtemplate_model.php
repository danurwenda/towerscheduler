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
    private $input_span_start = 17;
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
        $fasa = $objWorksheet->getCell('F7')->getValue();
        $tarikan = $objWorksheet->getCell('F8')->getValue();
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
            'fasa' => $fasa,
            'sc' => $sc,
            'w' => $w,
            'tarikan' => $tarikan,
            'spans' => $spans,
            'towers' => $towers
        ];
        return $ret;
    }

    /**
     * Template 1 : Tower Schedule
     */
    //TEMPLATE 1 : TOWER SCHEDULING
    private $template1_span_start = 13;
    private $template1_tower_ord_column = 'B';
    private $template1_tower_num_column = 'C';
    private $template1_tower_type_column = 'D';
    private $template1_tower_type2_column = 'E';
    private $template1_act_span_column = 'F';
    private $template1_cum_span_column = 'G';
    private $template1_tension_span_column = 'H';
    private $template1_equiv_span_column = 'I';
    private $template1_weight_span_column = 'J';
    private $template1_wind_span_column = 'K';
    private $template1_wtwd_ratio_column = 'L';
    private $template1_crossing_rem_column = 'M';
    private $span_columns = ['F', 'G', 'H', 'I', 'M'];
    private $tower_columns = ['B', 'C', 'D', 'E', 'J', 'K', 'L', 'N', 'O', 'P', 'Q'];

    public function generate_tower_schedule($data) {
        $inputFileName = $data['file'];
        //baca file input
        $data = $this->readInput($inputFileName);
        //write data to output
        $templateFileName = 'templates/output/template1.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templateFileName);
        //get first sheet
        $objSheet = $objPHPExcel->getSheet(0);
        $spans = $data['spans'];
        $towers = $data['towers'];
        $spanRow = $this->template1_span_start;
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
                $objSheet->setCellValue($this->template1_tower_type2_column . $towerRow, $v);
            }
            foreach ($tower[$this->weight_span] as $k => $v) {
                $objSheet->setCellValue($this->template1_weight_span_column . $towerRow, $v);
            }

            //advancing row (insert new if necessary), see condition below
            $spanRow+=2;
            //checking last tower 
            if ($i < (count($towers) - 1)) {
                //insert new space for next span-tower pair
                $objSheet->insertNewRowBefore($spanRow, 2);
                //setelah diinsert ini, kolom2 tower jadi 4row heigh
                //harus dipecah (unmerge), lalu dibagi jadi 2 buah merge cell
                //masing2 2 row heigh
                //sedangkan kolom2 span harus di merge jadi 2 row heigh
                $this->unmerge4merge2($objSheet, $spanRow - 1, $this->tower_columns);
                $this->merge2($objSheet, $spanRow, $this->span_columns);
                //fill formulas
                //tower order
                $objSheet->setCellValue($this->template1_tower_ord_column . ($spanRow + 1), $i + 2);
                //cumulative span
                $objSheet->setCellValue($this->template1_cum_span_column . $spanRow, '=' . $this->template1_cum_span_column . ($spanRow - 2) . '+' . $this->template1_act_span_column . $spanRow);
                //span tension
                $objSheet->setCellValue($this->template1_tension_span_column . $spanRow, '=' . $this->template1_act_span_column . $spanRow);
                //equiv tension
                $objSheet->setCellValue($this->template1_equiv_span_column . $spanRow, '=' . $this->template1_act_span_column . $spanRow);
                //wind span
                $objSheet->setCellValue($this->template1_wind_span_column . $towerRow, '=(' . $this->template1_act_span_column . ($spanRow - 2) . '+' . $this->template1_act_span_column . $spanRow . ')/2');
                //weight/wind ratio
                $objSheet->setCellValue($this->template1_wtwd_ratio_column . $towerRow, '=' . $this->template1_weight_span_column . $towerRow . '/' . $this->template1_wind_span_column . $towerRow);
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
                //cumulative span
                $objSheet->setCellValue($this->template1_cum_span_column . $spanRow, '=' . $this->template1_cum_span_column . ($spanRow - 2) . '+' . $this->template1_act_span_column . $spanRow);
                //wind span
                $objSheet->setCellValue($this->template1_wind_span_column . $towerRow, '=(' . $this->template1_act_span_column . ($spanRow - 2) . '+' . $this->template1_act_span_column . $spanRow . ')/2');
                //weight/wind ratio
                $objSheet->setCellValue($this->template1_wtwd_ratio_column . $towerRow, '=' . $this->template1_weight_span_column . $towerRow . '/' . $this->template1_wind_span_column . $towerRow);
            }
        }

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
    public function generate_material_schedule($data) {
        $inputFileName = $data['file'];
        //baca file input
        $data = $this->readInput($inputFileName);
        //write data to output
        $templateFileName = 'templates/output/template1.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($templateFileName);
        //get first sheet
        $objSheet = $objPHPExcel->getSheet(0);
        $spans = $data['spans'];
        $towers = $data['towers'];
        $spanRow = $this->template1_span_start;
    }

    //helper function
    private function merge2(PHPExcel_Worksheet $sheet, $rownum, $cols) {
        foreach ($cols as $col) {
            $rownum1 = $rownum + 1;
            $sheet->mergeCells("$col$rownum:$col$rownum1");
            //kasih border bawah
            $sheet->getStyle($col.$rownum1)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        }
    }

    private function unmerge4merge2(PHPExcel_Worksheet $sheet, $toprow, $cols) {
        foreach ($cols as $col) {
            $botrow = $toprow + 3;
            $sheet->unmergeCells("$col$toprow:$col$botrow");
        }
        $midrow = $toprow + 2;
        $this->merge2($sheet, $toprow, $cols);
        $this->merge2($sheet, $midrow, $cols);
    }

}
