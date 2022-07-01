@extends('layouts.app-master')

@section('content')
    <div class="bg-light p-5 rounded">        
        @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('error') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('success') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row g-3 mb-2 align-items-center justify-content-between">
            <div class="col-auto">
                <h1 class="app-page-title mb-0">รายงาน</h1>
            </div>
        </div><!--//row onclick="getURL($key);" -->
    
        <nav class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
            <a class="flex-sm-fill text-sm-center nav-link active" data-bs-toggle="tab" href="#" role="tab"
                aria-controls="orders-all" aria-selected="true"></a>
        </nav>             			   
                
        <div class="tab-content" id="orders-table-tab-content">
            <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
                <div class="app-card app-card-orders-table shadow-sm mb-5">
                    
                    <form method="GET" action="{{ route('report.to_report') }}" accept-charset="UTF-8"
                        class="table-search-form row gx-1 align-items-center" role="search">
                        <div class="app-card-body">
                            <div class="table-responsive">
                                <table class="table app-table-hover mb-0 text-left yajra-datatable" id="tb_show">
                                    <thead>
                                        <tr>
                                            <th class="cell" style="width: 5%"></th>
                                            <th class="cell" style="text-align:right; width: 20%;">ประเภทรายงาน : </th>
                                            <th class="cell" colspan="3" style="width: 65%">
                                                <select name="report_id" class="form-select col" required>
                                                    <option value="" selected>==== เลือกประเภทรายงาน ====</option>
                                                    <option value="1">1.ลำดับการจัดกลุ่มเมลล์</option>
                                                    <option value="2">2.รายชื่อเมลล์ในแต่ละกลุ่ม</option>
                                                    {{-- <option value="3">3.กราฟแสดงรายการรับ</option>
                                                    <option value="4">4.รายงานสรุปการสุ่ม</option>
                                                    <option value="5">5.กราฟแสดงข้อมูลตามประเภทสินค้า</option> --}}
                                                </select>
                                            </th>
                                            <th class="cell"style="width: 10%"></th>
                                        </tr>
                                        {{-- <tr>
                                            <th class="cell" style="width: 5%"></th>
                                            <th class="cell" style="text-align:right; width: 20%">วันที่ : </th>
                                            <th class="cell" style="width: 30%">
                                                <input type="date" class="form-control col" id="st_date" name="st_date" value="{{ date("Y-m-d") }}">
                                                <input type="hidden" class="form-control col" id="rang_date" name="rang_date" value="5">
                                            </th>
                                            <th class="cell" style="text-align: center" style="width: 5%"> </th>
                                            <th class="cell" style="width: 30%">
                                                <input type="date" class="form-control col" id="ed_date" name="ed_date">
                                            </th>
                                            <th class="cell" style="width: 10%"></th>
                                        </tr> --}}
                                        <tr>
                                            <th class="cell"></th>
                                            <th class="cell"></th>
                                            <th class="cell">
                                                <button type="submit" class="btn btn-success">
                                                    {{ __('Report') }}
                                                </button>
                                            </th>
                                            <th class="cell"></th>
                                            <th class="cell">
                                            </th>
                                            <th class="cell"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div><!--//table-responsive-->
                        
                        </div><!--//app-card-body-->                           
                    </form>
                            
                </div><!--//app-card-->                    
            </div><!--//tab-pane-->			        
        </div><!--//tab-content-->    
        
    </div><!--//app-wrapper-->    
@endsection