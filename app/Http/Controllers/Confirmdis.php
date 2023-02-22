<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Distrbution;
use App\Models\DistrbutionEdit;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;

class Confirmdis extends Controller
{
    public function index()
    {
        $depots = DB::table('depots')
            ->get();
        $session = "BAD";
        return view('confirmdis.index', compact('depots', 'session'));
    }
    public function search6(Request $request)
    {
        $depots = DB::table('depots')
            ->get();
        $distrbution = DB::table('distrbution_edits')
            ->where('distrbution_edits.ShipDate', $request->ShipDate)
            ->where('distrbution_edits.Depot', $request->Depots)
            ->where('distrbution_edits.CreateBy', 'System')
            ->get();
        $ship = "$request->ShipDate";
        $depot = "$request->Depots";
        $session = "GOOD";
        return view('confirmdis.index', compact('depots', 'distrbution', 'session', 'ship', 'depot'));
    }
    public function submitall(Request $request)
    {
        $distrbution = DB::table('distrbution_edits')
            ->where('distrbution_edits.ShipDate', $request->ShipDate)
            ->where('distrbution_edits.Depot', $request->Depots)
            ->where('distrbution_edits.CreateBy', 'System')
            ->get();
        foreach ($distrbution as $row) {
            $orderDetail = DB::table('order_details')
                ->where('order_details.id', $row->IDorderDetail)
                ->get();
            foreach ($orderDetail as $orders) {
                $distrbution_edits = DB::table('distrbution_edits')
                    ->where('distrbution_edits.IDorderDetail', $orders->id)
                    ->get();
                foreach ($distrbution_edits as $edit) {

                    $test = OrderDetail::find($orders->id);
                    $test->ItemDesciption = $edit->ItemDescription;
                    $test->ItemName = $edit->ItemNumber;
                    $test->amount = $edit->Qty;
                    $test->Status = "กระจายสินค้าแล้ว";
                    $test->save();
                    $dis_edit = DistrbutionEdit::find($row->id);
                    $dis_edit->CreateBy = Auth::user()->name;
                    $dis_edit->save();
                }

                $orderDetail = DB::table('order_details')
                    ->where('order_details.IDOrder', $orders->IDOrder)
                    ->where('order_details.CreateBy','=', 'กระจายสินค้าแล้ว')
                    ->get();
                if ($orderDetail == '[]') {
                    $order = Order::find($orders->IDOrder);
                    $order->Status = "กระจายสินค้าแล้ว";
                    $order->save();
                }
            }
        }
        return redirect()->route('confirmdis.index')->with('success', 'อัพเดพข้อมูลรายละเอียดการสั่งซื้อเรียบร้อย');
    }
    public function submit1(Request $request, $id)
    {
        $orderDetail = DB::table('order_details')
            ->where('order_details.id', $id)
            ->get();
        foreach ($orderDetail as $orders) {
            $distrbution_edits = DB::table('distrbution_edits')
                ->where('distrbution_edits.IDorderDetail', $orders->id)
                ->get();
            foreach ($distrbution_edits as $edit) {

                $test = OrderDetail::find($orders->id);
                $test->ItemDesciption = $edit->ItemDescription;
                $test->ItemName = $edit->ItemNumber;
                $test->amount = $edit->Qty;
                $test->Status = "กระจายสินค้าแล้ว";
                $test->save();
                $dis_edit = DistrbutionEdit::find($orders->id);
                $dis_edit->CreateBy = Auth::user()->name;
                $dis_edit->save();
            }
        }
        return redirect()->route('confirmdis.index')->with('success', 'อัพเดพข้อมูลรายละเอียดการสั่งซื้อเรียบร้อย');
    }
}
