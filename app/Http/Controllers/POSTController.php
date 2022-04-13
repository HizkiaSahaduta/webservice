<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class POSTController extends Controller
{
    // KNI
    public function setApprove (Request $request) {

        $userid = $request->userid;
        $po_id = $request->po_id;   
        // $tr_date = Carbon::now();
        $tr_date = now();

        try{

            $set = DB::connection("sqlsrv2")
                    ->table('po_hdr')
                    ->where('po_id', '=', $po_id)
                    ->update([
                        'aprv_flag' => 'Y',
                        'dt_aprv' => $tr_date,
                        'aprv_by' => $userid,
                        'rjc_by' => ''
                    ]);

            if ($set) {

                $log= DB::connection("sqlsrv2")
                        ->table('log_history')
                        ->insert([
                            'tr_log' => 'WEBAPI/setApprove',
                            'doc_id' => $po_id,
                            'dt_trans' => $tr_date,
                            'remark' => 'Confirm (Approve) by '.$userid,
                            'user_id' => $userid
                        ]);

                if ($log) {

                    $response = ['message' => 'P.O Number: '.$po_id.' approved'];
                    return response($response, 200);

                }

                else {

                    $response = ['message' => 'Failed save to log history'];
                    return response($response, 200);

                }
               
            }

            else{

                $response = ['message' => 'Failed to approve P.O Number:'.$po_id.';'.$userid.';'.$tr_date];
                return response($response, 200);
            }

           
        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => 'Failed #:'.$error];
            return response($response, 422);
        }

    }

    public function setReject (Request $request) {

        $userid = $request->userid;
        $po_id = $request->po_id;   
        // $tr_date = Carbon::now();
        $tr_date = now();

        try{

            $set = DB::connection("sqlsrv2")
                    ->table('po_hdr')
                    ->where('po_id', '=', $po_id)
                    ->update([
                        'aprv_flag' => 'X',
                        'dt_rjc' => $tr_date,
                        'aprv_by' => '',
                        'rjc_by' => $userid
                    ]);

            if ($set) {

                $log= DB::connection("sqlsrv2")
                        ->table('log_history')
                        ->insert([
                            'tr_log' => 'WEBAPI/setReject',
                            'doc_id' => $po_id,
                            'dt_trans' => $tr_date,
                            'remark' => 'Confirm (Reject) by '.$userid,
                            'user_id' => $userid
                        ]);
                if ($log) {

                    $response = ['message' => 'P.O Number: '.$po_id.' rejected'];
                    return response($response, 200);

                }
                else {

                    $response = ['message' => 'Failed save to log history'];
                    return response($response, 200);

                }
            }

            else {

                $response = ['message' => 'Failed to approve P.O Number:'.$po_id.';'.$userid.';'.$tr_date];
                return response($response, 200);
            }


        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => 'Failed #:'.$error];
            return response($response, 422);
        }

    }

    public function setUnApprove (Request $request) {

        $userid = $request->userid;
        $po_id = $request->po_id;   
        // $tr_date = Carbon::now();
        $tr_date = now();

        try{

            $set = DB::connection("sqlsrv2")
                    ->table('po_hdr')
                    ->where('po_id', '=', $po_id)
                    ->update([
                        'aprv_flag' => '',
                        'dt_aprv' => '1900-01-01 00:00:00.000',
                        'dt_rjc' => '1900-01-01 00:00:00.000',
                        'aprv_by' => '',
                        'rjc_by' => ''
                    ]);

            if ($set) {

                $log= DB::connection("sqlsrv2")
                        ->table('log_history')
                        ->insert([
                            'tr_log' => 'WEBAPI/setUnApprove',
                            'doc_id' => $po_id,
                            'dt_trans' => $tr_date,
                            'remark' => 'Confirm (UnApprove) by '.$userid,
                            'user_id' => $userid
                        ]);

                if ($log) {

                    $response = ['message' => 'P.O Number: '.$po_id.' unapproved'];
                    return response($response, 200);

                }

                else {

                    $response = ['message' => 'Failed save to log history'];
                    return response($response, 200);

                }
            }

            else {

                $response = ['message' => 'Failed to approve P.O Number:'.$po_id.';'.$userid.';'.$tr_date];
                return response($response, 200);
            }

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => 'Failed #:'.$error];
            return response($response, 422);
        }

    }

    public function setDelivConfirm (Request $request) {

        $userid = $request->userid;
        $deliv_id = $request->deliv_id;  
        $order_id = $request->order_id;   
        // $tr_date = Carbon::now();
        $tr_date = now();

        try{

            $set = DB::connection("sqlsrv3")
                    ->table('deliv_hdr')
                    ->where('deliv_id', '=', $deliv_id)
                    ->where('order_id', '=', $order_id)
                    ->update([
                        'stat' => 'S',
                        'dt_confirm1' => $tr_date
                    ]);

            if ($set) {

                $log= DB::connection("sqlsrv2")
                        ->table('log_history')
                        ->insert([
                            'tr_log' => 'WEBAPI/setConfirmDeliv',
                            'doc_id' => $deliv_id,
                            'dt_trans' => $tr_date,
                            'remark' => 'Confirm Delivery Note by '.$userid,
                            'user_id' => $userid
                        ]);

                if ($log) {

                    $response = ['message' => 'Delivery Note: '.$deliv_id.' confirmed'];
                    return response($response, 200);

                }

                else {

                    $response = ['message' => 'Failed save to log history'];
                    return response($response, 200);

                }
               
            }

            else{

                $response = ['message' => 'Failed to confirm Delivery Note:'.$deliv_id.';'.$userid.';'.$tr_date];
                return response($response, 200);
            }

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => 'Failed #:'.$error];
            return response($response, 422);
        }

    }

    // PREORDER

    public function submitApproval (Request $request) {

        // $userid = $request->userid;
        // $tr_date = Carbon::now();
        $tr_date = now();
        $quoteID = $request->quoteID;
        $poID = $request->poID;
        $listApproval = $request->listApproval;
        $countApproval = substr_count($listApproval,"A");
        $temp1_listApproval = explode(',', $listApproval);

        try{

            foreach ($temp1_listApproval as $listApproval1) {


                $quote_item = substr($listApproval1, 2);
    
                $approval = $listApproval1[0];


                if ($approval == "X") {

                    $updateEachDetailQuote = DB::connection("sqlsrv4")
                                            ->table('quote_item')
                                            ->where('quote_id', '=', $quoteID)
                                            ->where('quote_item', '=', $quote_item )
                                            ->update([
                                                'stat' => 'X'
                                            ]);

                    $updateEachDetailPO = DB::connection("sqlsrv4")
                                            ->table('sales_po_item')
                                            ->where('po_id', '=', $poID )
                                            ->where('item_num', '=', $quote_item )
                                            ->update([
                                                'stat' => 'X'
                                            ]);

                }

    
            }

            if ($countApproval > 0) {

                $response = ['message' => 'Thank you for your confirmation'];
                return response($response, 200);

            }

            else {

                $response = ['message' => 'Order rejected'];
                return response($response, 200);


            }
    
           
          
        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    public function getListOrder (Request $request) {

        $searchkey = $request->searchkey;
        $skuAll = $request->skuAll;
        $scAll = $request->scAll;
        $skuOpen =  $request->skuOpen;
        $skuPosted =  $request->skuPosted;
        $skuQuot =  $request->skuQuot;
        $skuConfirm =  $request->skuConfirm;
        $skuClosed =  $request->skuClosed;
        $skuReject =  $request->skuReject;
        $scOpen =  $request->scOpen;
        $scClosed =  $request->scClosed;
        $scReject =  $request->scReject;
        $groupid = $request->groupid;
        $salesid = $request->salesid;
        $custid = $request->custid;

        if($request->startDate != null || $request->startDate = '' )
        {
            $startDate = $request->startDate;
        }
        else
        {
            $startDate = '';
        }

        if($request->endDate != null || $request->endDate = '' )
        {
            $endDate = $request->endDate;
        }
        else
        {
            $endDate = '';
        }

        if($request->txtCustomer != null || $request->txtCustomer = '' )
        {
            $txtCustomer = $request->txtCustomer;
        }
        else
        {
            $txtCustomer = '';
        }

        $where = "where 1=1";

        if(!$skuAll && !$scAll && !$skuOpen && !$skuPosted && !$skuQuot && !$skuConfirm && !$skuClosed && !$skuReject && !$scOpen && !$scClosed && !$scReject) {

            $where .= " and a.sc_stat not in ('C', 'X')";
        }

        if ($skuAll) {

            $where .= " and a.last_stat in ('O', 'P', 'R', 'S', 'C', 'X')";

        }

        if ($skuOpen) {

            $where .= " and a.last_stat = 'O'";

        }

        if ($skuPosted) {

            $where .= " and a.last_stat = 'P'";

        }

        if ($skuQuot) {

            $where .= " and a.last_stat = 'R'";

        }

        if ($skuConfirm) {

            $where .= " and a.last_stat = 'S'";

        }

        if ($skuClosed) {

            $where .= " and a.last_stat = 'C'";

        }

        if ($skuReject) {

            $where .= " and a.last_stat = 'X'";

        }

        if ($scAll) {

            $where .= " and a.sc_stat in ('O', 'R', 'C', 'X', 'N/A')";

        }

        if ($scOpen) {

            $where .= " and a.sc_stat in ('O', 'R')";

        }

        if ($scClosed) {

            $where .= " and a.sc_stat = 'C'";

        }

        if ($scReject) {

            $where .= " and a.sc_stat = 'X'";

        }

        if (!empty($startDate))
        {
            if (!empty($endDate))
            {
                $where .= "and a.tr_date between '$startDate' and '$endDate' ";
            }
            else
            {
                //$sqlWhere = $sqlWhere . "and dt_order = '" .$startDate. "'";
				//$sqlWhere = $sqlWhere . "and dt_order >= TRIM('$startDate') and dt_order <= getDate() ";
				$where .= "and a.tr_date between '$startDate' and format(getDate(), 'yyyy-MM-dd') ";
            }
        }

        if (!empty($txtCustomer))
        {
            $where .= " and a.cust_id = '$txtCustomer'";
        }

        // if ($searchkey) {

        //     $where .= " and c.ord_desc like '%$searchkey%'";

        // }

        if ($groupid == 'SALES') {

            try{

                // $data = DB::connection("sqlsrv4")
                // ->select(DB::raw("select distinct a.*, b.salesman_name from (
                //     select b.stat as last_stat, 
                //     case
                //         when a.stat is null then 'N/A'
                //         else a.stat
                //     end as sc_stat, b.book_id,
                //     convert(varchar(10), b.tr_date, 120) as tr_date, 
                //     convert(varchar(10), a.dt_order, 120) as dt_order, 
                //     ltrim(rtrim(b.order_id)) as order_id, 
                //     ltrim(rtrim(b.cust_id)) as cust_id , ltrim(rtrim(b.cust_name)) as cust_name, a.day_change,
                //     ltrim(rtrim(a.mpf_id)) as mpf_id, convert(varchar(10), a.dt_close, 120) as dt_close, a.after_close, a.ppp,
                //     b.user_id, b.image, b.salesman_id
                //     from 
                //     view_sc_preorder a 
                //     right join OPENQUERY([MYSQL], 'SELECT * FROM order_book_hdr ') b on a.order_id = b.order_id) a
                //     left join salesman b on a.salesman_id = b.salesman_id
                //     inner join OPENQUERY([MYSQL], 'SELECT * FROM order_book_dtl') c on a.book_id = c.book_id
                //     $where and a.salesman_id = '$salesid' order by a.tr_date desc"));

                $data = DB::connection("sqlsrv4")
                ->select(DB::raw("
                select distinct a.*, b.salesman_name from (
                select b.stat as last_stat, 
                case
                    when a.stat is null then 'N/A'
                        else a.stat
                end as sc_stat, b.book_id,
                convert(varchar(10), b.tr_date, 120) as tr_date, 
                convert(varchar(10), a.dt_order, 120) as dt_order, 
                ltrim(rtrim(b.order_id)) as order_id, 
                ltrim(rtrim(b.cust_id)) as cust_id , ltrim(rtrim(b.cust_name)) as cust_name, a.day_change,
                ltrim(rtrim(a.mpf_id)) as mpf_id, convert(varchar(10), a.dt_close, 120) as dt_close, a.after_close, a.ppp,
                b.user_id, b.image, b.salesman_id
                from 
                view_sc_preorder a 
                right join OPENQUERY([MYSQL], 'SELECT  cust_id, tr_date, book_id, quote_id, stat, order_id, cust_name, user_id, image, salesman_id  FROM order_book_hdr') b on a.order_id = b.order_id) a
                left join salesman b on a.salesman_id = b.salesman_id
                inner join OPENQUERY([MYSQL], 'SELECT book_id FROM order_book_dtl') c on a.book_id = c.book_id
                $where and a.salesman_id = '$salesid' order by a.tr_date desc
                "));

                  
                return response($data, 200);
            }
            catch(QueryException $ex){
    
                $error = $ex->getMessage();
                $response = ['message' => $error];
                return response($response, 422);
            }
        }

        if ($groupid == 'CUSTOMER') {

            try{


                // $list_custid = '';

                // $salesid = DB::connection("sqlsrv4")
                //             ->table('customer')
                //             ->select('salesman_id')
                //             ->where('cust_id','=', $custid)
                //             ->Value('salesman_id');

                // $cust_grp_id_tmp = DB::connection("sqlsrv4")
                //             ->table('customer')
                //             ->selectRaw('LTRIM(RTRIM(cust_grp_id)) as cust_grp_id')
                //             ->where('cust_id', '=', $custid)
                //             ->where('active_flag','=', 'Y')
                //             ->groupBy('cust_grp_id')
                //             ->value('cust_grp_id');

                
                // $list_custid_tmp = DB::connection("sqlsrv4")
                //                 ->table('customer')
                //                 ->selectRaw('LTRIM(RTRIM(cust_id)) as cust_id')
                //                 ->where('cust_grp_id', '=', $cust_grp_id_tmp)
                //                 ->where('active_flag','=', 'Y')
                //                 ->get();


                // foreach ($list_custid_tmp as $list_custid_tmp) {
                    

                //     $list_custid .= "'".$list_custid_tmp->cust_id."',";
                    
                // }

                // $list_custid = substr_replace($list_custid, "", -1);

                $data = DB::connection("sqlsrv4")
                ->select(DB::raw("
                select distinct a.*, b.salesman_name from (
                select b.stat as last_stat, 
                case
                    when a.stat is null then 'N/A'
                        else a.stat
                end as sc_stat, b.book_id,
                convert(varchar(10), b.tr_date, 120) as tr_date, 
                convert(varchar(10), a.dt_order, 120) as dt_order, 
                ltrim(rtrim(b.order_id)) as order_id, 
                ltrim(rtrim(b.cust_id)) as cust_id , ltrim(rtrim(b.cust_name)) as cust_name, a.day_change,
                ltrim(rtrim(a.mpf_id)) as mpf_id, convert(varchar(10), a.dt_close, 120) as dt_close, a.after_close, a.ppp,
                b.user_id, b.image, b.salesman_id
                from 
                view_sc_preorder a 
                right join OPENQUERY([MYSQL], 'SELECT  cust_id, tr_date, book_id, quote_id, stat, order_id, cust_name, user_id, image, salesman_id  FROM order_book_hdr') b on a.order_id = b.order_id) a
                left join salesman b on a.salesman_id = b.salesman_id
                inner join OPENQUERY([MYSQL], 'SELECT book_id FROM order_book_dtl') c on a.book_id = c.book_id
                $where and a.cust_id = '$custid' order by a.tr_date desc
                "));

                return response($data, 200);
            }
            catch(QueryException $ex){
    
                $error = $ex->getMessage();
                $response = ['message' => $error];
                return response($response, 422);
            }

        }

        if ($groupid != 'SALES' || $groupid != 'CUSTOMER') {

            try{
                $data = DB::connection("sqlsrv4")
                ->select(DB::raw("
                select distinct a.*, b.salesman_name from (
                select b.stat as last_stat, 
                case
                    when a.stat is null then 'N/A'
                        else a.stat
                end as sc_stat, b.book_id,
                convert(varchar(10), b.tr_date, 120) as tr_date, 
                convert(varchar(10), a.dt_order, 120) as dt_order, 
                ltrim(rtrim(b.order_id)) as order_id, 
                ltrim(rtrim(b.cust_id)) as cust_id , ltrim(rtrim(b.cust_name)) as cust_name, a.day_change,
                ltrim(rtrim(a.mpf_id)) as mpf_id, convert(varchar(10), a.dt_close, 120) as dt_close, a.after_close, a.ppp,
                b.user_id, b.image, b.salesman_id
                from 
                view_sc_preorder a 
                right join OPENQUERY([MYSQL], 'SELECT  cust_id, tr_date, book_id, quote_id, stat, order_id, cust_name, user_id, image, salesman_id  FROM order_book_hdr') b on a.order_id = b.order_id) a
                left join salesman b on a.salesman_id = b.salesman_id
                inner join OPENQUERY([MYSQL], 'SELECT book_id FROM order_book_dtl') c on a.book_id = c.book_id
                $where order by a.tr_date desc
                "));
                  
                return response($data, 200);
            }
            catch(QueryException $ex){
    
                $error = $ex->getMessage();
                $response = ['message' => $error];
                return response($response, 422);
            }

        }


    }
	
	public function getOrderTracking(Request $request) {
		
		$sqlWhere = "1=1 ";
        //$txtCustomer = $request->txtCustomer;
		
		if($request->txtCustomer != null)
        {
            $txtCustomer = $request->txtCustomer;
        }
        else
        {
            $txtCustomer = '';
        }
		
		if($request->txtSales != null)
        {
            $txtSales = $request->txtSales;
        }
        else
        {
            $txtSales = '';
        }
        
        if($request->txtNoSC != null)
        {
            $txtNoSC = $request->txtNoSC;
        }
        else
        {
            $txtNoSC = '';
        }

        if($request->startDate != null || $request->startDate = '' )
        {
            $startDate = $request->startDate;
        }
        else
        {
            $startDate = '';
        }

        if($request->endDate != null || $request->endDate = '' )
        {
            $endDate = $request->endDate;
        }
        else
        {
            $endDate = '';
        }
		
		if($request->txtOutstanding != null)
        {
            $txtOutstanding = $request->txtOutstanding;
        }
        else
        {
            $txtOutstanding = '';
        }

		if (!empty($txtCustomer))
        {
            $sqlWhere =  $sqlWhere . "and cust_id = TRIM('$txtCustomer') ";
        }
		
		if (!empty($txtSales))
        {
            $sqlWhere =  $sqlWhere . "and salesman_id = TRIM('$txtSales') ";
        }

        if (!empty($txtNoSC))
        {
            $sqlWhere =  $sqlWhere . "and order_id = TRIM('$txtNoSC') ";
        }

        if (!empty($startDate))
        {
            if (!empty($endDate))
            {
                $sqlWhere = $sqlWhere . "and dt_order >= TRIM('$startDate') and dt_order <= TRIM('$endDate') ";
            }
            else
            {
                //$sqlWhere = $sqlWhere . "and dt_order = '" .$startDate. "'";
				//$sqlWhere = $sqlWhere . "and dt_order >= TRIM('$startDate') and dt_order <= getDate() ";
				$sqlWhere = $sqlWhere . "and dt_order between TRIM('$startDate') and format(getDate(), 'yyyyMMdd') ";
            }
        }

		switch ($txtOutstanding) {
		  case "A":
			$sqlWhere = $sqlWhere . "and dt_awal IS NULL and dt_akhir IS NULL ";
			break;
		  case "B":
			$sqlWhere = $sqlWhere . "and lpm_id = '' and aprv_ppp = '' ";
			break;
		  case "C":
			$sqlWhere = $sqlWhere . "and wgt_sisa > 0 ";
			break;
		case "D":
			$sqlWhere = $sqlWhere . "and deliv_id <> '' ";
			break;
		case "E":
			$sqlWhere = $sqlWhere . "and aprv_ppp = 'Y' ";
			break;
		case "F":
			$sqlWhere = $sqlWhere . "and lpm_id <> '' and aprv_ppp <> 'Y' ";
			break;
		  default:
			
			$sqlWhere = $sqlWhere;
			
		}

        $result = DB::connection('sqlsrv4')
                    ->table("tracking_order")
                    ->selectRaw("cust_name as customer,
                    FORMAT(dt_order, 'dd/MM/yyyy') as tglSC,
                    item_num as itemNum,
                    descr,
                    FORMAT(wgt_ord, 'N0') as wgtOrder,
                    book_id as sku_id,
					order_id as NoSC,
                    FORMAT(tr_date, 'dd/MM/yyyy') as tgl_sku,
					CASE WHEN FORMAT(dt_awal, 'dd-MM-yyyy') IS NULL
					  THEN 'N/A'
					  ELSE CONCAT(FORMAT(dt_awal, 'dd/MM/yyyy'),' - ', FORMAT(dt_akhir, 'dd/MM/yyyy'))
					END as schedule,
					CASE WHEN lpm_id = ''
					  THEN 'N/A'
					  ELSE lpm_id
					END as ppp_id,
					CASE WHEN FORMAT(dt_trx, 'dd-MM-yyyy') = '01-01-1900'
					  THEN 'N/A'
					  ELSE FORMAT(dt_trx, 'dd/MM/yyyy')
					END as tgl_ppp,
                    FORMAT(wgt_lpm, 'N0') as wgt_lpm,
					CASE WHEN deliv_id = ''
					  THEN 'N/A'
					  ELSE deliv_id
					END as noDeliv,
					CASE WHEN FORMAT(dt_deliv, 'dd-MM-yyyy') = '01-01-1900'
					  THEN 'N/A'
					  ELSE FORMAT(dt_deliv, 'dd/MM/yyyy')
					END as dtDeliv,
                    FORMAT(wgt_deliv, 'N0') as wgtDeliv")
                    ->whereRaw($sqlWhere)
					->orderBy('tr_date', 'ASC')
					->orderBy('item_num', 'ASC')
					->take(1000)
                    ->get();

		return response($result);
		
	}
	
}
