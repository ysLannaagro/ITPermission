<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupMail;
use App\Models\GroupMailRelation;
use App\Models\MailInGroup;
use App\Models\Mail;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{   
    public function __construct()
    {
        $this->middleware('isAdmin');
    }
      
    public function index()
    {
        return view('report.index');
    }

    public function report(Request $request)
    {
        // dd($request->report_id);
        $query = array();  
        $gm = array();   
        $m = array();  

        $query = GroupMail::where('status',1)->get();
        foreach ($query as $key) {
            $gm[$key->id] = $key->name;
        }

        $query = Mail::where('status',1)->get();
        foreach ($query as $key) {
            $m[$key->id]['name'] = $key->name;
            $m[$key->id]['pre_name'] = $key->pre_name;
            $m[$key->id]['email'] = $key->email;
        }

        if(count($gm)>0){ 
            if($request->report_id==1){              
                $gmr = array(); 
                $have_group = array(); 
                $query = GroupMailRelation::where('status',1)->get();
                foreach ($query as $key) {
                    $gmr[$key->group_mail_main][] = $key->group_mail_detail;    //มีตัวลูกกี่ตัว
                    $have_group[$key->group_mail_main] = $key->group_mail_main; //เก็บเป็นตัวที่มีกลุ่มแล้ว
                    $have_group[$key->group_mail_detail] = $key->group_mail_detail; //เก็บเป็นตัวที่มีกลุ่มแล้ว
                    $gmr_all['up'][$key->group_mail_detail][$key->group_mail_main] = $key->id;
                    $gmr_all['down'][$key->group_mail_main][$key->group_mail_detail] = $key->id;                
                }

                $gm_nogroup = array(); 
                $query = GroupMail::where('status',1)->whereNotIn('id', $have_group)->get();
                foreach ($query as $key) {
                    $gm_nogroup[$key->id] = $key->name;     //เก็บเป็นตัวที่ไม่มีกลุ่ม
                } 

                // dd($gmr_all);

                // //หาจำนวนลำดับขั้นของแต่ละกลุ่ม เอาค่าที่มากที่สุดมาเป็นจำนวนคอลัมน์
                // // //หาที่ถัดลงมา
                $max_col = 1;
                $to_down = array();
                global $to_return;   

                foreach ($gmr_all['down'] as $key=>$value) {
                    // echo $key.'->0->0->'.$key.'</br>';
                    $to_return[$key][0][0] = $key;
                    $b=0;
                    foreach ($value as $key1=>$value1) {
                        // echo $key.'->1->'.$b.'->'.$key1.'</br>';
                        $to_return[$key][1][$b] = $key1;
                        $b++;
                        // echo '1. '.$key.'->'.$b.'->'.$key1.'</br>';
                        $to_down = $this->display($gmr_all, $key, $key1, 2);
                    }
                }
                // dd($to_down);

                foreach ($to_down as $key=>$value) {
                    // echo $key.'->'.count($value).'</br>';
                    if($max_col < count($value))    $max_col = count($value);
                }
  
                $test = $this->report_1($gm, $gmr, $gm_nogroup, $to_down, $max_col);
                $rp_1 = $test[0];
                return response()->download($rp_1);
                
            }elseif($request->report_id==2){
                $query = MailInGroup::where('status',1)->get();
                foreach ($query as $key) {
                    $mig[$key->group_mail_id][] = $key->mail_id;
                }
                if(count($mig)<255){
                    foreach ($mig as $key=>$value) {
                        $len_title = strlen($gm[$key]);
                        if($len_title > 10) $title_sheet[$key] = substr($gm[$key],0,10);
                        else    $title_sheet[$key] = $gm[$key];
                    }
                    // dd($title_sheet);
                    $test = $this->report_2($gm, $m, $mig, $title_sheet);
                    $rp_1 = $test[0];
                    return response()->download($rp_1);
                }else{
                    return back()->with('error','มีจำนวนกลุ่มมากกว่า 255 กลุ่ม!');
                }               
                
            }
        }else{
            return back()->with('error','No data!');
        }    


    }

    public function display($gmr_all, $key, $key1, $loop) { 
        global $to_return;            
        if(!empty($gmr_all['down'][$key1])){                        
            $c=0;
            foreach ($gmr_all['down'][$key1] as $key2=>$value2) {
                // echo $key.'->'.$loop.'->'.$c.'->'.$key2.'</br>';
                $to_return[$key][$loop][$c] = $key2;
                $c++;
                $this->display($gmr_all, $key, $key2, $loop+1);
            }
        }
        return $to_return;  
    }

    public function report_1($gm, $gmr, $gm_nogroup, $to_down, $max_col)
    {
        // $to_excel = array();
        $all_col = $max_col+1;
        $column_end = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($all_col);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValueByColumnAndRow(1, 1, "ลำดับการจัดกลุ่มเมลล์");
        $sheet->mergeCellsByColumnAndRow(1, 1, $all_col, 1);

        $sheet->setCellValueByColumnAndRow(1, 2, "ที่");
        $sheet->mergeCellsByColumnAndRow(1, 2, 1, 3);
        $sheet->setCellValueByColumnAndRow(2, 2, "ลำดับ");
        $sheet->mergeCellsByColumnAndRow(2, 2, $all_col, 2);
        for($i=0; $i<$max_col; $i++){
            $sheet->setCellValueByColumnAndRow(($i+2), 3, ($i+1));
        }        
        $sheet->getStyle('A2:'.$column_end.'3')->getAlignment()->setHorizontal('center');         
        $sheet->getStyle('A2:'.$column_end.'3')->getAlignment()->setVertical('center');
        $sheet->getStyle('A2:'.$column_end.'3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');

        $startrow = 4;
        $i = 0;
        foreach ($to_down as $kmain=>$vmain) {
            $startcol = 1;
            $sheet->setCellValueByColumnAndRow($startcol, $startrow, ++$i);
            $startcol++;

            // $this->display_1($kmain, $startcol, $startrow, $gm);
            // $to_excel = $this->display_1($kmain, $startcol, $startrow, $gm);
            // $to_down = '$sheet->setCellValueByColumnAndRow(('.($startcol+$col+1).'), '.$startrow.', '.$gm[$vno1].')';
            // $sheet->$to_down;


            for($col=0; $col<$max_col; $col++){
                if(!empty($to_down[$kmain][$col])){
                    foreach ($to_down[$kmain][$col] as $kno=>$vno) { 
                        $sheet->setCellValueByColumnAndRow(($startcol+$col), $startrow, $gm[$vno]);
                        if(empty($gmr[$vno])){
                            $startrow++;
                        }else{
                            foreach ($gmr[$vno] as $kno1=>$vno1) {
                                $sheet->setCellValueByColumnAndRow(($startcol+$col+1), $startrow, $gm[$vno1]);
                                if(empty($gmr[$vno1])){
                                    $startrow++;
                                }else{
                                    foreach ($gmr[$vno1] as $kno2=>$vno2) {
                                        $sheet->setCellValueByColumnAndRow(($startcol+$col+2), $startrow, $gm[$vno2]);
                                        if(empty($gmr[$vno2])){
                                            $startrow++;
                                        }else{
                                            foreach ($gmr[$vno2] as $kno3=>$vno3) {
                                                $sheet->setCellValueByColumnAndRow(($startcol+$col+3), $startrow, $gm[$vno3]);
                                                if(empty($gmr[$vno3])){
                                                    $startrow++;
                                                }else{
                                                    foreach ($gmr[$vno3] as $kno4=>$vno4) {
                                                        $sheet->setCellValueByColumnAndRow(($startcol+$col+4), $startrow, $gm[$vno4]);
                                                        if(empty($gmr[$vno4])){
                                                            $startrow++;
                                                        }else{
                                                            foreach ($gmr[$vno4] as $kno5=>$vno5) {
                                                                $sheet->setCellValueByColumnAndRow(($startcol+$col+5), $startrow, $gm[$vno5]);
                                                                if(empty($gmr[$vno5v])){
                                                                    $startrow++;
                                                                }else{
                                                                    foreach ($gmr[$vno5] as $kno6=>$vno6) {
                                                                        $sheet->setCellValueByColumnAndRow(($startcol+$col+6), $startrow, $gm[$vno6]);
                                                                        if(empty($gmr[$vno6])){
                                                                            $startrow++;
                                                                        }else{
                                                                            foreach ($gmr[$vno6] as $kno7=>$vno7) {
                                                                                $sheet->setCellValueByColumnAndRow(($startcol+$col+7), $startrow, $gm[$vno7]);
                                                                                if(empty($gmr[$vno7])){
                                                                                    $startrow++;
                                                                                }else{
                                                                                    foreach ($gmr[$vno7] as $kno8=>$vno8) {
                                                                                        $sheet->setCellValueByColumnAndRow(($startcol+$col+8), $startrow, $gm[$vno8]);
                                                                                        if(empty($gmr[$vno8])){
                                                                                            $startrow++;
                                                                                        }else{
                                                                                            foreach ($gmr[$vno8] as $kno9=>$vno9) {
                                                                                                $sheet->setCellValueByColumnAndRow(($startcol+$col+9), $startrow, $gm[$vno9]);
                                                                                                if(empty($gmr[$vno9])){
                                                                                                    $startrow++;
                                                                                                }else{
                                                                                                    foreach ($gmr[$vno9] as $kno10=>$vno10) {
                                                                                                        $sheet->setCellValueByColumnAndRow(($startcol+$col+10), $startrow, $gm[$vno10]);
                                                                                                        $startrow++;
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }                        
                    }
                }
                break;
            }            
        }

        foreach ($gm_nogroup as $kmain=>$vmain) {
            $startcol = 1;
            $sheet->setCellValueByColumnAndRow($startcol, $startrow, ++$i);
            $startcol++;
            $sheet->setCellValueByColumnAndRow($startcol, $startrow, $vmain);
            $startrow++;
        }

        $sheet->getStyle('A2:'.$column_end.($startrow-1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        for($i=1; $i<=$all_col; $i++) {
            $columnID = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        // dd($sheet);

        $writer = new Xlsx($spreadsheet);
        $filename = "group_mail_diagram-" . date('yymmdd-hi') . ".xlsx";            
        $completedirectory = 'storage/app/public/group_mail/';
        $tmpfolder = date('Ymd');
        if (!is_dir($completedirectory . '/' . $tmpfolder)) {
            mkdir($completedirectory . '/' . $tmpfolder, 0777, true);
        }
        $writer->save($completedirectory . '/' . $tmpfolder . '/' . $filename);
        $path_file = $completedirectory . '/' . $tmpfolder . '/' . $filename;        
        return [$path_file];
    }

    // public function display_1($kmain, $startcol, $startrow, $gm) { 
    //     $sheet = $this->content;           
    //     if(empty($gmr[$kmain])){
    //         $startrow++;
    //     }else{
    //         foreach ($gmr[$kmain] as $kno1=>$vno1) {
    //             $sheet->setCellValueByColumnAndRow(($startcol+1), $startrow, $gm[$vno1]);
    //             $this->display_1($vno1, $startcol, $startrow, $gm);
    //         }
    //     }
    // }

    public function report_2($gm, $m, $mig, $title_sheet)
    {
        $spreadsheet = new Spreadsheet();
        $count_sheet = 0;         
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($mig as $key => $value) {   
            $sheet->setCellValueByColumnAndRow(1, 1, "รายชื่อเมลล์ในกลุ่ม ".$gm[$key]);
            $sheet->mergeCellsByColumnAndRow(1, 1, 4, 1);

            $sheet->setCellValueByColumnAndRow(1, 2, "No.");
            $sheet->setCellValueByColumnAndRow(2, 2, "ชื่อ");  
            $sheet->setCellValueByColumnAndRow(3, 2, "LACO Name");  
            $sheet->setCellValueByColumnAndRow(4, 2, "E-mail");  
            
            $sheet->getStyle('A1:D2')->getAlignment()->setHorizontal('center');         
            $sheet->getStyle('A1:D2')->getAlignment()->setVertical('center');
            $sheet->getStyle('A1:D2')->getFont()->setBold(true);
            $sheet->getStyle('A2:D2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9D9D9');

            $startrow = 3;
            $i = 1;
            foreach ($value as $kmail=>$vmail) {              
                $startcol = 1;
                $sheet->setCellValueByColumnAndRow($startcol, $startrow, $i++);
                $startcol++;
                $sheet->setCellValueByColumnAndRow($startcol, $startrow, $m[$vmail]['name'] );
                $startcol++;
                $sheet->setCellValueByColumnAndRow($startcol, $startrow, $m[$vmail]['pre_name'] );
                $startcol++;
                $sheet->setCellValueByColumnAndRow($startcol, $startrow, $m[$vmail]['email'] );

                $startrow++;               
            }
      
        
            $sheet->getStyle('A2:D'.($startrow-1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            for($i=1; $i<5; $i++) {
                $columnID = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }        
        
            $spreadsheet->getActiveSheet()->setTitle($title_sheet[$key]);

            if((count($mig)-1)>$count_sheet){
                $count_sheet++;
                $spreadsheet->createSheet();            
                $sheet = $spreadsheet->setActiveSheetIndex($count_sheet);
            }
        }        

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $filename = "mail_in_group-" . date('yymmdd-hi') . ".xlsx";            
        $completedirectory = 'storage/app/public/mail_in_group/';
        $tmpfolder = date('Ymd');
        if (!is_dir($completedirectory . '/' . $tmpfolder)) {
            mkdir($completedirectory . '/' . $tmpfolder, 0777, true);
        }
        $writer->save($completedirectory . '/' . $tmpfolder . '/' . $filename);
        $path_file = $completedirectory . '/' . $tmpfolder . '/' . $filename;
        return [$path_file];
    }
}
