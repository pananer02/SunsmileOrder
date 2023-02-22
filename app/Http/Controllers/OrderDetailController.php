<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderDetailController extends Controller
{
    public function index()
    {
        $data['order_details'] = OrderDetail::orderBy('id', 'asc')->get();
        return view('orderdetail.index', $data);
    }

    public function search5(Request $request)
    {
        $order_details = DB::table('orders')
            ->select('order_details.id', 'order_details.IDOrder', 'order_details.CustomerName', 'order_details.ItemDesciption', 'order_details.amount', 'order_details.UOM', 'order_details.Status')
            ->DISTINCT()
            ->join('customers', 'customers.id', '=', 'orders.IDCustomer')
            ->join('order_details', 'order_details.IDOrder', "=", 'orders.id')
            ->where('customers.Depot', $request->Depot)
            ->get();
        $depots = DB::table('depots')
            ->get();
        $order = DB::table('orders')
            ->get();
        $channel = DB::table('channel_admins')
            ->where('channel_admins.employee', Auth::user()->name)
            ->get();
        $depot = $request->Depot;
        return view('orderdetail.orderlist', compact('order_details', 'order', 'depots', 'depot', 'channel'));
    }
    public function orderlist() //หน้าแก้ไขทั้งหมด
    {
        $order_details = DB::table('order_details')
            ->get();
        $order = DB::table('orders')
            ->get();
        $depots = DB::table('depots')
            ->get();
        $depot = null;
        return view('orderdetail.orderlist', compact('order_details', 'order', 'depots', 'depot'));
    }

    public function edit(OrderDetail $orderdetail)
    {
        $Status = DB::table('orders')
            ->select('orders.Status')
            ->where('orders.id', $orderdetail->IDOrder)
            ->get();
        $items = DB::table('items')
            ->where('items.ItemDescription', $orderdetail->ItemName)
            ->get();

        $user = DB::table('customers')
            ->where('customers.id', $orderdetail->IDCustomer)
            ->get();
        $item = DB::table('detail_sales')
            ->select('items.id', 'items.ItemName', 'items.ItemDescription', 'items.PrimaryUOM', 'items.image')
            ->join('items', 'detail_sales.IDItem', '=', 'items.ItemName')
            ->join('price_pages', 'detail_sales.PriceID', '=', 'price_pages.PriceID')
            ->where('price_pages.channel', $user[0]->ChannelCode)
            ->where('price_pages.depot', $user[0]->Depot)
            ->DISTINCT()
            ->get();

        return view('orderdetail.edit', compact('orderdetail', 'item', 'items', 'Status'));
    }
    public function orderDeEdit($id, $idorder) //
    {
        $orderdetails = DB::table('order_details')
            ->where('order_details.id', $id)
            ->get();
        $item = DB::table('items')
            ->get();
        $Status = DB::table('orders')
            ->select('orders.Status')
            ->where('orders.id', $idorder)
            ->get();
        return view('orderdetail.edit2', compact('orderdetails', 'item', 'Status', 'id'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required',
            'Status' => 'required',
            'UpdateBy' => 'required'
        ]);
        $check2 = FALSE;
        $item = DB::table('items')
            ->get();
        foreach ($item as $items) {
            if ($items->ItemDescription == $request->ItemName) {
                $check2 = TRUE;
            }
        }

        $OrderDetail =  OrderDetail::find($id);
        if ($check2 == FALSE) {
            return redirect()->route('orderDeEdit', [$id, $OrderDetail->IDOrder])->with('success', 'ข้อมูลสินค้าผิดพลาด กรุณาใส่ใหม่....');
        }
        $queue = DB::table('items')
            ->where('items.ItemDescription', $request->ItemName)
            ->get();
        $OrderDetail->ItemDesciption = $request->ItemName;
        foreach ($queue as $key) {
            $OrderDetail->ItemName = $key->ItemName;
            $OrderDetail->UOM = $key->PrimaryUOM;
        }
        $OrderDetail->amount = $request->amount;
        $OrderDetail->UpdateBy = $request->UpdateBy;
        $id = $OrderDetail->IDOrder;
        $name = $request->UpdateBy;
        $OrderDetail->save();
        return redirect()->route('editorder', [$id, $name, $request->Status])->with('success', 'อัพเดพข้อมูลรายละเอียดการสั่งซื้อเรียบร้อย');
    }


    public function update2(Request $request, $id) //แก้ไขเสร้จกลับหน้า รายละเอียดทั้งหมด
    {
        $request->validate([
            'amount' => 'required',
            'ItemName' => 'required',
            'Status' => 'required',
            'UpdateBy' => 'required'
        ]);
        $check2 = FALSE;
        $item = DB::table('items')
            ->get();
        foreach ($item as $items) {
            if ($items->ItemDescription == $request->ItemName) {
                $check2 = TRUE;
            }
        }

        $OrderDetail =  OrderDetail::find($id);
        if ($check2 == FALSE) {
            return redirect()->route('orderDeEdit', [$id, $OrderDetail->IDOrder])->with('success', 'ข้อมูลสินค้าผิดพลาด กรุณาใส่ใหม่....');
        }
        $queue = DB::table('items')
            ->where('items.ItemDescription', $request->ItemName)
            ->get();
        $OrderDetail->ItemDesciption = $request->ItemName;
        foreach ($queue as $key) {
            $OrderDetail->ItemName = $key->ItemName;
            $OrderDetail->UOM = $key->PrimaryUOM;
        }
        $OrderDetail->amount = $request->amount;
        $OrderDetail->UpdateBy = $request->UpdateBy;
        $id = $OrderDetail->IDOrder;
        $name = $request->UpdateBy;
        $OrderDetail->save();
        return redirect()->route('orderlist')->with('success', 'อัพเดพข้อมูลรายละเอียดการสั่งซื้อเรียบร้อย');
    }

    public function destroy(Request $request, OrderDetail $orderdetail) //
    {
        $order = DB::table('orders')
            ->where('orders.id', $orderdetail->IDOrder)
            ->get();
        $orders = $request->Status;
        $orderdetail->delete();
        $order_details = DB::table('order_details')
            ->where('order_details.IDOrder', $orderdetail->IDOrder)
            ->get();
        return view('orderdetail.index', compact('order', 'order_details', 'orders'));
    }
    public function delete($id) //
    {
        DB::table('order_details')->where('id', $id)
            ->delete();
        return redirect()->route('orderlist');
    }

    public function deleteorder(Request $request, $orderdetail)
    {
        $OrderDetail =  OrderDetail::find($orderdetail);
        $id = $OrderDetail->IDOrder;
        $name = $OrderDetail->CreateBy;
        DB::table('order_details')->where('id', $orderdetail)->delete();
        return redirect()->route('editorder', [$id, $name, 'กำลังสั่งซื้อ'])->with('success', 'ลบรายการสั่งซื้อสินนี้เรียบร้อย');
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
        return redirect()->route('editorder', [$orderDetail->IDOrder, $request->CreateBy, 'กำลังสั่งซื้อ'])->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }
}
