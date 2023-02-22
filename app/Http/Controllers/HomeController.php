<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orderall = DB::table('orders')
            ->where('orders.IDCustomer', Auth::user()->RoleID)
            ->count();
        $orderActive = DB::table('orders')
            ->where('orders.IDCustomer', Auth::user()->RoleID)
            ->where('orders.Status', 'กำลังสั่งซื้อ')
            ->count();
        $orderWait = DB::table('orders')
            ->where('orders.IDCustomer', Auth::user()->RoleID)
            ->Where('orders.Status', 'ยืนยันสั่งซื้อ')
            ->count();
        $orderSend = DB::table('orders')
            ->where('orders.IDCustomer', Auth::user()->RoleID)
            ->Where('orders.Status', 'กระจายสินค้าแล้ว')
            ->count();
        $announce = DB::table('announces')
            ->get();
        return view('admin.profile', compact('orderall', 'orderActive', 'orderWait', 'orderSend', 'announce'));
    }
    public function profile()
    {
        $orderall = DB::table('orders')
            ->where('orders.IDCustomer', Auth::user()->RoleID)
            ->count();
        $orderActive = DB::table('orders')
            ->where('orders.IDCustomer', Auth::user()->RoleID)
            ->where('orders.Status', 'กำลังสั่งซื้อ')
            ->count();
        $orderWait = DB::table('orders')
            ->where('orders.IDCustomer', Auth::user()->RoleID)
            ->Where('orders.Status', 'ยืนยันสั่งซื้อ')
            ->count();
        $orderSend = DB::table('orders')
            ->where('orders.IDCustomer', Auth::user()->RoleID)
            ->Where('orders.Status', 'กระจายเรียบร้อย')
            ->count();
        $announce = DB::table('announces')
            ->get();
        return view('admin.profile', compact('orderall', 'orderActive', 'orderWait', 'orderSend', 'announce'));
    }
    public function check()
    {
        $sites = DB::table('customer_sites')
            ->where('customer_sites.AccountNumber', Auth::user()->UserID)
            ->get();
        return view('cart.indexs', compact('sites'));
    }
}
