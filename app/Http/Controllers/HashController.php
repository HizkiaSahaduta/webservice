<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class HashController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function changeAll () {

        $user = DB::connection("sqlsrv3")
                ->table('sec_user')
                ->select('user_id2')
                ->pluck('user_id2');

        foreach($user as $user) {
            $password = DB::connection("sqlsrv3")
                            ->table('sec_user')
                            ->select('user_pass')
                            ->where('user_id2', '=', $user)
                            ->value('user_pass');

            $update = DB::connection("sqlsrv3")
                        ->table('sec_user')
                        ->where('user_id2', '=', $user)
                        ->update(['username' => $user, 'password' => Hash::make($password)]);
        }

        return view('home');
       

    }
}
