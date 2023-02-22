<?php

namespace App\Http\Controllers;

use App\Models\ListItemCustomer;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListProductCustomerController extends Controller
{
    public function index()
    {
        $data['ListProductCustomer'] = ListItemCustomer::orderBy('id', 'asc')->get();
        return view('ListProductCustomer.index', $data);
    }
    public function edit(ListItemCustomer $ListProductCustomer)
    {
        $customers = DB::table('customers')
            ->get();
        $customerss = DB::table('customers')
            ->where('customers.AccountDescription', $ListProductCustomer->CustomerName)
            ->get();
        $item = DB::table('items')
            ->get();
        $items = DB::table('items')
            ->where('items.ItemDescription', $ListProductCustomer->ItemName)
            ->get();
        return view('ListProductCustomer.edit', compact('ListProductCustomer', 'customers', 'customerss', 'item', 'items'));
    }
    public function create()
    {
        $customers = DB::table('customers')
            ->get();
        $item = DB::table('items')
            ->get();
        return view('ListProductCustomer.create', compact('item', 'customers'));
    }
    public function destroy(ListItemCustomer $ListProductCustomer)
    {
        $ListProductCustomer->delete();
        return redirect()->route('ListProductCustomer.index')->with('success', 'ลบข้อมูลสินค้าพื้นฐานของลูกค้าเรียบร้อย');
    }
    public function store(Request $request)
    {
        $request->validate([
            'IDCustomer' => 'required',
            'ItemName' => 'required',
            'amount' => 'required',
            'uom' => 'required'
        ]);
        $item = DB::table('items')
            ->get();
        $customers = DB::table(('customers'))
            ->get();
        $check1 = FALSE;
        $check2 = FALSE;
        foreach ($item as $items) {
            if ($items->ItemDescription == $request->ItemName) {
                $check1 = TRUE;
            }
        }
        foreach ($customers as $row) {
            if ($row->id == $request->IDCustomer) {
                $check2 = TRUE;
            }
        }
        if ($check1 == FALSE) {
            return redirect()->route('ListProductCustomer.create')->with('success', 'ข้อมูลสินค้าผิดพลาด กรุณาใส่ใหม่....');
        }
        if ($check2 == FALSE) {
            return redirect()->route('ListProductCustomer.create')->with('success', 'ข้อมูลลูกค้าผิดพลาด กรุณาใส่ใหม่....');
        }
        $ListItemCustomer = new ListItemCustomer();
        $ListItemCustomer->IDCustomer = $request->IDCustomer;
        $customers = DB::table(('customers'))
            ->where('customers.id', $request->IDCustomer)
            ->get();
        foreach ($customers as $row) {
            $ListItemCustomer->CustomerName = $row->AccountDescription;
        }
        $item = DB::table('items')
            ->where('items.ItemDescription', $request->ItemName)
            ->get();
        foreach ($item as $items) {
            $ListItemCustomer->ItemName = $items->ItemName;
        }

        $ListItemCustomer->ItemDesciption = $request->ItemName;
        $ListItemCustomer->amount = $request->amount;
        $ListItemCustomer->UOM = $request->uom;
        $ListItemCustomer->save();
        return redirect()->route('ListProductCustomer.index')->with('success', 'เพิ่มข้อมูลสินค้าพื้นฐานของลูกค้าเรียบร้อย');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'IDCustomer' => 'required',
            'ItemName' => 'required',
            'amount' => 'required',

        ]);
        $ListItemCustomer =  ListItemCustomer::find($id);
        $ListItemCustomer->IDCustomer = $request->IDCustomer;
        $customers = DB::table(('customers'))
            ->where('customers.id', $request->IDCustomer)
            ->get();
        foreach ($customers as $row) {
            $ListItemCustomer->CustomerName = $row->AccountDescription;
        }
        $ListItemCustomer->ItemName = $request->ItemName;
        $ListItemCustomer->amount = $request->amount;
        $ListItemCustomer->save();
        return redirect()->route('ListProductCustomer.index')->with('success', 'แก้ไขข้อมูลสินค้าพื้นฐานของลูกค้าเรียบร้อย');
    }
}
