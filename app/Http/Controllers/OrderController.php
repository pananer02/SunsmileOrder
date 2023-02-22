<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Distrbution;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\DistrbutionEdit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        $order = DB::table('customers')
            ->select('orders.id', 'orders.OrderDate', 'orders.ShipDate', 'orders.DepotRoute', 'orders.EmployeeRoute', 'orders.Status', 'orders.CustomerName','orders.TimeCreate')
            ->DISTINCT()
            ->join('orders', 'customers.id', '=', 'orders.IDCustomer')
            ->join('channel_admins', 'channel_admins.channel', '=', 'customers.ChannelCode')
            ->where('channel_admins.employee', Auth::user()->name)
            ->get();

        if ($order == '[]') {
            $order = DB::table('orders')
                ->select('orders.id', 'orders.OrderDate', 'orders.ShipDate', 'orders.DepotRoute', 'orders.EmployeeRoute', 'orders.Status', 'orders.CustomerName','orders.TimeCreate')
                ->join('customers', 'customers.id', '=', 'orders.IDCustomer')
                ->get();
        }
        $depots = DB::table('depots')
            ->get();
        $depot = null;
        $channel = DB::table('channel_admins')
            ->where('channel_admins.employee', Auth::user()->name)
            ->get();
        return view('order.index', compact('order', 'depots', 'depot', 'channel'));
    }
    public function search4(Request $request)
    {
        $order = DB::table('orders')
            ->select('orders.id', 'orders.OrderDate', 'orders.ShipDate', 'orders.DepotRoute', 'orders.EmployeeRoute', 'orders.Status', 'orders.CustomerName','orders.TimeCreate')
            ->DISTINCT()
            ->join('customers', 'customers.id', '=', 'orders.IDCustomer')
            ->where(function ($queue) use ($request) {
                $queue->where('customers.Depot', '=', $request->Depot)
                    ->orwhere('customers.queue', '=', $request->queue)
                    ->orwhere('orders.OrderDate', '=', $request->BuyDate)
                    ->orwhere('orders.Schedule', '=', $request->LoadDate)
                    ->orwhere('orders.ShipDate', '=', $request->ShipDate);
            })
            ->get();
        $depots = DB::table('depots')
            ->get();
        $depot = $request->Depot;
        $channel = DB::table('channel_admins')
            ->where('channel_admins.employee', Auth::user()->name)
            ->get();
        return view('order.index', compact('order', 'depots', 'depot', 'channel'));
    }
    public function indexs(Request $request, $name)
    {
        $data['order'] = Order::orderBy('id', 'asc')
            ->where('CustomerName', $name)
            ->get();
        return view('order.indexs', $data);
    }

    public function edit(Order $order)
    {
        $order_details = DB::table('order_details')
            ->where('order_details.IDOrder', $order->id)
            ->get();
        $orders = $order->Status;
        return view('orderdetail.index', compact('order', 'order_details', 'orders'));
    }
    public function editO(Request $request, $orderid)
    {
        $admin = DB::table('employees')
            ->get();
        $order = DB::table(('orders'))
            ->select('orders.id', 'orders.OrderDate', 'orders.ShipDate', 'customers.Depot', 'customers.DepotRoute', 'customers.EmployeeRoute', 'orders.Status', 'orders.IDCustomer')
            ->join('customers', 'customers.id', '=', 'orders.IDCustomer')
            ->where('orders.id', $orderid)
            ->get();
        $depotroute = DB::table(('depot_routes'))
            ->select('depot_routes.id', 'depot_routes.RoutesName')
            ->join('depots', 'depots.id', '=', 'depot_routes.DepotID')
            ->where('depots.DepotName', $order[0]->Depot)
            ->get();
        return view('order.edit', compact('order', 'depotroute', 'admin'));
    }
    public function submitorder(Request $request, $orderid)
    {
        $order =  Order::find($orderid);
        $order->Status = "กระจายสินค้าแล้ว";
        $order->save();
        $order_details = DB::table('order_details')
            ->where('order_details.IDOrder', $orderid)
            ->get();
        foreach ($order_details as $orders) {
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
            }
        }
        $data['order'] = Order::orderBy('id', 'asc')->get();
        return redirect()->route('order.index')->with('success', 'ยืนยันการกระจายเรียบร้อย~');
    }
    public function editorder(Request $request, $order, $name, $status)
    {
        $order_details = DB::table('order_details')
            ->where('order_details.IDOrder', $order)
            ->orderByDesc('id')
            ->get();
        $queue = DB::table('orders')
            ->where('orders.id', $order)
            ->get();
        return view('orderdetail.indexorder', compact('order', 'order_details', 'name', 'status', 'queue'));
    }
    public function destroy(Order $order)
    {

        $order->delete();
        OrderDetail::where('IDOrder', $order->id)->delete();
        return redirect()->route('order.index')->with('success', 'ลบข้อมูลการสั่งซื้อเรียบร้อย');
    }
    public function orderD(Request $request, $order)
    {
        $orders =  Order::find($order);
        $name = $orders->CustomerName;
        Order::where('id', $order)->delete();
        OrderDetail::where('IDOrder', $order)->delete();
        return redirect()->route('myorders', $name)->with('success', 'ลบข้อมูลการสั่งซื้อเรียบร้อย');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'DepotRoute' => 'required',
            'Employee' => 'required',
            'Schedule' => 'required',
            'CreateBy' => 'required',

        ]);
        $order =  Order::find($id);
        $order->Status = 'ยืนยันคำสั่งซื้อ';
        $order->UpdateBy = $request->CreateBy;
        $order->EmployeeRoute = $request->Employee;
        $order->DepotRoute = $request->DepotRoute;
        $order->Schedule = $request->Schedule;
        $order->save();
        $customer = Customers::find($request->CustomerName);
        $customer->EmployeeRoute = $request->Employee;
        $customer->DepotRoute = $request->DepotRoute;
        $customer->save();

        $orderDetail = DB::table('order_details')
            ->where('order_details.IDOrder', $id)
            ->get();

        foreach ($orderDetail as $orders) {
            $Distrbution = new Distrbution();

            $CustomerID = DB::table('users')
                ->where('users.name', $orders->CustomerName)
                ->get();
            $itemID = DB::table('items')
                ->where('items.ItemName', $orders->ItemName)
                ->get();

            foreach ($itemID as $itemIDs) {

                $Distrbution->ItemNumber = $itemIDs->ItemName;
                $Distrbution->ItemDescription = $itemIDs->ItemDescription;
            }
            foreach ($CustomerID as $CustomerID) {
                $Distrbution->CustomerNo = $CustomerID->UserID;
                $customers = DB::table('customers')
                    ->where('customers.id', $CustomerID->RoleID)
                    ->get();
                foreach ($customers as $customer) {
                    $Distrbution->Depot = $customer->Depot;
                    $Distrbution->Route = $customer->DepotRoute;
                    $Distrbution->Queue = $customer->queue;
                }
            }
            $Distrbution->ShipDate = $order->ShipDate;
            $Distrbution->Day_ShipDate = $order->Day_ShipDate;
            $Distrbution->CustomerName = $orders->CustomerName;
            $Distrbution->Qty = $orders->amount;
            $Distrbution->Uom = $orders->UOM;
            $Distrbution->CreateBy = "System";
            $Distrbution->UpdateBy = "System";
            $Distrbution->save();
        }
        foreach ($orderDetail as $orders) {
            $Distrbution = new DistrbutionEdit();

            $CustomerID = DB::table('users')
                ->where('users.name', $orders->CustomerName)
                ->get();

            $itemID = DB::table('items')
                ->where('items.ItemName', $orders->ItemName)
                ->get();
            foreach ($itemID as $itemIDs) {
                $Distrbution->ItemNumber = $itemIDs->ItemName;
                $Distrbution->ItemDescription = $itemIDs->ItemDescription;
            }
            foreach ($CustomerID as $CustomerID) {
                $Distrbution->CustomerNo = $CustomerID->UserID;
                $customers = DB::table('customers')
                    ->where('customers.id', $CustomerID->RoleID)
                    ->get();
                foreach ($customers as $customer) {
                    $Distrbution->Depot = $customer->Depot;
                    $Distrbution->Route = $customer->DepotRoute;
                    $Distrbution->Queue = $customer->queue;
                }
            }
            $Distrbution->IDorderDetail = $orders->id;
            $Distrbution->ShipDate = $order->ShipDate;
            $Distrbution->Day_ShipDate = $order->Day_ShipDate;
            $Distrbution->CustomerName = $orders->CustomerName;
            $Distrbution->Employee = $request->Employee;
            $Distrbution->Qty = $orders->amount;
            $Distrbution->Uom = $orders->UOM;
            $Distrbution->CreateBy = "System";
            $Distrbution->UpdateBy = "System";
            $Distrbution->save();
        }
        return redirect()->route('order.index')->with('success', 'ยืนยันคำสั่งซื้อออเดอร์นี้เรียบร้อย');
    }
}
