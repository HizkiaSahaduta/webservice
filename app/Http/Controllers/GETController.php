<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use GuzzleHttp\Client;

class GETController extends Controller
{
    // KNI

    public function getUserExist (Request $request) {

        $email = $request->email;

        try{

            $result = DB::connection("sqlsrv")
                        ->table('users')
                        ->where('email', '=', $email)
                        ->count();

            $response = ['message' => $result];
            return response($response, 200);

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    public function getPOHdr (Request $request) {

        $dt_start = $request->dt_start;
        $dt_end = $request->dt_end;
        $po_id = $request->po_id;
        $flag = $request->flag;
        $where = "where a.vendor_id='V003'";

        if ($flag && $flag != 'N') {

            $where.= " and a.aprv_flag = '$flag'";
        }

        if ($flag && $flag == 'N') {

            $where.= " and a.aprv_flag in ('','N')";
        }

        if ($po_id) {

            $where.= " and a.po_id = '$po_id'";
        }

        if ($dt_start && !$dt_end) {

            $where.= " and a.dt_po = '$dt_start'";
        }

        if (!$dt_start && $dt_end) {

            $where.= " and a.dt_po = '$dt_end'";
        }

        if ($dt_start && $dt_end) {

            $where.= " and a.dt_po between '$dt_start' and '$dt_end'";
        }
        
        try{

            $data = DB::connection("sqlsrv2")
                    ->select(DB::raw("select distinct a.office_id, 
                    LTRIM(RTRIM(a.po_id)) as po_id, 
                    FORMAT(a.dt_po, 'dd MMM yyyy') as dt_po,
                    LTRIM(RTRIM(a.pay_term_id)) as pay_term_id, 
                    a.stat, 
                    LTRIM(RTRIM(a.aprv_flag)) as aprv_flag,
                    cast(a.amt_subtotal as float) as amt_subtotal,
                    cast(a.amt_ppn as float) as amt_ppn,
                    cast(a.amt_net as float) as amt_net,
                    LTRIM(RTRIM(a.sp_id)) as sp_id
                    from po_hdr a
                    inner join po_item b on a.office_id=b.office_id and a.po_id=b.po_id 
                    inner join article c on b.article_id=c.article_id ".$where));

            //$response = ['data' => $data];
            return response($data, 200);

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    public function getSumHdr (Request $request) {

        $dt_start = $request->dt_start;
        $dt_end = $request->dt_end;
        $po_id = $request->po_id;
        $flag = $request->flag;

        $total_po = 0;
        $ppn = 0;
        $net = 0;

        $where = "where a.vendor_id='V003'";

        if ($flag && $flag != 'N') {

            $where.= " and a.aprv_flag = '$flag'";
        }

        if ($flag && $flag == 'N') {

            $where.= " and a.aprv_flag in ('','N')";
        }

        if ($po_id) {

            $where.= " and a.po_id = '$po_id'";
        }

        if ($dt_start && !$dt_end) {

            $where.= " and a.dt_po = '$dt_start'";
        }

        if (!$dt_start && $dt_end) {

            $where.= " and a.dt_po = '$dt_end'";
        }

        if ($dt_start && $dt_end) {

            $where.= " and a.dt_po between '$dt_start' and '$dt_end'";
        }
        
        try{

            $data = DB::connection("sqlsrv2")
                    ->select(DB::raw("select 
                    CASE WHEN cast(sum(a.amt_subtotal) as float) IS NULL THEN 0
                    ELSE cast(sum(a.amt_subtotal) as float)                                       
                    END AS total_po, 
                    CASE WHEN cast(sum(a.amt_ppn) as float) IS NULL THEN 0
                    ELSE cast(sum(a.amt_ppn) as float)                                       
                    END AS ppn,
                    CASE WHEN cast(sum(a.amt_net) as float) IS NULL THEN 0
                    ELSE cast(sum(a.amt_net) as float)                                       
                    END AS net
                    from po_hdr a
                    inner join po_item b on a.office_id=b.office_id and a.po_id=b.po_id 
                    inner join article c on b.article_id=c.article_id ".$where));

            foreach ($data as $data) {

                $total_po = number_format($data->total_po,2,",",".");
                $ppn = number_format($data->ppn,2,",",".");
                $net = number_format($data->net,2,",",".");
            }

            $data = ['total_po' => $total_po, 'ppn' => $ppn, 'net' => $net];
            return response($data, 200);

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }



    public function getPODtl (Request $request) {

        $po_id = $request->po_id;
        $where = "where a.vendor_id='V003' and a.po_id = '$po_id'";

        try{

            $data = DB::connection("sqlsrv2")
                    ->select(DB::raw("select a.office_id, a.po_id, a.dt_po, a.stat, b.po_item, b.article_id,
                    LTRIM(RTRIM(c.description)) as description,
                    cast(c.length as float) as length,
                    cast(b.qty as float) as qty,
                    b.unit_meas, 
                    cast(b.unit_price as float) as unit_price,
                    cast(b.amt_net as float) as amt_net
                    from po_hdr a
                    inner join po_item b on a.office_id=b.office_id and a.po_id=b.po_id 
                    inner join article c on b.article_id=c.article_id ".$where." order by b.po_item asc"));

            //$response = ['data' => $data];
            return response($data, 200);

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    public function getDelivHdr (Request $request) {

        $dt_start = $request->dt_start;
        $dt_end = $request->dt_end;
        $flag = $request->flag;
        $po_id = $request->po_id;
        //$where = "where co.CustomerId = '039990'";
        $where = "where co.CustomerId = '00047376'";

        if ($po_id) {

            $where.= " and co.NoReferensi = '$po_id'";
        }

        if ($flag && $flag == 'Y') {

            $where.= " and dh.dt_confirm1 <> '1900-01-01 00:00:00.000'";
        }

        if ($flag && $flag == 'N') {

            $where.= " and dt_confirm1 = '1900-01-01 00:00:00.000'";
        }

        if ($dt_start && !$dt_end) {

            $where.= " and dh.ship_date = '$dt_start'";
        }

        if (!$dt_start && $dt_end) {

            $where.= " and dh.ship_date = '$dt_end'";
        }

        if ($dt_start && $dt_end) {

            $where.= " and dh.ship_date between '$dt_start' and '$dt_end'";
        }
        
        try{

            $data = DB::connection("sqlsrv3")
                    ->select(DB::raw("select LTRIM(RTRIM(dh.deliv_id)) as deliv_id, LTRIM(RTRIM(co.NoReferensi)) as NoPo,
                    LTRIM(RTRIM(dh.order_id)) as order_id, 
                    LTRIM(RTRIM(dh.receiver)) as receiver, 
                    FORMAT(dh.ship_date, 'dd MMM yyyy') as ship_date, 
                    CASE WHEN dt_confirm1 <> '1900-01-01 00:00:00.000' THEN 'Y' ELSE 'N' END AS stat,
                    CASE WHEN dt_confirm1 <> '1900-01-01 00:00:00.000' THEN FORMAT(dh.dt_confirm1, 'dd MMM yyyy') ELSE '-' END AS dt_confirm1
                    from deliv_hdr dh
                    inner join CustomerOrder co on dh.mill_id = co.mill_id and dh.order_id = co.CustomerOrderNo ".$where));

            //$response = ['data' => $data];
            return response($data, 200);

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    public function getDelivDtl (Request $request) {

        $deliv_id = $request->deliv_id;
        $order_id = $request->order_id;
        $where = "where dh.deliv_id = '$deliv_id' and dh.order_id = '$order_id' order by dd.item_num asc";

        try{

            $data = DB::connection("sqlsrv3")
                    ->select(DB::raw("select dd.item_num,
                    LTRIM(RTRIM(dd.descr)) as descr,
                    cast(dd.qty_ship  as float) as qty_ship
                    from deliv_dtl dd
                    inner join deliv_hdr dh on dh.deliv_id = dd.deliv_id and dh.mill_id = dd.mill_id ".$where));

            //$response = ['data' => $data];
            return response($data, 200);

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    // PreOrder

    public function trackOrder (Request $request) {

        $book_id = $request->id;

        try{

            $data = DB::connection("sqlsrv4")
                    ->select(DB::raw("SELECT LTRIM(RTRIM(order_id)) as order_id, dt_order, LTRIM(RTRIM(pay_term_id)) as pay_term, 
                    item_num, LTRIM(RTRIM(descr)) as item, req_shipx, leat_time, wgt_ord, wgt_sisa,
                    LTRIM(RTRIM(lpm_id)) as lpm_id, FORMAT(dt_trx, 'dd MMM yyyy') as dt_lpm, FORMAT(valid_date, 'dd MMM yyyy') as dt_lpm_valid, wgt_lpm,
                    LTRIM(RTRIM(deliv_id)) as deliv_id, FORMAT(dt_deliv, 'dd MMM yyyy') as dt_deliv, wgt_deliv
                    from tracking_order 
                    where book_id = '$book_id'"));

            //$response = ['data' => $data];
            return response($data, 200);

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    public function dashboardOrderCustGroup (Request $request) {

        $txtCustID = $request->custid;
       
        $order_blm_kirim_jml = 0;
        $order_blm_kirim_wgt = 0;
        $inv_kurang_bayar_inv = 0;
        $inv_kurang_bayar_piutang = 0; 
        $siap_kirim_lpm = 0;
        $siap_kirim_wgt = 0;
        $siap_kirim_idr = 0;
        $inv_last_year_amt_total = 0; 
        $inv_last_year_total_inv = 0;
        $inv_last_year_amt_paid = 0;
        $inv_last_year_total_piutang = 0; 
        $total_credit_limit = 0; 
        $total_giro = 0; 
        $jml_ppp = 0;
        $total_ppp = 0;
        $jml_sc = 0;
        $total_sc = 0;
        $jml_quote = 0;
        $total_quote = 0;

        try{

            $credits = DB::connection("sqlsrv4")
                    ->select(DB::raw("select amt_credit_limit from customer where cust_id = '$txtCustID'"));

            foreach ($credits as $credit) {

                $total_credit_limit = "IDR. ".number_format($credit->amt_credit_limit,0,",",".");

            }

            $giros = DB::connection("sqlsrv4")
                    ->select(DB::raw("SELECT sum(giro_amt - used_amt) as giro_amt FROM giro_rcv WHERE cust_id = '$txtCustID' AND stat <> 'C'"));

            foreach ($giros as $giro) {

                $total_giro = "IDR. ".number_format($giro->giro_amt,0,",",".");

            }

            $ppps = DB::connection("sqlsrv4")
                    ->select(DB::raw("
                        select count(lpm_id) as jlm_ppp, sum(total_price) as total from view_out_ppplpm where cust_id = '$txtCustID' and aprv_flag <> 'Y' and stat = 'O'
                    "));

            foreach ($ppps as $ppp) {
                $jml_ppp = number_format($ppp->jlm_ppp,0,",",".");
                $total_ppp = "IDR. ".number_format($ppp->total,0,",",".");
            }

            $scs = DB::connection("sqlsrv4")
                        ->select(DB::raw("
                            select count(DISTINCT a.order_id) as jml_sc, sum((a.wgt_ord - a.wgt_shipped) * b.unit_price) as total 
                            from view_order_status a join order_item b on a.order_id = b.order_id and a.item_num = b.item_num
                            where a.cust_id = '$txtCustID' AND a.wgt_outstanding > 1500
                        "));

            foreach ($scs as $sc) {
                $jml_sc = number_format($sc->jml_sc,0,",",".");
                $total_sc = "IDR. ".number_format($sc->total,0,",",".");
            }

            $quotes = DB::connection("sqlsrv4")
                        ->select(DB::raw("
                        select count(quote_id) as jml_quote, sum(amt_total) as total_quote from view_quote_open where cust_id = '$txtCustID'
                        "));

            foreach ($quotes as $quote) {
                $jml_quote = number_format($quote->jml_quote,0,",",".");
                $total_quote = "IDR. ".number_format($quote->total_quote,0,",",".");
            }

            $cust_grp_id_tmp = DB::connection("sqlsrv4")
                                ->table('customer')
                                ->selectRaw('LTRIM(RTRIM(cust_grp_id)) as cust_grp_id')
                                ->where('cust_id', '=', $txtCustID)
                                ->where('active_flag','=', 'Y')
                                ->groupBy('cust_grp_id')
                                ->value('cust_grp_id');

            $count_cust = DB::connection("sqlsrv4")
                        ->table('customer')
                        ->select('cust_id')
                        ->where('cust_grp_id', '=', $cust_grp_id_tmp)
                        ->where('active_flag','=', 'Y')
                        ->count();


            $r1 = DB::connection("sqlsrv4")
                    ->select(DB::raw("SELECT b.cust_id, count(a.order_id) as jml_order, 
                            cast(sum(a.wgt_sisa) as float) as wgt_outstanding FROM tracking_order a 
                            left join customer b on a.cust_id = b.cust_id
                            WHERE a.deliv_id = ' ' and a.order_id <> ' ' and b.cust_id = '$txtCustID'
                            group by b.cust_id"));

            foreach ($r1 as $r1) {

                $order_blm_kirim_jml = number_format($r1->jml_order,0,",",".")." Orders";
                $order_blm_kirim_wgt = number_format($r1->wgt_outstanding/1000,2,",",".")." TON";

            }

            $r2 = DB::connection("sqlsrv4")
                    ->select(DB::raw("select b.cust_id, count(a.inv_id) as total_invoice, 
                    cast(sum(a.piutang) as float) as total_piutang from view_piutang a 
                    left outer join customer b on a.cust_id = b.cust_id 
                    where a.Piutang > 1 and b.cust_id = '$txtCustID'
                    GROUP BY b.cust_id"));

            foreach ($r2 as $r2) {

                $inv_kurang_bayar_inv = number_format($r2->total_invoice,0,",",".")." Inv";
                $inv_kurang_bayar_piutang = "IDR. ".number_format($r2->total_piutang,0,",",".");

            }

            $r3 = DB::connection("sqlsrv4")
                    ->select(DB::raw("select e.cust_id, count(a.lpm_id) as jml_lpm, sum(a.wgt) as wgt_lpm, cast(sum(a.wgt * c.unit_price * 1 * (100 - c.pct_disc) / 100 ) as float) as total
                    from lpm_item a join lpm_hdr b on a.lpm_id = b.lpm_id 
                    join order_item c on a.order_id = c.order_id and a.item_num = c.item_num
                    join order_mast d on c.order_id = d.order_id 
                    join customer e on d.cust_id = e.cust_id
                    where a.lpm_id like 'PPIC/LPM%' and b.deliv_id = ' ' and e.cust_id = '$txtCustID'
                    GROUP BY e.cust_id"));

            foreach ($r3 as $r3) {

                $siap_kirim_lpm = number_format($r3->jml_lpm,0,",",".")." LPM";
                $siap_kirim_wgt = number_format($r3->wgt_lpm/1000,2,",",".")." TON";
                $siap_kirim_idr = "IDR. ".number_format($r3->total,0,",",".");

            }

            $r4 = DB::connection("sqlsrv4")
                    ->select(DB::raw("select cast(count(a.inv_id) as float) as total_inv, cast(sum(a.amt_total) as float) as amt_total, cast(sum(a.amt_paid) as float) as amt_paid,
                    cast(sum(a.Piutang) as float) as total_piutang from view_piutang a
                    left outer join customer b on a.cust_id = b.cust_id 
                    where b.cust_id = '$txtCustID' and a.dt_inv >= DATEADD(M, -12, GETDATE())
                    group by b.cust_id"));

            foreach ($r4 as $r4) {


                $inv_last_year_total_inv = number_format($r4->total_inv,0,",",".")." Inv";
                $inv_last_year_amt_total = "IDR. ".number_format($r4->amt_total,0,",",".");
                $inv_last_year_amt_paid = "IDR. ".number_format($r4->amt_paid,0,",",".");
                $inv_last_year_total_piutang = "IDR. ".number_format($r4->total_piutang,0,",",".");

            }

            $r5 = DB::connection("sqlsrv4")
                    ->select(DB::raw("select b.prod_code, trim(c.descr) as descr, cast(sum(b.wgt) as float) as wgt, cast(sum(b.amt_total) as float) as amt_total 
                    from inv_mast a inner join inv_item b on a.inv_id = b.inv_id
                    inner join prod_spec c on b.prod_code = c.prod_code
                    inner join customer d on a.cust_id = d.cust_id
                    where a.dt_inv >= DATEADD(M, -12, GETDATE()) and d.cust_id = '$txtCustID'
                    group by b.prod_code, c.descr "));

            $data = ['order_blm_kirim_jml' => $order_blm_kirim_jml,
                    'order_blm_kirim_wgt' => $order_blm_kirim_wgt,
                    'inv_kurang_bayar_inv' => $inv_kurang_bayar_inv,
                    'inv_kurang_bayar_piutang' => $inv_kurang_bayar_piutang,
                    'siap_kirim_lpm' => $siap_kirim_lpm,
                    'siap_kirim_wgt' => $siap_kirim_wgt,
                    'siap_kirim_idr' => $siap_kirim_idr,
                    'inv_last_year_total_inv' => $inv_last_year_total_inv,
                    'inv_last_year_amt_total' => $inv_last_year_amt_total,
                    'inv_last_year_amt_paid' => $inv_last_year_amt_paid,
                    'inv_last_year_total_piutang' => $inv_last_year_total_piutang,
                    'list_prod_last_year' => $r5,
                    'total_credit_limit' => $total_credit_limit,
                    'total_giro' => $total_giro,
                    'jml_ppp' => $jml_ppp,
                    'total_ppp' => $total_ppp,
                    'jml_sc' => $jml_sc,
                    'total_sc' => $total_sc,
                    'jml_quote' => $jml_quote,
                    'total_quote' => $total_quote
                ];

            return response($data, 200);
        

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }



        

    }

    public function dashboardOrderbyCustID (Request $request) {

        $txtCustID = $request->custid;
       
        $order_blm_kirim_jml = 0;
        $order_blm_kirim_wgt = 0;
        $inv_kurang_bayar_inv = 0;
        $inv_kurang_bayar_piutang = 0; 
        $siap_kirim_lpm = 0;
        $siap_kirim_wgt = 0;
        $siap_kirim_idr = 0;
        $inv_last_year_amt_total = 0; 
        $inv_last_year_total_inv = 0;
        $inv_last_year_amt_paid = 0;
        $inv_last_year_total_piutang = 0; 

        try{

            $r1 = DB::connection("sqlsrv4")
                    ->select(DB::raw("SELECT b.cust_id, count(a.order_id) as jml_order, 
                            cast(sum(a.wgt_sisa) as float) as wgt_outstanding FROM tracking_order a 
                            left join customer b on a.cust_id = b.cust_id
                            WHERE a.deliv_id = ' ' and a.order_id <> ' ' and b.cust_id = '$txtCustID'
                            group by b.cust_id"));

            foreach ($r1 as $r1) {

                $order_blm_kirim_jml = number_format($r1->jml_order,0,",",".")." Orders";
                $order_blm_kirim_wgt = number_format($r1->wgt_outstanding,2,",",".")." KG";

            }

            $r2 = DB::connection("sqlsrv4")
                    ->select(DB::raw("select b.cust_id, count(a.inv_id) as total_invoice, 
                    cast(sum(a.piutang) as float) as total_piutang from view_piutang a 
                    left outer join customer b on a.cust_id = b.cust_id 
                    where a.Piutang > 1 and b.cust_id = '$txtCustID'
                    GROUP BY b.cust_id"));

            foreach ($r2 as $r2) {

                $inv_kurang_bayar_inv = number_format($r2->total_invoice,0,",",".")." Inv";
                $inv_kurang_bayar_piutang = "IDR. ".number_format($r2->total_piutang/1000000,2,",",".");

            }

            $r3 = DB::connection("sqlsrv4")
                    ->select(DB::raw("select e.cust_id, count(a.lpm_id) as jml_lpm, sum(a.wgt) as wgt_lpm, cast(sum(a.wgt * c.unit_price * 1 * (100 - c.pct_disc) / 100 ) as float) as total
                    from lpm_item a join lpm_hdr b on a.lpm_id = b.lpm_id 
                    join order_item c on a.order_id = c.order_id and a.item_num = c.item_num
                    join order_mast d on c.order_id = d.order_id 
                    join customer e on d.cust_id = e.cust_id
                    where a.lpm_id like 'PPIC/LPM%' and b.deliv_id = ' ' and e.cust_id = '$txtCustID'
                    GROUP BY e.cust_id"));

            foreach ($r3 as $r3) {

                $siap_kirim_lpm = number_format($r3->jml_lpm,0,",",".")." LPM";
                $siap_kirim_wgt = number_format($r3->wgt_lpm,2,",",".")." KG";
                $siap_kirim_idr = "IDR. ".number_format($r3->total/1000000,2,",",".");

            }

            $r4 = DB::connection("sqlsrv4")
                    ->select(DB::raw("select cast(count(a.inv_id) as float) as total_inv, cast(sum(a.amt_total) as float) as amt_total, cast(sum(a.amt_paid) as float) as amt_paid,
                    cast(sum(a.Piutang) as float) as total_piutang from view_piutang a
                    left outer join customer b on a.cust_id = b.cust_id 
                    where b.cust_id = '$txtCustID' and a.dt_inv >= DATEADD(M, -12, GETDATE())
                    group by b.cust_id"));

            foreach ($r4 as $r4) {


                $inv_last_year_total_inv = number_format($r4->total_inv,0,",",".")." Inv";
                $inv_last_year_amt_total = "IDR. ".number_format($r4->amt_total/1000000,2,",",".");
                $inv_last_year_amt_paid = "IDR. ".number_format($r4->amt_paid/1000000,2,",",".");
                $inv_last_year_total_piutang = "IDR. ".number_format($r4->total_piutang/1000000,2,",",".");

            }

            $r5 = DB::connection("sqlsrv4")
                    ->select(DB::raw("select b.prod_code, trim(c.descr) as descr, cast(sum(b.wgt) as float) as wgt, cast(sum(b.amt_total) as float) as amt_total 
                    from inv_mast a inner join inv_item b on a.inv_id = b.inv_id
                    inner join prod_spec c on b.prod_code = c.prod_code
                    inner join customer d on a.cust_id = d.cust_id
                    where a.dt_inv >= DATEADD(M, -12, GETDATE()) and d.cust_id = '$txtCustID'
                    group by b.prod_code, c.descr "));

                    
            
            $data = ['order_blm_kirim_jml' => $order_blm_kirim_jml,
                    'order_blm_kirim_wgt' => $order_blm_kirim_wgt,
                    'inv_kurang_bayar_inv' => $inv_kurang_bayar_inv,
                    'inv_kurang_bayar_piutang' => $inv_kurang_bayar_piutang,
                    'siap_kirim_lpm' => $siap_kirim_lpm,
                    'siap_kirim_wgt' => $siap_kirim_wgt,
                    'siap_kirim_idr' => $siap_kirim_idr,
                    'inv_last_year_total_inv' => $inv_last_year_total_inv,
                    'inv_last_year_amt_total' => $inv_last_year_amt_total,
                    'inv_last_year_amt_paid' => $inv_last_year_amt_paid,
                    'inv_last_year_total_piutang' => $inv_last_year_total_piutang,
                    'list_prod_last_year' => $r5                    
            ];

            return response($data, 200);
          

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    public function getQuoteDetail (Request $request) {

        $quote_id = $request->quote_id;

        try{
    
            $data = DB::connection("sqlsrv4")
                    ->select(DB::raw("SELECT quote_item, ord_desc, wgt_quo, unit_price, amt_gross, amt_net, stat, req_ship_week
                    from quote_item where quote_id = '$quote_id'"));

            return response($data, 200);
          

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    public function getQuoteDetailConfirmed (Request $request) {

        $quote_id = $request->quote_id;

        try{
    
            $data = DB::connection("sqlsrv4")
                    ->select(DB::raw("SELECT quote_item, ord_desc, wgt_quo, unit_price, amt_gross, amt_net, stat, req_ship_week
                    from quote_item where quote_id = '$quote_id'"));

            return response($data, 200);
          

        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }

    }

    public function getOutstandingDeliv (Request $request) { 

        $custid = $request->custid;
        $salesid = $request->salesid;
        $privilege = $request->privilege;
        $dt_start = $request->dt_start;
        $dt_end = $request->dt_end;
        $where = "";

        if ($dt_start && !$dt_end) {

            $where.= " and a.dt_order = '$dt_start'";
        }

        if (!$dt_start && $dt_end) {

            $where.= " and a.dt_order = '$dt_end'";
        }

        if ($dt_start && $dt_end) {

            $where.= " and a.dt_order between '$dt_start' and '$dt_end'";
        }

        if ($custid) { 

            try{

                $data = DB::connection("sqlsrv4")
                        ->select(DB::raw("select ltrim(rtrim(a.order_id)) as order_id, ltrim(rtrim(a.cust_name)) as cust_name,
                        FORMAT(a.dt_order, 'dd MMM yyyy') as dt_order,
                        a.leat_time, a.descr, 
                        CONVERT(varchar(12), b.thick) as thick, 
                        CAST(b.width as float) as width, 
                        b.grade_id, b.coat_mass, 
                        CONVERT(varchar(12), a.wgt_ord) as wgt_ord, 
                        CONVERT(varchar(12), a.wgt_deliv) as wgt_deliv,
                        CASE
                            WHEN (a.wgt_ord - a.wgt_deliv) < 0 THEN  '('+CONVERT(varchar(12),((a.wgt_ord - a.wgt_deliv) * -1))+')'
                            ELSE CONVERT(varchar(12),(a.wgt_ord - a.wgt_deliv))
                        END as outstd,
                        CONVERT(varchar(12), a.wgt_lpm) as wgt_lpm, 
                        CASE
                            WHEN ((a.wgt_ord - a.wgt_deliv) - a.wgt_lpm) < 0 THEN '('+CONVERT(varchar(12),(((a.wgt_ord - a.wgt_deliv) - a.wgt_lpm) * -1))+')'
                            ELSE CONVERT(varchar(12),((a.wgt_ord - a.wgt_deliv) - a.wgt_lpm))
                        END as remain
                        from tracking_order a
                        inner join prod_spec b on a.prod_code = b.prod_code
                        where cust_id = '$custid' and order_id <> '' "." ".$where.""));
    
                //$response = ['data' => $data];
                return response($data, 200);
    
            }
            catch(QueryException $ex){
    
                $error = $ex->getMessage();
                $response = ['message' => $error];
                return response($response, 422);
            }

        }

        if ($salesid) { 

            try{

                $data = DB::connection("sqlsrv4")
                        ->select(DB::raw("select ltrim(rtrim(a.order_id)) as order_id, ltrim(rtrim(a.cust_name)) as cust_name,
                        FORMAT(a.dt_order, 'dd MMM yyyy') as dt_order,
                        a.leat_time, a.descr, 
                        CONVERT(varchar(12), b.thick) as thick, 
                        CAST(b.width as float) as width, 
                        b.grade_id, b.coat_mass, 
                        CONVERT(varchar(12), a.wgt_ord) as wgt_ord, 
                        CONVERT(varchar(12), a.wgt_deliv) as wgt_deliv,
                        CASE
                            WHEN (a.wgt_ord - a.wgt_deliv) < 0 THEN  '('+CONVERT(varchar(12),((a.wgt_ord - a.wgt_deliv) * -1))+')'
                            ELSE CONVERT(varchar(12),(a.wgt_ord - a.wgt_deliv))
                        END as outstd,
                        CONVERT(varchar(12), a.wgt_lpm) as wgt_lpm, 
                        CASE
                            WHEN ((a.wgt_ord - a.wgt_deliv) - a.wgt_lpm) < 0 THEN '('+CONVERT(varchar(12),(((a.wgt_ord - a.wgt_deliv) - a.wgt_lpm) * -1))+')'
                            ELSE CONVERT(varchar(12),((a.wgt_ord - a.wgt_deliv) - a.wgt_lpm))
                        END as remain
                        from tracking_order a
                        inner join prod_spec b on a.prod_code = b.prod_code
                        inner join order_book_hdr c on a.book_id = c.book_id
                        where c.salesman_id = '$salesid' and a.order_id <> '' "." ".$where.""));
    
                //$response = ['data' => $data];
                return response($data, 200);
    
            }
            catch(QueryException $ex){
    
                $error = $ex->getMessage();
                $response = ['message' => $error];
                return response($response, 422);
            }


        }

        if ($privilege) {

            try{

                $data = DB::connection("sqlsrv4")
                        ->select(DB::raw("select ltrim(rtrim(a.order_id)) as order_id, ltrim(rtrim(a.cust_name)) as cust_name,
                        FORMAT(a.dt_order, 'dd MMM yyyy') as dt_order,
                        a.leat_time, a.descr, 
                        CONVERT(varchar(12), b.thick) as thick, 
                        CAST(b.width as float) as width, 
                        b.grade_id, b.coat_mass, 
                        CONVERT(varchar(12), a.wgt_ord) as wgt_ord, 
                        CONVERT(varchar(12), a.wgt_deliv) as wgt_deliv,
                        CASE
                            WHEN (a.wgt_ord - a.wgt_deliv) < 0 THEN  '('+CONVERT(varchar(12),((a.wgt_ord - a.wgt_deliv) * -1))+')'
                            ELSE CONVERT(varchar(12),(a.wgt_ord - a.wgt_deliv))
                        END as outstd,
                        CONVERT(varchar(12), a.wgt_lpm) as wgt_lpm, 
                        CASE
                            WHEN ((a.wgt_ord - a.wgt_deliv) - a.wgt_lpm) < 0 THEN '('+CONVERT(varchar(12),(((a.wgt_ord - a.wgt_deliv) - a.wgt_lpm) * -1))+')'
                            ELSE CONVERT(varchar(12),((a.wgt_ord - a.wgt_deliv) - a.wgt_lpm))
                        END as remain
                        from tracking_order a
                        inner join prod_spec b on a.prod_code = b.prod_code
                        where order_id <> '' "." ".$where.""));
    
                //$response = ['data' => $data];
                return response($data, 200);
    
            }
            catch(QueryException $ex){
    
                $error = $ex->getMessage();
                $response = ['message' => $error];
                return response($response, 422);
            }


        }

    }

    public function listEntity() {

        $result = DB::connection("sqlsrv3")
        ->select(DB::raw("select 
        case
            when custGroup = 'LAIN' then 'OTHER' 
            else custGroup
        end as entity from View_wh_perform
        group by custGroup"));

        return response()->json($result);
    }

    public function getListProduk(Request $request)
    {
        $sqlWhere = "1=1";

        if($request->txtCoilId != null)
        {
            $txtCoilId = $request->txtCoilId;
        }
        else
        {
            $txtCoilId = '';
        }

        if (!empty($txtCoilId))
        {
            $sqlWhere = "a.coil_id = ". "'" . $txtCoilId . "' and a.stat = 'S' and a.tr_type = 'SA'";
        }

        $RawMatsResult = DB::connection('sqlsrv4')
                                ->table('deliv_unit as a')
                                ->select('a.deliv_id','a.dt_modified', 'a.coil_id', 'a.wgt', 'd.unit_meas', 'd.descr as nama_produk', 'e.descr as category', 'f.brand_name as brand_coat_name', 'h.cust_name', 'i.waranty')
                                ->leftJoin('deliv_item as b', function($join){
                                    $join->on('b.deliv_id', '=', 'a.deliv_id')
                                         ->on('b.deliv_seq', '=', 'a.deliv_seq');
                                })
                                ->join('prod_spec as d', 'd.prod_code', '=', 'b.prod_code', 'left outer')
                                ->join('category as e', 'e.category_id', '=', 'd.category_id', 'left outer')
                                ->join('brand_coat as f', 'f.coat_mass', '=', 'd.coat_mass', 'left outer')
                                ->leftJoin('deliv_hdr as g', function($join){
                                    $join->on('g.deliv_id', '=', 'a.deliv_id')
                                         ->on('g.mill_id', '=', 'a.mill_id');
                                })
                                ->leftJoin('customer as h', function($join){
                                    $join->on('h.cust_id', '=', 'g.cust_id')
                                         ->on('h.mill_id', '=', 'g.mill_id');
                                })
                                ->leftJoin('mst_waranty as i', function($join){
                                    $join->on('i.color_id', '=', 'd.color_id')
                                         ->on('i.coat_mass', '=', 'd.coat_mass');
                                })
                                ->whereRaw($sqlWhere)
                                ->orderBy('a.dt_modified', 'desc')
                                ->get();
                                
        return response($RawMatsResult, 200);
        
    }

    public function getCoilID(Request $request)
    {
        $sqlWhere = "1=1";

        if($request->coil_id != null)
        {
            $coil_id = $request->coil_id;
        }
        else
        {
            $coil_id = '';
        }

        $RawMatsResult = DB::connection('sqlsrv4')
                                ->select(DB::raw("select top 1 * from view_waranty where coil_id = '$coil_id'"));
                                
        return response($RawMatsResult, 200);
    }

    public function getListInvPiutangCust(Request $request)
    {
        try{
            $txtCustID = $request->custid;

            $data = DB::connection("sqlsrv4")
                                    ->select(DB::raw("select a.inv_id, a.order_id, a.dt_due, (a.amt_total - amt_paid - amt_retur) as amt_owe 
                                    from inv_mast a 
                                    JOIN order_mast b ON a.mill_id = b.mill_id AND a.order_id = b.order_id 
                                    left outer join customer c ON a.mill_id = c.mill_id and b.cust_id = c.cust_id 
                                    where a.mill_id = 'SR' AND c.cust_id = '$txtCustID' AND paid_flag <> 'Y' and (a.amt_total - amt_paid - amt_retur)
                                    <> 0 ORDER BY a.dt_due "));

            return response($data, 200);
        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }
    }

    public function getListGiroCust(Request $request)
    {
        try{
            $txtCustID = $request->custid;

            $data = DB::connection("sqlsrv4")
                                ->select(DB::raw("
                                SELECT a.giro_num, b.bank_name, dt_giro, (giro_amt - used_amt) as giro_amt FROM giro_rcv a 
                                join bank_mast b on a.bank_id = b.bank_id WHERE cust_id = '$txtCustID' and (giro_amt - used_amt) > 0 and stat <> 'C' and a.clear_flag not in ('K', 'R') order by dt_giro"));

            return response($data, 200);
        }
        catch(QueryException $ex){

            $error = $ex->getMessage();
            $response = ['message' => $error];
            return response($response, 422);
        }
    }


    public function getCustSvrKMB(Request $request)
    {
        $search = $request->get('term');

        if ($request->sales_id) {
            $whereSql = [
                ['SalesId', '=', $request->sales_id]
            ];
        } else {
            $whereSql = null;
        }

        $result = DB::connection('sqlsrv6')
                    ->table('customer')
                    ->Where('NamaCustomer', 'LIKE', '%'. $search. '%')
					->where('active_flag', '=', 'Y')
                    ->where($whereSql)
                    ->orWhere("CustomerId", "=",  $search  )
                    ->take(25)
                    ->get();

        return response()->json($result);
    }

    public function getSalesSvrKMB(Request $request)
    {
        $search = $request->get('term');

        if ($request->sales_id) {
            $whereSql = [
                ['SalesId', '=', $request->sales_id]
            ];
        } else {
            $whereSql = null;
        }

        $result = DB::connection('sqlsrv6')
                    ->table('sales')
                    ->Where('NamaSales', 'LIKE', '%'. $search. '%')
					->where('active_flag', '=', 'Y')
                    ->where($whereSql)
                    ->orWhere("SalesId", "=",  $search  )
                    ->take(25)
                    ->get();

        return response()->json($result);
    }

    public function getCustSvrVivo(Request $request)
    {
        $search = $request->get('term');

        if ($request->sales_id) {
            $whereSql = [
                ['SalesId', '=', $request->sales_id]
            ];
        } else {
            $whereSql = null;
        }

        $result = DB::connection('sqlsrv3')
                    ->table('Customer')
                    ->Where('NamaCustomer', 'LIKE', '%'. $search. '%')
					->where('active_flag', '=', 'Y')
					->where('cv_id', '=', '00')
                    ->where($whereSql)
                    ->orWhere("CustomerId", "=",  $search  )
                    ->take(25)
                    ->get();

        return response()->json($result);
    }

    public function getSalesSvrVivo(Request $request)
    {
        $search = $request->get('term');

        if ($request->sales_id) {
            $whereSql = [
                ['SalesId', '=', $request->sales_id]
            ];
        } else {
            $whereSql = null;
        }

        $result = DB::connection('sqlsrv3')
                    ->table('Sales')
                    ->Where('NamaSales', 'LIKE', '%'. $search. '%')
					->where('active_flag', '=', 'Y')
					->where('office_id', '>', 50)
                    ->where($whereSql)
                    ->orWhere("SalesId", "=",  $search  )
                    ->orWhere("SalesId", "=",  'ARIF BUDIMAN')
                    ->take(25)
                    ->get();

        return response()->json($result);
    }

    public function getOrderSvrVivo(Request $request)
    {
        $url = 'https://preorder.bajaringanvivo.co.id/api/orders?sales_id=' . $request->sales_id . '&office_id=' . $request->office_id . '&cust_id=' . $request->cust_id . '&order_id=' . $request->order_id . '&start_date=' . $request->start_date . '&end_date=' . $request->end_date;

		$datas = $this->sendRequest('GET', $url, null);

        return response()->json($datas);
    }

    protected function sendRequest($option, $url, $data = null)
	{
        $client = new Client;

		$result = $client->request($option, $url, [
			'headers' => [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json'
			],
			'json' => $data
		]);

        return json_decode($result->getBody(), true);
	}
}
