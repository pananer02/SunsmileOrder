<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Distrbution;
use App\Models\DistrbutionEdit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{

    public function indexs(Request $request, $id)
    {
        $request->validate([
            'ShipDate' => 'required',
        ]);
        date_default_timezone_set('Asia/Bangkok');

        $customers = DB::table(('customers'))
            ->where('customers.id', $id)
            ->get();
        $Order =  new Order();
        $name = null;
        $Order->IDCustomer = $id;
        foreach ($customers as $row) {
            $Order->CustomerName = $row->AccountDescription;
            $name =  $row->AccountDescription;
        }
        $d = Date('d/m/Y');
        $l = null;
        $t = Date("H:i");
        if (Date('l') == 'Monday') {
            $l = 'วันจันทร์';
        }
        if (Date('l') == 'Tuesday') {
            $l = 'วันอังคาร';
        }
        if (Date('l') == 'Wednesday') {
            $l = 'วันพุธ';
        }
        if (Date('l') == 'Thursday') {
            $l = 'วันพฤหัสบดี';
        }
        if (Date('l') == 'Friday') {
            $l = 'วันศุกร์';
        }
        if (Date('l') == 'Saturday') {
            $l = 'วันเสาร์';
        }
        if (Date('l') == 'Sunday') {
            $l = 'วันอาทิตย์';
        }
        $day = "$l $d";
        $Order->Day_OrderDate =  $d;
        $Order->OrderDate = $day;
        $Order->ShipDate = $request->ShipDate;
        $Order->Day_ShipDate = $request->ShipDates;
        $Order->Status = "กำลังสั่งซื้อ";
        $Order->TimeCreate = $t;
        $Order->CreateBy = $name;
        $Order->UpdateBy = $name;
        $Order->save();
        $orderMaxs = DB::table('orders')
            ->max('id');
        $orderName = DB::table('list_item_customers')
            ->where('list_item_customers.IDCustomer', $id)
            ->get();
        foreach ($orderName as $order_detail) {
            $orderDetail = new OrderDetail();
            $orderDetail->IDOrder = $orderMaxs;
            $orderDetail->IDCustomer = $id;
            $orderDetail->CustomerName = $order_detail->CustomerName;
            $orderDetail->ItemName = $order_detail->ItemName;
            $item = DB::table('items')
                ->where('items.ItemName', $order_detail->ItemName)
                ->get();
            foreach ($item as $row) {
                $orderDetail->ItemDesciption = $row->ItemDescription;
            }
            $orderDetail->amount = $order_detail->amount;
            $orderDetail->UOM = $order_detail->UOM;
            $orderDetail->Status =  "default";
            $orderDetail->CreateBy = $name;
            $orderDetail->UpdateBy = $name;
            $orderDetail->save();
        }
        $order_details = DB::table('order_details')
            ->where('order_details.IDOrder', $orderMaxs)
            ->get();
        $order = DB::table('orders')
            ->where('orders.id', $orderMaxs)
            ->get();
        return view('cart.index', compact('order_details', 'order'));
    }

    public function submit(Request $request, $id)
    {
        date_default_timezone_set('Asia/Bangkok');
        $order =  Order::find($id);
        $order->Status = "สั่งซื้อเรียบร้อย";
        $d = Date('d/m/Y');
        $order->SubmitDate = "$d";

       
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        date_default_timezone_set("Asia/Bangkok");

        $sToken = "FoIEQtYEMKVsnNTfjNiJSQUi1qYKsaLmZOTdH8tvcQD";
        $sMessage = "มี Order ของ " . $order->CustomerName . " เข้ามาวันที่ " . Date('d-m-Y');


        $chOne = curl_init();
        curl_setopt($chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");
        curl_setopt($chOne, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($chOne, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($chOne, CURLOPT_POST, 1);
        curl_setopt($chOne, CURLOPT_POSTFIELDS, "message=" . $sMessage);
        $headers = array('Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $sToken . '',);
        curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($chOne, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($chOne);

        //Result error 
        if (curl_error($chOne)) {
            echo 'error:' . curl_error($chOne);
        } else {
            $result_ = json_decode($result, true);
            echo "status : " . $result_['status'];
            echo "message : " . $result_['message'];
        }
        curl_close($chOne);
        $order->save();
        return redirect()->route('home');
    }
    public function index()
    {
        $orderMaxs = DB::table('orders')
            ->max('id');
        $order_details = DB::table('order_details')
            ->where('order_details.IDOrder', $orderMaxs)
            ->orderByDesc('id')
            ->get();
        $order = DB::table('orders')
            ->where('orders.id', $orderMaxs)
            ->get();
        return view('cart.index', compact('order_details', 'order'));
    }
    public function edit(Request $request, $orderdetails)
    {
        $order = DB::table('order_details')
            ->where('order_details.id', $orderdetails)
            ->get();
        $item = DB::table('items')
            ->get();
        $items = null;
        foreach ($order as $row) {
            $items = DB::table('items')
                ->where('items.ItemDescription', $row->ItemName)
                ->get();
        }
        return view('cart.edit', compact('order', 'item', 'items'));
    }

    public function add(Request $request, $order)
    {
        $orders = DB::table('orders')
            ->select('orders.id', 'orders.CustomerName', 'orders.IDCustomer')
            ->where('orders.id',  $order)
            ->get();
        $user = DB::table('customers')
            ->where('customers.AccountNumber', Auth::user()->UserID)
            ->get();
        $item = DB::table('detail_sales')
            ->select('items.id', 'items.ItemName', 'items.ItemDescription', 'items.PrimaryUOM', 'items.image')
            ->join('items', 'detail_sales.IDItem', '=', 'items.ItemName')
            ->join('price_pages', 'detail_sales.PriceID', '=', 'price_pages.PriceID')
            ->where('price_pages.channel', $user[0]->ChannelCode)
            ->where('price_pages.depot', $user[0]->Depot)
            ->DISTINCT()
            ->get();
        return view('cart.create', compact('orders', 'item'));
    }
    public function adds(Request $request, $order) //ตัวแอดรอบ 2
    {
        $orders = DB::table('orders')
            ->select('orders.id', 'orders.CustomerName', 'orders.IDCustomer')
            ->where('orders.id',  $order)
            ->get();
        $user = DB::table('customers')
            ->where('customers.id', $orders[0]->IDCustomer)
            ->get();
        $item = DB::table('detail_sales')
            ->select('items.id', 'items.ItemName', 'items.ItemDescription', 'items.PrimaryUOM', 'items.image')
            ->join('items', 'detail_sales.IDItem', '=', 'items.ItemName')
            ->join('price_pages', 'detail_sales.PriceID', '=', 'price_pages.PriceID')
            ->where('price_pages.channel', $user[0]->ChannelCode)
            ->where('price_pages.depot', $user[0]->Depot)
            ->DISTINCT()
            ->get();
        return view('orderdetail.creates', compact('orders', 'item'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'ItemName' => 'required',
            'amount' => 'required',
            'CreateBy' => 'required',
        ]);
        $items = DB::table('items')
            ->where('items.ItemDescription', $request->ItemName)
            ->get();
        $OrderDetail =  OrderDetail::find($id);
        $OrderDetail->ItemDesciption = $items[0]->ItemDescription;
        $OrderDetail->ItemName = $items[0]->ItemName;
        $OrderDetail->amount = $request->amount;
        $OrderDetail->UpdateBy = $request->CreateBy;
        $OrderDetail->save();
        return redirect()->route('cart.index')->with('success', 'อัพเดพข้อมูลรายละเอียดการสั่งซื้อเรียบร้อย');
    }
    public function destroy(Request $request, $orderdetail)
    {
        DB::table('order_details')->where('id', $orderdetail)->delete();
        return redirect()->route('cart.index')->with('success', 'ลบรายการสั่งซื้อสินนี้เรียบร้อย');
    }
    public function store(Request $request) //กลับไปออเดอร์ของลูกค้า
    {
        $request->validate([
            'ItemName' => 'required',
            'IDOrder' => 'required',
            'IDCustomer' => 'required',
            'Status' => 'required',
            'CreateBy' => 'required',
        ]);
        $orderDetail = new OrderDetail();
        $orderDetail->IDOrder = $request->IDOrder;
        $orderDetail->IDCustomer = $request->IDCustomer;
        $queue = DB::table('customers')
            ->where('customers.id', $request->IDCustomer)
            ->get();
        foreach ($queue as $key) {
            $orderDetail->CustomerName = $key->AccountDescription;
        }
        $orderDetail->ItemDesciption = $request->ItemName;

        $orderDetail->amount = '0';
        $queue = DB::table('items')
            ->where('items.ItemDescription', $request->ItemName)
            ->get();
        foreach ($queue as $key) {
            $orderDetail->ItemName = $key->ItemName;
            $orderDetail->UOM = $key->PrimaryUOM;
        }
        $orderDetail->Status = $request->Status;
        $orderDetail->CreateBy = $request->CreateBy;
        $orderDetail->UpdateBy = $request->CreateBy;
        $orderDetail->save();
        return redirect()->route('cart.index')->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }
}
