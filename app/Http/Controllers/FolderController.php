<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use Shuchkin\SimpleXLSX;
use App\Models\GroupMail;
use App\Models\FolderInGroup;
use App\Models\GroupMailRelation;
use Session;

class FolderController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }
    
    public function index(Request $request)
    {
        Session::put('txt_mail_search', ''); 
        Session::put('txt_search', ''); 
        Session::put('set_col', ''); 
        Session::put('txt_mail', '');
        Session::put('txt_folder', '');
        if(!empty($request->page_main)){
            Session::put('txt_folder_search', $request->txt_search); 
        }
        $txt_search ='';
        $folder = array();
        $folder = new Folder;
        $folder = $folder->where('status', 1);
        // if(!empty($request->txt_search)){
        if(!empty(Session::get('txt_folder_search'))){
            $txt_search = Session::get('txt_folder_search');       
        //     // dd($txt_search);      
            $folder = $folder->where('name', 'like', '%'.$txt_search.'%');
        }            
        $folder = $folder->orderBy('name')->get();

        $query = array();
        $fig = array();
        // $query = MailInGroup::where('status', 1)->selectRaw('mail_id, COUNT(group_mail_id) AS group_mail_id')
        //     ->groupBy('mail_id')->get();
        // foreach ($query as $key) {
        //     $fig[$key->mail_id] = $key->group_mail_id;
        // }

        return view('folder.index',compact('folder','txt_search', 'fig'));
    }

    public function create()
    {
        return view('folder.create');
    }

    public function store(Request $request)
    {
        // dd($request);
        // $validatedData = $request->validate([
        //     'name' => 'unique:folders|max:255',
        // ]);

        $requestData = $request->all();
        $query = array();
        $query = Folder::where('name', $requestData['name'])->where('status', 1)->count();
        if($query>0){
            return redirect()->back()->with('error', 'ชื่อซ้ำกับที่มีอยู่!');
        }else{
            Folder::create($requestData);
            return redirect()->route('folder.index')->with('success', ' added!');
        }
    }

    public function edit($id)
    {
        $folder = Folder::findOrFail($id);
        return view('folder.edit', compact('folder'));
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->all(); 
        $query = array();
        $query = Folder::where('name', $requestData['name'])->where('status', 1)->whereNotIn('id', [$id])->count();
        if($query>0){
            return redirect()->back()->with('error', 'ชื่อซ้ำกับที่มีอยู่!');
        }else{
            $folder = Folder::findOrFail($id);
            $folder->update($requestData);  
            return redirect()->route('folder.index')->with('success', ' updated!');
        }
 
    }

    public function destroy($id)
    {
        $set_status['status'] = '0';

        $folder = Folder::findOrFail($id);
        $folder->update($set_status);
        return redirect()->route('folder.index')->with('success', ' deleted!');
    }

    public function importExportView()
    {
        return view('folder.import');
    }
   
    public function import(Request $request) 
    {
        $requestData = $request->all();
        $completedirectory = 'storage/app/public/upload/folder/';  

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
                if(count($xlsx->rows()[0])==2){ 
                    $query = array();
                    $query = Folder::where('status', 1)->get();
                    foreach ($query as $key) {
                        $folder[$key->name] = $key->id;
                    } 
                    // dd($gm);
                    foreach ($xlsx->rows() as $r => $row) {                                                                      
                        if ($r > 0 && !empty(trim($row[1]))) {  
                            if(empty($folder[trim($row[1])])){
                                $add_fd = array();
                                $add_fd['name'] = trim($row[1]);
                                $fd_id = Folder::create($add_fd)->id;
                                $folder[trim($row[0])] = $fd_id;
                            }                                      
                        }
                    }
                    return back()->with('success','Import successfully');  
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
        $requestData = $request->all();
        $completedirectory = 'storage/app/public/upload/folder/';  

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
                $query = array();
                $query = Folder::where('status', 1)->get();
                foreach ($query as $key) {
                    $folder[$key->name] = $key->id;
                } 
                
                $query = GroupMail::where('status', 1)->get();
                foreach ($query as $key) {
                    $gm[$key->name] = $key->id;
                } 

                $query = FolderInGroup::where('status', 1)->get();
                foreach ($query as $key) {
                    $fig_data[$key->folder_id][$key->group_mail_id] = $key->id;
                } 
                // dd($gm);
                foreach ($xlsx->rows() as $r => $row) { 
                    // echo $r.'</br>';
                    // dd('empty-');
                    if($r==1){
                        $i = 1;
                        $group_id = array();
                        // dd('empty-'.$r);
                        while (!empty($row[$i])) {
                            // echo $row[$i].'</br>';
                            if(empty($gm[trim($row[$i])])){
                                $add_gm = array();
                                $add_gm['name'] = trim($row[$i]);
                                $gm_id = GroupMail::create($add_gm)->id;
                                $gm[trim($row[$i])] = $gm_id;
                            }
                            $group_id[$i] = $gm[trim($row[$i])];
                            $i++;
                        }
                        // dd($group_id);
                    }                                              
                    if ($r > 1 && !empty(trim($row[0]))) { 
                        // dd('empty-'.$r); 
                        if(empty($folder[trim($row[0])])){
                            $add_fd = array();
                            $add_fd['name'] = trim($row[0]);
                            $fd_id = Folder::create($add_fd)->id;
                            $folder[trim($row[0])] = $fd_id;
                        }
                        $folder_id[$r] = $folder[trim($row[0])];
                        // echo $folder_id[$r].'->'.$folder[trim($row[0])].'</br>';
                        // dd($group_id);
                        foreach ($group_id as $key => $value) {
                            // if($key==1)  echo $key.'</br>';
                            if(!empty($row[$key])){
                                $use_gm[$folder_id[$r]][$value] = trim($row[$key]);                     
                            } 
                        }  
                    }
                } 
                foreach ($use_gm as $kfolder => $vfolder) {
                    foreach ($vfolder as $kgm => $vgm) {
                        $to_save = array();
                        $to_save['folder_id'] = $kfolder;
                        $to_save['group_mail_id'] = $kgm;
                        if($vgm=='F')   $to_save['to_full'] = 1; 
                        else     $to_save['to_full'] = 0;                               
                        if($vgm=='R')   $to_save['to_read'] = 1; 
                        else     $to_save['to_read'] = 0; 
                        if(empty($fig_data[$kfolder][$kgm])){ 
                            FolderInGroup::create($to_save);
                        }else{
                            FolderInGroup::where('id', $fig_data[$kfolder][$kgm])->update($to_save); 
                        }
                    }
                }       
                return back()->with('success','Import successfully');  
            } else {
                echo SimpleXLSX::parseError();
            }   
        }else{
            return back()->with('error','No file');
        } 
        
    }
    
    // public function display($gmr_all, $key) { 
        // // echo $key.'</br>';
        // global $to_return;  
        // $tr = 0;       
        // if(!empty($gmr_all['down'][$key])){  
        //     foreach ($gmr_all['down'][$key] as $key1 => $value1) {
        //         // echo $key1.'->'.$key1.'</br>'; 
        //         $to_return[$key1] = $key1;          
        //         $this->display($gmr_all, $key1);
        //     }             
        // }
        // if(!empty($gmr_all['up'][$key])){  
        //     foreach ($gmr_all['up'][$key] as $key1 => $value1) {
        //         // echo $key1.'->'.$key1.'</br>'; 
        //         $to_return[$key1] = $key1; 
        //     }
        // }
        // return $to_return;  
    public function display($a, $key1, $value1, $gmr_all) { 
        global $newArray; 
        global $last_loop;
        global $num;
        global $chk;
        if(empty($last_loop))   $last_loop=0;
        if(is_null($num) || $chk<>$key1){
            $num=0;
        }  
        if(is_null($chk)){
            $chk=$key1;
        }   
        if($a==1 && $num==0){
            $last_loop=0;
            $newArray[$key1][$num][0] = $value1;
        }
        if(!empty($gmr_all['up'][$value1])){
            foreach ($gmr_all['up'][$value1] as $kdet => $vdet) { 
                if($a==$last_loop+1){
                    $newArray[$key1][$num][$a] = $kdet;   
                }else{
                    $num++; 
                    for($i=0;$i<=$a;$i++){
                        if($i < $a){
                            $newArray[$key1][$num][$i] = $newArray[$key1][$num-1][$i]; 
                        }else{
                            $newArray[$key1][$num][$a] = $kdet; 
                        }                            
                    }
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
        $folder = Folder::findOrFail($id);
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
        // dd($gmr_all['up'][2]);
        //เป็นการหาย้อนขึ้นไป
        $folder_grorp = array();  
        $folder_grorp = FolderInGroup::where('folder_id', $id)->where('status', 1)->get();
        $loop = 0;
        $to_show = array();
        // $row_id = array();
        foreach ($folder_grorp as $key) {
            $to_show[$loop] = $key->group_mail_id;  //8, 2
            $row_id[$loop] = $key->id;  //1, 767
            $not_in[] = $key->group_mail_id;


            // echo $key->group_mail_id.'</br>'; 
            // $set_group[$loop][] = $key->group_mail_id;
            $f_r[$loop]['full'] = $key->to_full;
            $f_r[$loop]['read'] = $key->to_read;
            // $row_id[$loop] = $key->id;
            // $chk_det = $key->group_mail_id;

            // if(empty($chk_duplicate[$chk_det]))  $chk_duplicate[$chk_det] = 1;
            // else    $chk_duplicate[$chk_det] += 1;
            // $not_in[] = $chk_det;

            // while (!empty($gmr_all['up'][$chk_det])) {                    
            //     foreach ($gmr_all['up'][$chk_det] as $key1 => $value1) {
            //         $chk_det = $key1;
            //         $set_group[$loop][] = $key1;

            //         if(empty($chk_duplicate[$chk_det]))  $chk_duplicate[$chk_det] = 1;
            //         else    $chk_duplicate[$chk_det] += 1;
            //         $not_in[] = $chk_det;

            //     } 
            // }

            // $to_return = array();
            // $i = $key->group_mail_id;
            //ได้ชั้นเดียว ลงไปหลายชั้ยไม่ได้
            // while (!empty($gmr_all['down'][$i])) {
            //     foreach ($gmr_all['down'][$key->group_mail_id] as $key1 => $value1) {
            //         $to_return[$key1] = $key1;
            //         $i = $key1;
            //     }
            // }   

            // if(!empty($gmr_all['down'][$key->group_mail_id])){  
                // foreach ($gmr_all['down'][$key->group_mail_id] as $key1 => $value1) {
                //     $to_return[$key1] = $key1; 
                //     if(!empty($gmr_all['down'][$key1])){ 
                //         foreach ($gmr_all['down'][$key1] as $key2 => $value1) {
                //             $to_return[$key2] = $key2;
                //             if(!empty($gmr_all['down'][$key2])){ 
                //                 foreach ($gmr_all['down'][$key2] as $key3 => $value1) {
                //                     $to_return[$key3] = $key3;
                //                 }
                //             }
                //         }
                //     }
                // }
            // }
            //หาที่ถัดลงมา และสูงขึ้นไป
            // $to_down = $this->display($gmr_all, $key->group_mail_id);
            // print_r($to_down);
            $loop++;
        }
        // dd($to_show);
        $set_group = array();
        $to_recursive = array();
        foreach ($to_show as $key1 => $value1) { 
            $to_recursive = $this->display(1, $key1, $value1, $gmr_all);
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

        //เพิ่มกลุ่ม ต้องไม่มีกลุ่ม+หัว+หาง ของกลุ่มที่มีแล้ว
        $group_add = GroupMail::whereNotIn('id', $not_in)->where('status', 1)->orderBy('name')->get();
        
        return view('folder.group', compact('folder','id','gm_all','set_group','max_col','chk_duplicate','group_add','gmr_all','row_id','f_r'));
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
                $add_fig = array();
                $add_fig['folder_id'] = $id;
                $add_fig['group_mail_id'] = $value;
                FolderInGroup::create($add_fig);
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
        return redirect()->route('folder.to_group', $id)->with('success', 'Add Group!');
    }

    public function group_del($mail_del)
    {
        $set_status['status'] = '0';
        $mig = FolderInGroup::findOrFail($mail_del);
        $mig->update($set_status);

        return back()->with('success','Delete successfully');  
    }
}
