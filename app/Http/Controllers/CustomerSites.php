<?php

namespace App\Http\Controllers;

use App\Models\CustomerSites as ModelsCustomerSites;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class CustomerSites extends Controller
{
    public function index()
    {
        $data['customer_sites'] = ModelsCustomerSites::orderBy('id', 'asc')->get();
        return view('customersites.index', $data);
    }
    public function create()
    {
        $customer = DB::table('customers')
            ->get();
        return view('customersites.create', compact('customer'));
    }
    public function edit()
    {
    }
    public function destroy(ModelsCustomerSites $request)
    {
        $request->delete();
        return redirect()->route('customersites.index')->with('success', 'ลบประกาศของลูกค้าเรียบร้อยแล้ว');
    }
    public function store(Request $request)
    {
        $customersites = new ModelsCustomerSites();
        $customersites->AccountNumber = $request->customer;
        $customersites->sites = $request->sites;
        $customersites->save();
        return redirect()->route('customersites.index')->with('success', 'สร้างข้อมูลสาขาของลูกค้าเรียบร้อยแล้ว');
    }
    public function update(Request $request, $id)
    {
    }
}
