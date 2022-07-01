<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupMail;
use App\Models\GroupMailRelation;
use App\Models\MailInGroup;
use App\Models\Mail;
use App\Models\FolderInGroup;
use App\Models\Folder;
use Session;

class GroupMailController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function display_down($gmr_all, $key, $hi, $arr) { 
        // echo $key.'</br>';
        // global $to_down;
        global $newArray;  
        // global $reference;   
        global $have_it;   
        $have_it['all'][$key] = 1;
        $have_it[$hi][$key] = 1;
        $arr[] = $key;
        if(empty($gmr_all['down'][$key])){   
            // $newArray = [];
            $reference =& $newArray;
            foreach ($arr as $key) {
                // $reference[$key] = [];
                $reference =& $reference[$key];
            }
            unset($reference);                
        }else{
            foreach ($gmr_all['down'][$key] as $kdet => $vdet) {              
                $this->display_down($gmr_all, $kdet, $hi+1, $arr);                
            }
        }     
        return [$newArray, $have_it];  
    }

    public function return_search($key, $value, $chk_key, $chk_val, $gm_all, $id) {
        global $newArray_2;
        global $head_me;
        if(!empty($gm_all['search'][$chk_key])){
            $newArray_2[$key] = $value;
        }
        if(is_array($chk_val)){
            foreach ($chk_val as $key1 => $value1) {
                if(!empty($id)){
                    if($key1==$id){
                        $head_me = $key;
                    }
                }
                $this->return_search($key, $value, $key1, $value1, $gm_all, $id);
            }
        }
        // dd($arr);
        return [$newArray_2, $head_me];
    }

    public function index(Request $request)
    {        
        // $txt_search ='';
        // $set_col ='';
        // dd($request->txt_search);
        if(!empty($request->page_main)){
            // dd('t-txt_search');
            Session::put('txt_search', $request->txt_search); 
        // }
        // if(isset($request->set_col)){ 
            Session::put('set_col', $request->set_col);  
        }
        Session::put('txt_mail', '');
        Session::put('txt_folder', '');

        $query = array(); 
        $gm_all_show = array(); 
        $gm_all = array(); 
        $gm = array();
        $query = GroupMail::where('status', 1)->orderBy('name')->get();
        foreach ($query as $key) {
            // echo $key->id.'->name: '.$key->name.'->public: '.$key->set_column.'</br>';
            $gm_all_show['id'][$key->id] = $key->name;
            $gm_all_show['set'][$key->id] = $key->set_column;
        }

        $query = new GroupMail;
        $query = $query->where('status', 1);
        if(!empty(Session::get('txt_search'))){
            // $txt_search = $request->txt_search; 
            // $query = $query->where('name', 'like', '%'.$txt_search.'%'); 
            $query = $query->where('name', 'like', '%'.Session::get('txt_search').'%');
        }  
        if(!empty(Session::get('set_col'))){
            // $set_col = $request->set_col;             
            if(Session::get('set_col')=='public')  $query = $query->where('set_column', 1);
            else    $query = $query->where('set_column', 0);
        }          
        $query = $query->orderBy('name')->get();

        // $query = GroupMail::where('status',1)->get();
        foreach ($query as $key) {
            // echo $key->id.'->name: '.$key->name.'->public: '.$key->set_column.'</br>';
            $gm_all['id'][$key->id] = $key->name;
            $gm_all['set'][$key->id] = $key->set_column;
            $gm_all['search'][$key->id] = $key->id;
        }
        // dd($gm_all['search']);

        // $query = GroupMailRelation::where('status',1);
        $gmr_all = array();
        if(!empty($gm_all['search'])){
            $query = new GroupMailRelation;
            $query = $query->where('status', 1);
            // if(!empty($request->txt_search) || !empty($request->set_col)){
            //     $query = $query->where(function($query1) use($gm_all) {
            //         $query1->whereIn('group_mail_main', $gm_all['search'])
            //         ->orWhereIn('group_mail_detail', $gm_all['search']);
            //     });
            // }
            $query = $query->get();
            foreach ($query as $key) {
                $gmr_all['up'][$key->group_mail_detail][$key->group_mail_main] = $key->id;      //ขึ้นไป
                $gmr_all['down'][$key->group_mail_main][$key->group_mail_detail] = $key->id;    //ลงมา
            }
        }
        
        $to_show = array();
        if(!empty($gmr_all['down'])){
            foreach ($gmr_all['down'] as $key=>$value) {  
                $to_show = $this->display_down($gmr_all, $key, 0, $arr= array()); 
            }
        }
        $to_display_down = array();
        $have_it = array();
        if(!empty($to_show[0]))     $to_display_down = $to_show[0];
        if(!empty($to_show[1]))     $have_it = $to_show[1];
        // dd($to_display_down);     
        // echo count($have_it).'</br>';    
        $loop = count($have_it)-2;
        if(!empty($have_it[0])){
            foreach ($have_it[0] as $key => $value) {
                for($i=1; $i<=$loop; $i++){
                    if(!empty($have_it[$i][$key])){
                        unset($to_display_down[$key]);
                    }
                }
            }
        }
        // dd($to_display_down);

        //หาตัวที่จะแสดงผล
        $query = array();
        $to_return = array();
        foreach ($to_display_down as $key => $value) {
            $query = $this->return_search($key, $value, $key, $value, $gm_all,$hm=0);
            $to_return = $query[0];
        }

        if(!empty($gm_all['id'])){
            foreach ($gm_all['id'] as $key => $value) {
                if(empty($have_it['all'][$key])){
                    $to_return[$key]= 1;
                }
            }
        }   
        // dd($to_return);  

        $query = array();
        $num_gm = array();
        $query = GroupMailRelation::where('status', 1)
            ->selectRaw('COUNT(group_mail_detail) AS group_mail_detail, group_mail_main')
            ->groupBy('group_mail_main')
            ->get();
        foreach ($query as $key) {
            $num_gm[$key->group_mail_main] = $key->group_mail_detail;
        } 

        $num_m = array();
        $query = MailInGroup::where('status', 1)
            ->selectRaw('group_mail_id, COUNT(mail_id) AS mail_id')
            ->groupBy('group_mail_id')
            ->get();
        foreach ($query as $key) {
            $num_m[$key->group_mail_id] = $key->mail_id;
        }

        $num_f = array();
        $query = FolderInGroup::where('status', 1)
            ->selectRaw('group_mail_id, COUNT(folder_id) AS folder_id')
            ->groupBy('group_mail_id')
            ->get();
        foreach ($query as $key) {
            $num_f[$key->group_mail_id] = $key->folder_id;
        }

        return view('group_mail.index',compact('num_gm','num_m','num_f','to_return','gm_all','gm_all_show'));
    }

    public function index_1(Request $request)
    {
        // dd($request);
        $txt_search ='';
        $set_col ='';
        $gm = array();
        // $gm = GroupMail::orderBy('status','DESC')->orderBy('name')->get();
        $gm = new GroupMail;
        $gm = $gm->where('status', 1);
        if(!empty($request->txt_search)){
            $txt_search = $request->txt_search;      
            $gm = $gm->where('name', 'like', '%'.$txt_search.'%');
        }  
        if(!empty($request->set_col)){
            $set_col = $request->set_col;   
            if($set_col=='public')  $gm = $gm->where('set_column', 1);
            else    $gm = $gm->where('set_column', 0);
        }          
        $gm = $gm->orderBy('status','DESC')->orderBy('name')->get();

        $query = array();
        $num_gm = array();
        $query = GroupMailRelation::where('status', 1)
            ->selectRaw('COUNT(group_mail_detail) AS group_mail_detail, group_mail_main')
            ->groupBy('group_mail_main')
            ->get();
        foreach ($query as $key) {
            $num_gm[$key->group_mail_main] = $key->group_mail_detail;
        } 

        $num_m = array();
        $query = MailInGroup::where('status', 1)
            ->selectRaw('group_mail_id, COUNT(mail_id) AS mail_id')
            ->groupBy('group_mail_id')
            ->get();
        foreach ($query as $key) {
            $num_m[$key->group_mail_id] = $key->mail_id;
        }

        return view('group_mail.index-old',compact('gm','txt_search','set_col','num_gm','num_m'));
    }

    public function chk_public(Request $request)
    {
        $requestData = $request->all();
        // dd($requestData['id']);
        $gm = GroupMail::findOrFail($requestData['id']);
        // dd($gm->name);
        if($requestData['chk']=='check'){
            $set_status['set_column'] = '1';
            $tex = "ปรับ ".$gm->name." เป็น Public mail แล้ว";
        }else{
            $set_status['set_column'] = '0';
            $tex = "ปรับ ".$gm->name." เป็น Private mail แล้ว";
        }
        $gm->update($set_status);

        // $txt_search ='';
        // $set_col ='';
        // if(!empty($requestData['txt_search']))    $txt_search = $requestData['txt_search'];
        // if(!empty($requestData['set_col']))       $set_col = $requestData['set_col'];

        // return redirect('group_mail?txt_search='.$txt_search.'&set_col'.$set_col)->with('success', ' updated public mail!');
        return $tex;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('group_mail.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $validatedData = $request->validate([
        //     'name' => 'required|unique:group_mails|max:255',
        // ]);

        $requestData = $request->all();
        $query = array();
        $query = GroupMail::where('name', $requestData['name'])->where('status', 1)->count();
        if($query>0){
            return redirect()->back()->with('error', 'ชื่อซ้ำกับที่มีอยู่!');
        }else{
            GroupMail::create($requestData);
            return redirect()->route('group_mail.index')->with('success', ' added!');
        }
        // dd($requestData['set_column']);
        // if(!empty($requestData['set_column'])){

        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $txt_search ='';
        // $set_col ='';
        // if(!empty($request->txt_search))    $txt_search = $request->txt_search;    
        // if(!empty($request->set_col))   $set_col = $request->set_col;  
        $gm = GroupMail::findOrFail($id);
        return view('group_mail.edit', compact('gm'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        // $txt_search ='';
        // $set_col ='';
        // if(!empty($request->txt_search))    $txt_search = $request->txt_search;    
        // if(!empty($request->set_col))   $set_col = $request->set_col;

        $requestData = $request->all(); 
        $query = array();
        $query = GroupMail::where('name', $requestData['name'])->where('status', 1)->whereNotIn('id', [$id])->count();
        if($query>0){
            return redirect()->back()->with('error', 'ชื่อซ้ำกับที่มีอยู่!');
        }else{
            $gm = GroupMail::findOrFail($id);
            $gm->update($requestData);  
            return redirect()->route('group_mail.index')->with('success', ' updated!');
        }

        // $gm = GroupMail::findOrFail($id);
        // $gm->update($requestData);      

        // return redirect()->route('group_mail.index')->with('success', ' updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $txt_search ='';
        // $set_col ='';
        // if(!empty($request->txt_search))    $txt_search = $request->txt_search;    
        // if(!empty($request->set_col))   $set_col = $request->set_col;

        $set_status['status'] = '0';

        // $i = 0;
        // do {
        //     if($i==0)   $to_query = $id;
        //     $query = GroupMailRelation::where('group_mail_main', $to_query)->where('status',1)->get();
        //     if(count($query)>0){
        //         $to_query = $query[0]['group_mail_detail'];
        //         $gm = GroupMailRelation::findOrFail($query[0]['id']);
        //         $gm->update($set_status);
        //     }
        //     $i++;
        // }while (count($query)>0);
        // $query = GroupMailRelation::where('group_mail_detail', $id)->where('status',1)->update($set_status);

        $gmr = GroupMailRelation::where('group_mail_main', $id)->orWhere('group_mail_detail', $id)->update($set_status);

        $gm = GroupMail::findOrFail($id);
        $gm->update($set_status);

        return redirect()->route('group_mail.index')->with('success', ' deleted!');
    }

    public function to_use($id)
    {
        $set_status['status'] = '1';
        $gm = GroupMail::findOrFail($id);
        $gm->update($set_status);
        return redirect()->route('group_mail.index')->with('success', ' deleted!');
    }

    public function to_cut(){
        $query = array();
        $gmr_all = array();
        $query = GroupMailRelation::where('status', 1)->get();
        foreach ($query as $key) {
            $gmr_all['up'][$key->group_mail_detail][$key->group_mail_main] = $key->id;      //ขึ้นไป
            $gmr_all['down'][$key->group_mail_main][$key->group_mail_detail] = $key->id;    //ลงมา
        }         

        $to_show = array();
        if(!empty($gmr_all['down'])){
            foreach ($gmr_all['down'] as $key=>$value) {  
                $to_show = $this->display_down($gmr_all, $key, 0, $arr= array()); 
            }
        }
        $to_return = array();
        $have_it = array();
        if(!empty($to_show[0]))     $to_return = $to_show[0];
        if(!empty($to_show[1]))     $have_it = $to_show[1];   
        // echo count($have_it).'</br>';    
        $loop = count($have_it)-2;
        if(!empty($have_it[0])){
            foreach ($have_it[0] as $key => $value) {
                for($i=1; $i<=$loop; $i++){
                    if(!empty($have_it[$i][$key])){
                        unset($to_return[$key]);
                    }
                }
            }
        }
        return [$to_return,$have_it];

        // if(!empty($gm_all['id'])){
        //     foreach ($gm_all['id'] as $key => $value) {
        //         if(empty($have_it['all'][$key])){
        //             $to_return[$key]= 1;
        //         }
        //     }
        // }
        // // dd($to_return); 
    }

    public function to_manage(Request $request, $id)
    {
        $query = array();  
        // $gm_all = array(); 
        $gm_status = array();       
        // $query = GroupMail::orderBy('name')->get();
        // foreach ($query as $key) {
        //     $gm_all[$key->id]['name'] = $key->name;
        //     $gm_all[$key->id]['status'] = $key->status;
        // } 

        $query = GroupMail::where('status',1)->whereNotIn('id', [$id])->orderBy('name')->get();
        foreach ($query as $key) {
            $gm_status[$key->id]['name'] = $key->name;
        }

        $gm = GroupMail::findOrFail($id);

        //หา group_mail_main ย้อนขึ้นไป
        $old_main = array();
        $i = 0;
        do {
            if($i==0)   $to_query = $id;
            $query = GroupMailRelation::where('group_mail_detail', $to_query)->where('status',1)->get();
            if(count($query)>0){
                $old_main[$i] = $query[0]['group_mail_main'];
                $to_query = $old_main[$i];
            }
            $i++;
        }while (count($query)>0);
        // dd($old_main);

        $to_detail = array();   //หา group_mail_detail
        $level = array();       //เก็บ query สำหรับ select option
        $to_show = array();     //เก็บ group_mail_detail
        $to_notin = array();
        $to_notin[0] = $id; 

        $gmr_all = array();     
        $gmr_all = $this->to_cut();
        $level_cut = array();
        $notin = array();
        $level_cut = $gmr_all[0];
        $notin = $gmr_all[1];
        // dd($notin);
        $query = array();
        $head_me = '';
        foreach ($level_cut as $key => $value) {
            unset($notin['all'][$key]);
            $query = $this->return_search($key, $value, $key, $value, $gm_all=[],$id);
            $head_me = $query[1];
            // if(is_array($value)){
            //     foreach ($value as $key1 => $value1) {
            //         if($key1==$id){
            //             $head_me = $key;
            //         }
            //         if(is_array($value1)){
            //             foreach ($value1 as $key2 => $value2) {
            //                 if($key2==$id){
            //                     $head_me = $key;
            //                 }
            //             }
            //         }
            //     }
            // }
        }
        // dd($head_me);
        if(!empty($head_me))    $to_notin[] = $head_me;
        if(count($notin['all'])>0){
            foreach ($notin['all'] as $key => $value) {
                $to_notin[] = $key;
            }            
        }
        // dd($to_notin);

        // //เป็นลูกแล้วจะไม่สามารถเป็นลูกได้อีก ยกเวันแสดงตัวแม่ของตัวเอง
        // $query = GroupMailRelation::where('status',1)->whereIn('group_mail_main', [$id])->get();
        // if(count($query)>0){
        //     foreach ($query as $key) {
        //         $to_notin[] = $key->group_mail_detail;
        //     }            
        // }

        // dd($to_notin);
        $level[] = GroupMail::whereNotIn('id', $to_notin)->where('status',1)->orderBy('name')->get();

        $i = 0;
        $to_detail = GroupMailRelation::where('group_mail_main', $id)->where('status',1)->get();
        foreach ($to_detail as $key) {
            $to_show[$i]['detail'] = $key->group_mail_detail;
            $to_show[$i]['id'] = $key->id;
            // $level[] = GroupMail::whereNotIn('id', $to_notin)->where('status',1)->orderBy('name')->get();
            // $to_notin[] = $key->group_mail_detail;
            $i++;
        }
        // $level[] = GroupMail::whereNotIn('id', $to_notin)->where('status',1)->orderBy('name')->get();
        // dd($level);

        //หาว่ามี mail ในกลุ่มนั้นมั้ย
        $query = MailInGroup::selectRaw('group_mail_id, COUNT(mail_id) AS mail_id')->where('status',1)->groupBy('group_mail_id')->get();
        foreach ($query as $key) {
            $chk_mail[$key->group_mail_id] = $key->mail_id;
        }

        // $to_detail[0] = GroupMailRelation::where('group_mail_main', $id)->where('status',1)->get();
        // // dd($to_detail[0]);
        // $level[0] = GroupMail::whereNotIn('id', $to_notin)->where('status',1)->orderBy('name')->get();
        // // dd($level[0][0]['group_mail_detail']);
        // if(count($to_detail[0])>0) {
        //     $to_show[0] = $to_detail[0][0]['group_mail_detail'];
        //     $to_notin[] = $to_detail[0][0]['group_mail_detail'];
        // }
        // // print_r($to_notin);
        // $i=0;
        // // echo $i.'--count = '.count($to_detail[$i]).'</br>';
        // while (count($to_detail[$i])>0) {
        //     // echo $i.'--count = '.count($level[$i]).' to_show = '.$to_show[$i].'</br>';
        //     $to_detail[($i+1)] = GroupMailRelation::where('group_mail_main', $to_show[$i])->where('status',1)->get();
        //     $level[($i+1)] = GroupMail::whereNotIn('id', $to_notin)->where('status',1)->orderBy('name')->get();
        //     if(count($to_detail[($i+1)])>0) {
        //         $to_show[($i+1)] = $to_detail[($i+1)][0]['group_mail_detail'];
        //         $to_notin[] = $to_detail[($i+1)][0]['group_mail_detail'];
        //         // print_r($to_notin);
        //     }
        //     $i++;
        // }
        // dd($level);
        
        return view('group_mail.manage', compact('gm_status','gm','level','to_show','old_main','to_notin','chk_mail'));
    }

    public function manage(Request $request, $id)
    {
        // //กลับมาทำลบอันเก่าก่อนด้วย
        $query = array();
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
                $query = GroupMailRelation::where('group_mail_main', $id)->where('group_mail_detail', $value)->where('status',1)->count();
                if($query==0){
                    $add_gm_relation = array();
                    $add_gm_relation['group_mail_main'] = $id;
                    $add_gm_relation['group_mail_detail'] = $value;
                    GroupMailRelation::create($add_gm_relation);
                }
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
        return redirect()->route('group_mail.index')->with('success', ' Managed!');
    }

    public function manage_del($id)
    {
        $set_status['status'] = '0';
        $mig = GroupMailRelation::findOrFail($id);
        $mig->update($set_status);

        return back()->with('success','Delete successfully');  
    }

    public function importExportView()
    {
        return view('group_mail.import');
    }
   
    public function import(Request $request) 
    {
        $requestData = $request->all();
        $completedirectory = 'storage/app/public/upload/group_mail/';  

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
                if(count($xlsx->rows()[0])==5){                  
                    $set_status['status'] = 'Inactive';
                    foreach ($xlsx->rows() as $r => $row) {                                                 
                        if ($r > 0 && !empty(trim($row[1]))) {                
                            if(trim($row[3])==''){
                                return back()->with('error',trim($row[1]).' ไม่มีข้อมูลชั่วโมงเริ่มต้น');
                            }else{
                                $to_save = array();
                                $to_save['name'] = trim($row[1]);
                                $to_save['type'] = trim($row[2]);  
                                
                                $to_save['time'] = trim($row[3]);   
                                $to_save['desc'] = trim($row[4]);  
                                $chk_fl = GroupMail::where('status','Active')->where('name',trim($row[1]))->get();
                                // dd(count($chk_fl));
                                if(count($chk_fl)>0){
                                    GroupMail::where('status','Active')->where('name',trim($row[1]))->update($set_status);
                                }
                                // dd($to_save);
                                GroupMail::create($to_save); 
                            }                       
                        }
                    }
                    return back()->with('success','Updated successfully');  
                } else{
                    return back()->with('error','Can not Updated');
                } 
            } else {
                echo SimpleXLSX::parseError();
            }   
        }else{
            return back()->with('error','No file');
        } 
        
    }

    public function to_mail(Request $request, $id)
    {
        if(!empty($request->page_main)){ 
            Session::put('txt_mail', $request->txt_mail);  
        }
        $gm = array();
        $mig = array();
        $mig_all = array();
        $mail = array();
        $query = array(); 
        $txt_search = ''; 



        $gm = GroupMail::findOrFail($id);
        //เมลล์ทั้งหมดในกลุ่ม
        $query = MailInGroup::where('group_mail_id', $id)->where('status', 1)->get();
        foreach ($query as $key) {
            $mig_all[] = $key->mail_id;
        }

        $query = new MailInGroup;
        $query = $query->join('mails', 'mail_in_groups.mail_id', '=', 'mails.id');
        $query = $query->select('mail_in_groups.id', 'mail_in_groups.mail_id', 'mails.name', 'mails.pre_name', 'mails.email');
        $query = $query->where('mail_in_groups.group_mail_id', $id)->where('mail_in_groups.status', 1);
        if(!empty(Session::get('txt_mail'))){
            $txt_search = Session::get('txt_mail');        
            $query = $query->where(function($sub_query) use ($txt_search) {
                        $sub_query->where('mails.name', 'like', '%'.$txt_search.'%')
                        ->orWhere('mails.pre_name', 'like', '%'.$txt_search.'%')
                        ->orWhere('mails.email', 'like', '%'.$txt_search.'%');
                    });
        } 
        $query = $query->orderBy('mails.name')->get();
        foreach ($query as $key) {
            $mig[$key->id] = $key->mail_id;
        }
        // dd($mig);
        $query = Mail::where('status',1)->orderBy('name')->get();
        foreach ($query as $key) {
            $mail[$key->id]['name'] = $key->name;
            $mail[$key->id]['pre_name'] = $key->pre_name;
            $mail[$key->id]['email'] = $key->email;
        }

        $mail_add = Mail::whereNotIn('id', $mig_all)->where('status',1)->orderBy('name')->get(); 
        
        // //หากลุ่มที่เกี่ยวข้อง        
        // $query = new GroupMailRelation;
        // $query = $query->where('status', 1);
        // // if(!empty($request->txt_search) || !empty($request->set_col)){
        // //     $query = $query->where(function($query1) use($id) {
        // //         $query1->where('group_mail_main', $id)
        // //         ->orWhere('group_mail_detail', $id);
        // //     });
        // // }
        // $query = $query->get();
        // foreach ($query as $key) {
        //     $gmr_all['up'][$key->group_mail_detail][$key->group_mail_main] = $key->id;      //ขึ้นไป
        //     $gmr_all['down'][$key->group_mail_main][$key->group_mail_detail] = $key->id;    //ลงมา
        // }
        // // dd($gmr_all);
        // $gm_name = array(); 
        // $to_in_group = array(); 
        // $query = GroupMail::where('status',1)->get();
        // foreach ($query as $key) {
        //     $gm_name[$key->id] = $key->name;
        // }

        // $to_in_group[-1] = $this->up_group($gmr_all, $id);//หาขึ้นไป
        // //หาระดับเดียวกัน ไปหาในระดับขึ้นไปแล้ว
        // // if(!empty($gmr_all['down'][$to_in_group[-1][0]])){
        // //     foreach ($gmr_all['up'][$to_in_group[-1][0]] as $key=>$value) {
        // //         $to_in_group[0][] = $key;
        // //     }
        // // }
        // $to_in_group[1] = $this->down_group($gmr_all, $id);//หาลงมา        
        // // dd($to_in_group);
        // $ig_search = array(); 
        // foreach ($to_in_group as $key => $value) {
        //     if(is_array($value)){
        //         foreach ($value as $key1 => $value1) {
        //             foreach ($value1 as $key2 => $value2) {
        //                 $ig_search[] = $value2;
        //             }
        //         }
        //     }
        // }
        $query = array();
        $ig_search = array(); 
        $gm_name = array(); 
        $to_in_group = array();
        $query = $this->to_find($id);
        $ig_search = $query[0];
        $gm_name = $query[1];
        $to_in_group = $query[2];
        // print_r($ig_search);
        // echo '</br>';
        //รายชื่อในกลุ่มอื่น
        $mig_other = array(); 
        $query = new MailInGroup;
        $query = $query->join('mails', 'mail_in_groups.mail_id', '=', 'mails.id');
        $query = $query->select('mail_in_groups.id', 'mail_in_groups.group_mail_id', 'mail_in_groups.mail_id', 'mails.name', 'mails.pre_name', 'mails.email');
        $query = $query->whereIn('mail_in_groups.group_mail_id', $ig_search)->where('mail_in_groups.status', 1);
        if(!empty($txt_search)){
            $query = $query->where(function($sub_query) use ($txt_search) {
                        $sub_query->where('mails.name', 'like', '%'.$txt_search.'%')
                        ->orWhere('mails.pre_name', 'like', '%'.$txt_search.'%')
                        ->orWhere('mails.email', 'like', '%'.$txt_search.'%');
                    });
        } 
        $query = $query->orderBy('mails.name')->get();
        // dd($query);
        foreach ($query as $key) {
            $mig_other[$key->group_mail_id][] = $key->mail_id;
        }
        // dd($mig_other);

        return view('group_mail.mail',compact('gm','mig','mail','mail_add','to_in_group','mig_other','gm_name'));
    }

    public function to_find($id){
        //หากลุ่มที่เกี่ยวข้อง        
        $query = new GroupMailRelation;
        $query = $query->where('status', 1);
        $query = $query->get();
        foreach ($query as $key) {
            $gmr_all['up'][$key->group_mail_detail][$key->group_mail_main] = $key->id;      //ขึ้นไป
            $gmr_all['down'][$key->group_mail_main][$key->group_mail_detail] = $key->id;    //ลงมา
        }
        // dd($gmr_all);
        $gm_name = array(); 
        $to_in_group = array(); 
        $query = GroupMail::where('status',1)->get();
        foreach ($query as $key) {
            $gm_name[$key->id] = $key->name;
        }

        $to_in_group[-1] = $this->up_group($gmr_all, $id);//หาขึ้นไป
        $to_in_group[1] = $this->down_group($gmr_all, $id);//หาลงมา        
        // dd($to_in_group);
        $ig_search = array(); 
        foreach ($to_in_group as $key => $value) {
            if(is_array($value)){
                foreach ($value as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $ig_search[] = $value2;
                    }
                }
            }
        }
        return [$ig_search, $gm_name, $to_in_group];
    }

    //หาขึ้นไป
    public function up_group($gmr_all, $id, $i=0){
        global $up_g;
        if(!empty($gmr_all['up'][$id])){
            foreach ($gmr_all['up'][$id] as $key=>$value) {
                // echo 'up '.$id.'->'.$key.'</br>';
                $up_g[($i-1)][] = $key;
                if(!empty($gmr_all['down'][$key])){
                    foreach ($gmr_all['down'][$key] as $key1=>$value1) {
                        if($key1!=$id)  $up_g[($i-1)][] = $key1;
                    }
                }
                $this->up_group($gmr_all, $key);
            }
            // dd($gmr_all);
        }
        return $up_g;
    }
    //หาลงมา
    public function down_group($gmr_all, $id, $i=0){
        global $down_g;
        if(!empty($gmr_all['down'][$id])){
            foreach ($gmr_all['down'][$id] as $key=>$value) {
                $down_g[($i+1)][] = $key;
                $this->down_group($gmr_all, $key);
            }
        }
        return $down_g;
    }

    public function mail(Request $request, $id)
    {
        // dd($request->to_detail);
        $query = array();
        // $to_where = 0;
        // $i=0;
        foreach ($request->to_detail as $key => $value) {
            if(!empty($value)){
                $query = MailInGroup::where('mail_id', $value)->where('group_mail_id', $id)->where('status',1)->count();
                if($query==0){
                    $add_mig = array();
                    $add_mig['mail_id'] = $value;
                    $add_mig['group_mail_id'] = $id;
                    MailInGroup::create($add_mig);
                }
            }
        }        

        return redirect()->route('group_mail.to_mail',$id)->with('success', ' Managed!');
    }
   
    public function mail_del($mail_del)
    {
        $set_status['status'] = '0';
        $mig = MailInGroup::findOrFail($mail_del);
        $mig->update($set_status);

        return back()->with('success','Delete successfully');  
    }

    public function to_folder(Request $request, $id)
    {
        if(!empty($request->page_main)){  
            Session::put('txt_folder', $request->txt_folder);  
        }

        $gm = array();
        $fig = array();
        $folder = array();
        $query = array(); 
        $txt_folder = ''; 
        $folder_add = array();
        $not_in = array();

        $gm = GroupMail::findOrFail($id);        
        //หา folder id แม้ว่าจะมีตัว search อยู่
        $query = FolderInGroup::where('group_mail_id', $id)->where('status', 1)->get();
        foreach ($query as $key) {
            $not_in[] = $key->folder_id;
        }
        $query = new FolderInGroup;
        $query = $query->join('folders', 'folder_in_groups.folder_id', '=', 'folders.id');
        $query = $query->select('folder_in_groups.id', 'folder_in_groups.folder_id', 'folders.name', 'folder_in_groups.to_full', 'folder_in_groups.to_read');
        $query = $query->where('folder_in_groups.group_mail_id', $id)->where('folder_in_groups.status', 1);
        if(!empty(Session::get('txt_folder'))){
            $txt_folder = Session::get('txt_folder');        
            $query = $query->where('folders.name', 'like', '%'.$txt_folder.'%');
        } 
        $query = $query->orderBy('folders.name')->get();
        foreach ($query as $key) {
            $fig[$key->id]['f_id'] = $key->folder_id;
            $fig[$key->id]['full'] = $key->to_full;
            $fig[$key->id]['read'] = $key->to_read;            
        }
        // dd($fig);
        $query = Folder::where('status',1)->orderBy('name')->get();
        foreach ($query as $key) {
            $folder[$key->id]['name'] = $key->name;
        }
        
        $folder_add = Folder::whereNotIn('id', $not_in)->where('status',1)->orderBy('name')->get();        
        // dd($folder_add);

        // //หากลุ่มที่เกี่ยวข้อง        
        // $query = new GroupMailRelation;
        // $query = $query->where('status', 1);
        // $query = $query->get();
        // foreach ($query as $key) {
        //     $gmr_all['up'][$key->group_mail_detail][$key->group_mail_main] = $key->id;      //ขึ้นไป
        //     $gmr_all['down'][$key->group_mail_main][$key->group_mail_detail] = $key->id;    //ลงมา
        // }
        // // dd($gmr_all);
        // $gm_name = array(); 
        // $to_in_group = array(); 
        // $query = GroupMail::where('status',1)->get();
        // foreach ($query as $key) {
        //     $gm_name[$key->id] = $key->name;
        // }

        // $to_in_group[-1] = $this->up_group($gmr_all, $id);//หาขึ้นไป
        // $to_in_group[1] = $this->down_group($gmr_all, $id);//หาลงมา        
        // // dd($to_in_group);
        // $ig_search = array(); 
        // foreach ($to_in_group as $key => $value) {
        //     if(is_array($value)){
        //         foreach ($value as $key1 => $value1) {
        //             foreach ($value1 as $key2 => $value2) {
        //                 $ig_search[] = $value2;
        //             }
        //         }
        //     }
        // }

        $query = array();
        $ig_search = array(); 
        $gm_name = array(); 
        $to_in_group = array();
        $query = $this->to_find($id);
        $ig_search = $query[0];
        $gm_name = $query[1];
        $to_in_group = $query[2];

        // print_r($ig_search);
        // echo '</br>';
        //รายชื่อในกลุ่มอื่น
        $fig_other = array(); 
        $query = new FolderInGroup;
        $query = $query->join('folders', 'folder_in_groups.folder_id', '=', 'folders.id');
        $query = $query->select('folder_in_groups.id', 'folder_in_groups.group_mail_id', 'folder_in_groups.folder_id', 'folders.name');
        $query = $query->whereIn('folder_in_groups.group_mail_id', $ig_search)->where('folder_in_groups.status', 1);
        if(!empty($txt_search)){
            $query = $query->where('folders.name', 'like', '%'.$txt_search.'%');
        } 
        $query = $query->orderBy('folders.name')->get();
        // dd($query);
        foreach ($query as $key) {
            $fig_other[$key->group_mail_id][] = $key->folder_id;
        }
        // dd($mig_other);
        return view('group_mail.folder',compact('gm','fig','folder','folder_add', 'gm_name', 'to_in_group'));
    }

    public function folder(Request $request, $id)
    {
        // dd($request->to_detail);
        $query = array();
        // $to_where = 0;
        // $i=0;
        foreach ($request->to_detail as $key => $value) {
            if(!empty($value)){
                $query = FolderInGroup::where('folder_id', $value)->where('group_mail_id', $id)->where('status',1)->count();
                if($query==0){
                    $add_fig = array();
                    $add_fig['folder_id'] = $value;
                    $add_fig['group_mail_id'] = $id;
                    FolderInGroup::create($add_fig);
                }
            }
        }        

        return redirect()->route('group_mail.to_folder',$id)->with('success', ' Folder!');
    }

    public function folder_del($folder_del)
    {
        $set_status['status'] = '0';
        $fig = FolderInGroup::findOrFail($folder_del);
        $fig->update($set_status);

        return back()->with('success','Delete successfully');  
    }

    public function chk_folder(Request $request)
    {
        $requestData = $request->all();
        // dd($requestData['id']);
        $fig = FolderInGroup::findOrFail($requestData['id']);
        $gm = GroupMail::findOrFail($fig->group_mail_id);
        $folder = Folder::findOrFail($fig->folder_id);
        // dd($gm->name);
        if($requestData['chk']=='check'){
            if($requestData['type']=='full'){
                $set_status['to_full'] = '1';
                $set = 'Full';
            }else{
                $set_status['to_read'] = '1';
                $set = 'Read';
            }
        }else{
            if($requestData['type']=='full'){
                $set_status['to_full'] = '0';
                $set = 'Not Full';
            }else{
                $set_status['to_read'] = '0';
                $set = 'Not Read';
            }
        }
        $fig->update($set_status);
        $tex = "ปรับกลุ่ม ".$gm->name." ที่ Folder ".$folder->name." ให้เป็น ".$set." แล้ว";

        return $tex;
    }
}
