<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mail;
use App\Models\MailInGroup;
use App\Models\GroupMail;
use App\Models\GroupMailRelation;

use Shuchkin\SimpleXLSX;
use Session;

class MailController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }
    
    // public $to_return = array(); 

    public function index(Request $request)
    {
        Session::put('txt_folder_search', ''); 
        Session::put('txt_search', ''); 
        Session::put('set_col', ''); 
        Session::put('txt_mail', '');
        Session::put('txt_folder', '');
        if(!empty($request->page_main)){
            Session::put('txt_mail_search', $request->txt_search); 
        }
        $txt_search ='';
        $mail = array();
        $mail = new Mail;
        $mail = $mail->where('status', 1);
        // if(!empty($request->txt_search)){
        // dd(Session::get('txt_mail_search')); 
        if(!empty(Session::get('txt_mail_search'))){
            $txt_search = Session::get('txt_mail_search');       
            // dd($txt_search);      
            $mail = $mail->where(function($query) use ($txt_search) {
                        $query->where('name', 'like', '%'.$txt_search.'%')
                        ->orWhere('pre_name', 'like', '%'.$txt_search.'%')
                        ->orWhere('email', 'like', '%'.$txt_search.'%');
                    });
        }            
        $mail = $mail->orderBy('status','DESC')->orderBy('name')->get();

        // $query = array();
        // $mig = array();
        // $query = MailInGroup::where('status', 1)->selectRaw('mail_id, COUNT(group_mail_id) AS group_mail_id')
        //     ->groupBy('mail_id')->get();
        // foreach ($query as $key) {
        //     $mig[$key->mail_id] = $key->group_mail_id;
        // }

        return view('mail.index',compact('mail','txt_search'));
    }

    public function create()
    {
        return view('mail.create');
    }

    public function store(Request $request)
    {
        // dd($request);
        // $validatedData = $request->validate([
        //     'pre_name' => 'nullable|sometimes|unique:mails|max:255',
        //     'email' => 'required|unique:mails,email|max:255',
        // ]);

        $requestData = $request->all();
        $query = array();
        if(!empty($requestData['pre_name'])){
            $query[0] = Mail::where('pre_name', $requestData['pre_name'])->where('status', 1)->count();
            if($query[0]>0){
                return redirect()->back()->with('error', 'LACO name ซ้ำกับที่มีอยู่!');
            }
        }    
        $query[1] = Mail::where('email', $requestData['email'])->where('status', 1)->count();
        // if($query[0]>0){
        //     return redirect()->back()->with('error', 'LACO name ซ้ำกับที่มีอยู่!');
        // }else
        if($query[1]>0){
            return redirect()->back()->with('error', 'E-mail ซ้ำกับที่มีอยู่!');
        }else{
            $to_save['name'] = $requestData['name'];
            if(!empty($requestData['pre_name']))    $to_save['pre_name'] = strtoupper($requestData['pre_name']);
            $to_save['email'] = $requestData['email'];
            Mail::create($to_save);
            return redirect()->route('mail.index')->with('success', ' added!');
        }

        // $to_save['name'] = $requestData['name'];
        // if(!empty($requestData['pre_name']))    $to_save['pre_name'] = strtoupper($requestData['pre_name']);
        // $to_save['email'] = $requestData['email'];
        // Mail::create($to_save);

        // return redirect()->route('mail.index')->with('success', ' added!');
    }

    public function edit($id)
    {
        $mail = Mail::findOrFail($id);
        return view('mail.edit', compact('mail'));
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->all(); 
        // dd($requestData);
        $chk = array();
        if(!empty($requestData['pre_name'])){
            $chk[0] = Mail::where('pre_name', $requestData['pre_name'])->where('status', 1)->whereNotIn('id', [$id])->get();
            if(count($chk[0])>0)   return redirect()->route('mail.edit',$id)->with('error', 'มี LACO Name : '.strtoupper($requestData['pre_name']).' นี้แล้ว!'); 
        }    
        $chk[1] = Mail::where('email', $requestData['email'])->where('status', 1)->whereNotIn('id', [$id])->get();
        // dd($chk[0]);
        if(count($chk[1])>0){
            // if(count($chk[0])>0)   return redirect()->route('mail.edit',$id)->with('error', 'มี LACO Name : '.strtoupper($requestData['pre_name']).' นี้แล้ว!'); 
            if(count($chk[1])>0)   return redirect()->route('mail.edit',$id)->with('error', 'มี E-mail : '.$requestData['email'].' นี้แล้ว!');
        }else{

            $mail = Mail::findOrFail($id);     
            $to_save['name'] = $requestData['name'];
            if(!empty($requestData['pre_name']))    $to_save['pre_name'] = strtoupper($requestData['pre_name']);
            else    $to_save['pre_name'] = null;
            $to_save['email'] = $requestData['email'];
            $mail->update($to_save); 
            return redirect()->route('mail.index')->with('success', ' updated!');
        }
 
    }

    public function destroy($id)
    {
        $set_status['status'] = '0';

        MailInGroup::where('status', 1)->where('mail_id', $id)->update($set_status);

        $mail = Mail::findOrFail($id);
        $mail->update($set_status);
        return redirect()->route('mail.index')->with('success', ' deleted!');
    }

    public function importExportView()
    {
        return view('mail.import');
    }
   
    public function import(Request $request) 
    {
        $validatedData = $request->validate([
            'file_upload'  => 'required|mimes:xls,xlsx'
        ]);

        $requestData = $request->all();
        $completedirectory = 'storage/app/public/upload/mail/';  

        if ($request->hasFile('file_upload')) {
            // $tmpfolder = md5(time());
            $tmpfolder = date('Ymd');
            if (!is_dir($completedirectory . '/' . $tmpfolder)) {
                mkdir($completedirectory . '/' . $tmpfolder, 0777, true);
            }  

            $zipfile = $request->file('file_upload');
            $uploadname = $zipfile->getClientOriginalName();
            $name = md5($zipfile->getClientOriginalName() . time()) . '-upload.' . $zipfile->getClientOriginalExtension();
            $destinationPath = public_path($completedirectory . $tmpfolder);
            $zipfile->move($destinationPath, $name);

            $uploadpath = $completedirectory . "/" . $tmpfolder  . "/" . $name;
            $uploadfile = $destinationPath  . "/" . $name;              
            
            if ($xlsx = SimpleXLSX::parse($uploadfile)) {  
                if(count($xlsx->rows()[0])==4){ 
                    $mail_data = array();
                    $query = Mail::where('status',1)->get();
                    foreach($query as $key){
                        $mail_data['email'][$key->email] = $key->id;
                        if(!empty($key->pre_name))  $mail_data['pre_name'][$key->pre_name] = $key->id;
                    }                
                    // dd($mail_data);                 
                    foreach ($xlsx->rows(0) as $r => $row) {                                                              
                        if ($r > 0 && !empty(trim($row[1]))) {   
                            // dd($row[0]);
                            $save_mail = array();
                            $save_mail['name'] = $row[1]; 
                            $save_mail['pre_name'] = $row[2];                            
                            $save_mail['email'] = $row[3];
                            // dd($save_mail);
                            if(empty($mail_data['pre_name'][$row[2]]) && empty($mail_data['email'][$row[3]])){
                                Mail::create($save_mail);
                            }else{
                                $query = new Mail;
                                if(!empty($mail_data['pre_name'][$row[2]]))    $$query = $query->where('pre_name', $row[2]);
                                if(!empty($mail_data['email'][$row[3]]))   $query = $query->where('email', $row[3]);
                                $query = $query->update($save_mail);
                            }
                        }
                    }                    
                    return back()->with('success','Updated successfully');  
                }else{
                    return back()->with('error','คอลัมน์เกินกว่าที่กำหนด');
                }
            } else {
                echo SimpleXLSX::parseError();
            }   
        }else{
            return back()->with('error','No file');
        } 
        
    }

    public function import_relation(Request $request) 
    {
        $validatedData = $request->validate([
            'file_upload'  => 'required|mimes:xls,xlsx'
        ]);

        $requestData = $request->all();
        $completedirectory = 'storage/app/public/upload/mail/';  

        if ($request->hasFile('file_upload')) {
            // $tmpfolder = md5(time());
            $tmpfolder = date('Ymd');
            if (!is_dir($completedirectory . '/' . $tmpfolder)) {
                mkdir($completedirectory . '/' . $tmpfolder, 0777, true);
            }  

            $zipfile = $request->file('file_upload');
            $uploadname = $zipfile->getClientOriginalName();
            $name = md5($zipfile->getClientOriginalName() . time()) . '-upload.' . $zipfile->getClientOriginalExtension();
            $destinationPath = public_path($completedirectory . $tmpfolder);
            $zipfile->move($destinationPath, $name);

            $uploadpath = $completedirectory . "/" . $tmpfolder  . "/" . $name;
            $uploadfile = $destinationPath  . "/" . $name;              
            
            if ($xlsx = SimpleXLSX::parse($uploadfile)) {  
                $query = Mail::where('status',1)->get();
                foreach($query as $key){
                    $mail_data[$key->email] = $key->id;
                }

                $query = GroupMail::where('status',1)->get();
                foreach($query as $key){
                    $gm_data[$key->name] = $key->id;
                }

                $query = MailInGroup::where('status',1)->get();
                foreach($query as $key){
                    $mig_data[$key->mail_id][$key->group_mail_id] = $key->id;
                }
                // dd($xlsx->rows(0));  
                $head_gm_id = array();
                $use_gm = array(); 
                foreach ($xlsx->rows(0) as $r => $row) {                   
                    // echo 'r => '.$r.'</br>';
                    if($r==0){
                        $i = 1;
                        while (!empty($row[$i])) {
                            if(empty($gm_data[$row[$i]])){
                                $save_gm['name'] = $row[$i];  
                                $id_gm = GroupMail::create($save_gm)->id;
                                $gm_data[$row[$i]] = $id_gm;
                            }
                            $head_gm_id[$i] = $gm_data[$row[$i]];
                            $i++;
                        }  
                        // dd($head_gm_id);    //81  
                    } 
                    // dd($head_gm_id);                                           
                    if ($r > 0 && !empty(trim($row[0]))) {   
                        // dd($row[0]);
                        if(empty($mail_data[$row[0]])){
                            $exp_mail = explode('@',$row[0]);
                            $save_mail['name'] = $exp_mail[0];                            
                            $save_mail['email'] = $row[0];
                            $id_mail = Mail::create($save_mail)->id;
                            $mail_data[$row[0]] = $id_mail;
                        }
                        // $to_save['mail_id'] = $mail_data[$row[0]];

                        foreach ($head_gm_id as $key => $value) {
                            // if($key==1) echo $key.'->'.$row[$key].'</br>';
                            if(!empty($row[$key]))  $use_gm[$mail_data[$row[0]]][$value] = $row[$key];
                        }
                    }
                }
                // echo 'use_gm</br>';
                // dd($use_gm);

                foreach ($use_gm as $kmail => $vmail) {
                    foreach ($vmail as $kgm => $vgm) {
                        if(empty($mig_data[$kmail][$kgm])){
                            $to_save = array();
                            $to_save['mail_id'] = $kmail;
                            $to_save['group_mail_id'] = $kgm;
                            // $count_row = MailInGroup::where('status',1)->where('mail_id',$kmail)->count('group_mail_id',$kgm)->count();
                            // if($count_row==0)   
                            MailInGroup::create($to_save);
                        }
                    }
                }
                return back()->with('success','Updated successfully');  
            } else {
                echo SimpleXLSX::parseError();
            }   
        }else{
            return back()->with('error','No file');
        } 
        
    }

    public function display($a, $key1, $value1, $gmr_all) { 
        global $newArray; 
        global $last_loop;
        global $num;
        global $chk;
        if(empty($last_loop))   $last_loop=0;
        if(is_null($num) || $chk<>$key1){
            // echo 'chang num</br>';  //เข้าครั้งเดียว
            $num=0;
        }  
        if(is_null($chk)){
            // echo 'chang chk</br>';  //เข้าครั้งเดียว
            $chk=$key1;
        } 
        // echo 'chk : '.$chk.'->key1 : '.$key1.'->value1 : '.$value1.'</br>';
        // if($chk<>$key1){
        //     echo '---------chk : '.$chk.'<> key1 : '.$key1.'----------------</br>';
        //     $num=0;
        // } 
        // echo 'a ->'.$a.'</br>';     //นับใส่ array ตัวที่ 3 ได้        
        if($a==1 && $num==0){
            $last_loop=0;
            // echo '0->'.$value1.'</br>';     //id ลำดับแรกแต่ละสาย 
            $newArray[$key1][$num][0] = $value1;
        }
        if(!empty($gmr_all['up'][$value1])){
            // echo 'start foreach</br>'; 
            foreach ($gmr_all['up'][$value1] as $kdet => $vdet) { 
                // echo 'next foreach</br>';
                if($a==$last_loop+1){
                    // echo 'key1='.$key1.', num='.$num.', a='.$a.', last_loop='.$last_loop.'+++</br>';
                    $newArray[$key1][$num][$a] = $kdet;    
                    // print_r($newArray); 
                    // echo '----newArray</br>'; 
                }else{
                    $num++; 
                    for($i=0;$i<=$a;$i++){
                        // echo 'key1='.$key1.', num='.$num.', i='.$i.', a='.$a.'+++</br>';
                        if($i <> $a){
                            // echo $a.'+++</br>';
                            $newArray[$key1][$num][$i] = $newArray[$key1][$num-1][$i];                             
                            // $num++;
                            // print_r($newArray);
                            // echo '----newArray</br>'; 
                        }else{
                            // $num++; 
                            // echo $a.'***</br>';
                            $newArray[$key1][$num][$a] = $kdet; 
                            // print_r($newArray);
                            // echo '----newArray</br>'; 
                        }                            
                    }
                    // print_r($newArray);
                    // echo '----newArray</br>'; 
                }
                if($chk<>$key1)     $chk=$key1;
                $last_loop = $a;
                $this->display($a+1, $key1, $kdet, $gmr_all);     
            }
        }
        return $newArray; 
    }

    public function to_group($id)
    {
        $mail = Mail::findOrFail($id);
        // dd($mail->email);

        $query = array(); 
        $gm_all = array(); 
        $query = GroupMail::all();
        foreach ($query as $key) {
            $gm_all[$key->id] = $key->name;
        }

        $gmr_all = array(); 
        $query = GroupMailRelation::where('status',1)->get();
        foreach ($query as $key) {
            $gmr_all['up'][$key->group_mail_detail][$key->group_mail_main] = $key->id;
            $gmr_all['down'][$key->group_mail_main][$key->group_mail_detail] = $key->id;
        }
        // dd($gmr_all['up'][5]);
        //เป็นการหาย้อนขึ้นไป
        $group_mail = array();  
        $group_mail = MailInGroup::where('mail_id', $id)->where('status', 1)->get();
        // dd($group_mail);    //8, 2
        $loop = 0;
        $to_down = array();
        $to_show = array();
        $row_id = array();        
        $not_in = array();  
        // $to_return = array();
        foreach ($group_mail as $key) {
            // echo $key->group_mail_id.'</br>'; 
            // $set_group[$loop][][] = $key->group_mail_id;
            $to_show[$loop] = $key->group_mail_id;  //8, 2
            $row_id[$loop] = $key->id;  //1, 767
            $not_in[] = $key->group_mail_id;    //8, 2
            // $chk_det = $key->group_mail_id;

            // if(empty($chk_duplicate[$chk_det]))  $chk_duplicate[$chk_det] = 1;
            // else    $chk_duplicate[$chk_det] += 1; 

            $loop++;
        }
        // dd($to_show);
        $set_group = array();
        $to_recursive = array();
        foreach ($to_show as $key1 => $value1) { 
            // $set_group[$key1][0][0] = $value1;
            $to_recursive = $this->display(1, $key1, $value1, $gmr_all);
            // if(!empty($gmr_all['up'][$value1])){ 
            //     $a = 1; $b = 2; $c = 3; $d = 4;
            //     $num = 0; $num1 = 0; $num2 = 0; $num3 = 0;
            //     $last_loop = 0;
            //     // $num_1 = 0; $num_2 = 0; $num_3 = 0; $num_4 = 0;
            //     foreach ($gmr_all['up'][$value1] as $kdet => $vdet) { 
            //         // echo '1. a : '.$a.'--kdet : '.$kdet.'--num : '.$num.'</br>';
            //         // if($num==0){
            //         if($a==$last_loop+1 || $last_loop==1){
            //             // echo 'set_group['.$key1.']['.$num.']['.$a.'] : '.$kdet.'</br>';
            //             $set_group[$key1][$num][$a] = $kdet;                                
            //         }else{
            //             $num++;
            //             for($i=0;$i<=$a;$i++){                            
            //                 if($i <> $a){
            //                     // echo 'set_group['.$key1.']['.$num.']['.$i.'] : '.$set_group[$key1][($num-1)][$i].'</br>';
            //                     $set_group[$key1][$num][$i] = $set_group[$key1][$num-1][$i];  
            //                 }else{
            //                     // echo 'set_group['.$key1.']['.$num.']['.$a.'] : '.$kdet.'</br>';
            //                     $set_group[$key1][$num][$a] = $kdet; 
            //                 }    
            //             }
            //         }
            //         $last_loop = $a;
            //         // $to_recursive = $this->display($key1, $num, $a+1, $kdet, $gmr_all);
            //         if(!empty($gmr_all['up'][$kdet])){ 
            //             // $b = 2;
            //             // $num_1 = 0;
            //             foreach ($gmr_all['up'][$kdet] as $kdet1 => $vdet1) { 
            //                 // echo '2. b : '.$b.'--kdet1 : '.$kdet1.'--num : '.$num.'</br>';
            //                 // if($num1==0){
            //                 if($b==$last_loop+1){
            //                     // echo 'set_group['.$key1.']['.$num.']['.$b.'] : '.$kdet1.'</br>';
            //                     $set_group[$key1][$num][$b] = $kdet1;                                
            //                 }else{
            //                     $num++;
            //                     for($i=0;$i<=$b;$i++){                            
            //                         if($i <> $b){
            //                             // echo 'set_group['.$key1.']['.$num.']['.$i.'] : '.$set_group[$key1][($num-1)][$i].'</br>';
            //                             $set_group[$key1][$num][$i] = $set_group[$key1][$num-1][$i];  
            //                         }else{
            //                             // echo 'set_group['.$key1.']['.$num.']['.$b.'] : '.$kdet1.'</br>';
            //                             $set_group[$key1][$num][$b] = $kdet1; 
            //                         }    
            //                     }
            //                     // $num = 0; $num1 = 0; $num2 = 0; $num3 = 0;
            //                 } 
            //                 $last_loop = $b;
            //                 if(!empty($gmr_all['up'][$kdet1])){ 
            //                     // $c = 3;
            //                     // $num2 = 0;
            //                     foreach ($gmr_all['up'][$kdet1] as $kdet2 => $vdet2) { 
            //                         // echo '3. c : '.$c.'--kdet2 : '.$kdet2.'--num : '.$num.'</br>';
            //                         // if($num2==0){
            //                         if($c==$last_loop+1){
            //                             // echo 'set_group['.$key1.']['.$num.']['.$c.'] : '.$kdet2.'</br>';
            //                             $set_group[$key1][$num][$c] = $kdet2;                                
            //                         }else{
            //                             $num++;
            //                             for($i=0;$i<=$c;$i++){                            
            //                                 if($i <> $c){
            //                                     // echo 'set_group['.$key1.']['.$num.']['.$i.'] : '.$set_group[$key1][($num-1)][$i].'</br>';
            //                                     $set_group[$key1][$num][$i] = $set_group[$key1][$num-1][$i];  
            //                                 }else{
            //                                     // echo 'set_group['.$key1.']['.$num.']['.$c.'] : '.$kdet2.'</br>';
            //                                     $set_group[$key1][$num][$c] = $kdet2; 
            //                                 }    
            //                             }
            //                             // $num = 0; $num1 = 0; $num2 = 0; $num3 = 0;
            //                         } 
            //                         $last_loop = $c; 
            //                         if(!empty($gmr_all['up'][$kdet2])){ 
            //                             // $d = 4;
            //                             // $num3 = 0;
            //                             foreach ($gmr_all['up'][$kdet2] as $kdet3 => $vdet3) { 
            //                                 // echo '4. d : '.$d.'--kdet3 : '.$kdet3.'--num : '.$num.'</br>';
            //                                 // if($num3==0){
            //                                 if($d==$last_loop+1){
            //                                     // echo 'set_group['.$key1.']['.$num.']['.$d.'] : '.$kdet3.'</br>';
            //                                     $set_group[$key1][$num][$d] = $kdet3;                                
            //                                 }else{
            //                                     $num++;
            //                                     for($i=0;$i<=$d;$i++){                            
            //                                         if($i <> $d){
            //                                             // echo 'set_group['.$key1.']['.$num.']['.$i.'] : '.$set_group[$key1][($num-1)][$i].'</br>';
            //                                             $set_group[$key1][$num][$i] = $set_group[$key1][$num-1][$i];  
            //                                         }else{
            //                                             // echo 'set_group['.$key1.']['.$num.']['.$d.'] : '.$kdet3.'</br>';
            //                                             $set_group[$key1][$num][$d] = $kdet3; 
            //                                         }    
            //                                     }
            //                                     // $num = 0; $num1 = 0; $num2 = 0; $num3 = 0;
            //                                 }
            //                                 $last_loop = $d;
            //                                 // $num3++;
            //                             }
            //                         } 
            //                         // $num2++;
            //                     }
            //                 }
            //                 // $num1++;
            //             }
            //         }              
            //         // $num++;  
            //     }
            // }
        }
        // dd($to_recursive);
        foreach ($to_recursive as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    $set_group[$key][$key1][$key2] = $value2;
                }
            }
        }
            
        $chk_duplicate = array();
        foreach ($set_group as $key => $value) {
            foreach ($value as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    if(empty($chk_duplicate[$value2]))  $chk_duplicate[$value2] = 1;
                    else    $chk_duplicate[$value2] += 1; 
                }
            }
        }
        
        // dd($set_group);
        
        $max_col = 0;
        foreach ($set_group as $key=>$value) {
            foreach ($value as $key1 => $value1) {
                if($max_col < count($set_group[$key][$key1]))    $max_col = count($set_group[$key][$key1]);
            }
        }
        // dd($max_col);

        //เพิ่มกลุ่ม ต้องไม่มีกลุ่ม+หัว+หาง ของกลุ่มที่มีแล้ว
        $group_add = GroupMail::whereNotIn('id', $not_in)->where('status', 1)->orderBy('name')->get();
        
        return view('mail.group', compact('mail','id','gm_all','set_group','max_col','chk_duplicate','group_add','gmr_all','row_id'));
    }

    public function group(Request $request, $id)
    {
        //กลับมาทำลบอันเก่าก่อนด้วย

        // $query = array();
        // $to_where = 0;
        // $i=0;
        // $set_status['status'] = '0';
        // $query = GroupMailRelation::where('group_mail_main', $id)->get();
        // if(count($query)>0){
        //     foreach ($query as $key) {
        //         $old_data[$key->group_mail_detail] = $key->id;

        //         $gm = GroupMailRelation::findOrFail($key->id);
        //         $gm->update($set_status);
        //     }
        // }
        $requestData = $request->all();
        // $gm_main = '';
        foreach($requestData['to_detail'] as $key=>$value){
            if(!empty($value)){
                $add_mig['mail_id'] = $id;
                $add_mig['group_mail_id'] = $value;
                MailInGroup::create($add_mig);
            }
        } 



        // do {
        //     if($i==0)    $to_where = $id;
        //     else        $to_where = $old_data[($i-1)]['detail'];
        //     $query = GroupMailRelation::where('group_mail_main', $to_where)->get();
        //     if(count($query)>0){
        //         foreach ($query as $key) {
        //             $old_data[$i]['id'] = $key->id;
        //             $old_data[$i]['main'] = $key->group_mail_main;
        //             $old_data[$i]['detail'] = $key->group_mail_detail;
                    
        //             $gm = GroupMailRelation::findOrFail($key->id);
        //             $gm->update($set_status);
        //         }
        //     }
        //     $i++;
        //     // echo 'count = '.count($query).'</br>';
        //     // if ($i == 4) {
        //     //     break;
        //     // }
        // }while (count($query)>0);
        // // dd($old_data);

        // $requestData = $request->all();
        // // dd($requestData['to_detail']);
        // $gm_main = '';
        // foreach($requestData['to_detail'] as $key=>$value){
        //     if(!empty($value)){
        //         if(empty($gm_main)){
        //             $add_gm_relation['group_mail_main'] = $id;
        //         }else{
        //             $add_gm_relation['group_mail_main'] = $gm_main;
        //         }
        //         $gm_main = $value; 
        //         $add_gm_relation['group_mail_detail'] = $value;
        //         GroupMailRelation::create($add_gm_relation);
        //     }
        // } 
        return redirect()->route('mail.index')->with('success', 'Add Group!');
    }

    public function group_del($mail_del)
    {
        $set_status['status'] = '0';
        $mig = MailInGroup::findOrFail($mail_del);
        $mig->update($set_status);

        return back()->with('success','Delete successfully');  
    }
}
