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
}
