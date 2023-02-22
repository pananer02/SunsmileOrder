<?php

namespace App\Http\Controllers;

use App\Models\Distrbution;
use App\Models\DistrbutionEdit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistrbutionCon extends Controller
{
    public function index()
    {
        $distrbution = DB::table('distrbutions')
            ->get();
        $depots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->get();
        $session = "BAD";
        $text = "";
        return view('distrbutions.index', compact('distrbution', 'depots', 'session', 'text'));
    }
    public function search(Request $request)
    {

        $distrbution = DB::table('distrbutions')
            ->where('distrbutions.ShipDate', $request->ShipDate)
            ->where('distrbutions.Depot', $request->Depot)
            ->get();
        $depots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->get();
        $session = "OK";
        $text = "ค้นหาวันที่ $request->ShipDate , สายส่ง $request->Depot";
        return view('distrbutions.index', compact('distrbution', 'depots', 'session', 'text'));
    }
    public function edit(Distrbution $distrbution)
    {
        $item = DB::table('items')
            ->select('items.id', 'items.ItemName')
            ->get();
        return view('distrbutions.edit', compact('distrbution', 'item'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'Depot' => 'required',
            'ShipDate' => 'required',
            'Route' => 'required',
            'CustomerNo' => 'required',
            'CustomerName' => 'required',
            'ItemNumber' => 'required',
            'Uom' => 'required',
            'UpdateBy' => 'required',
            'ItemDescription' => 'required',
            'Qty' => 'required'
        ]);
        $distrbution = new DistrbutionEdit;
        $distrbution->Depot = $request->Depot;
        $distrbution->ShipDate = $request->ShipDate;
        $distrbution->Route = $request->Route;
        $distrbution->CustomerNo = $request->CustomerNo;
        $distrbution->CustomerName = $request->CustomerName;
        $distrbution->ItemNumber = $request->ItemNumber;
        $distrbution->ItemDescription = $request->ItemDescription;
        $distrbution->Qty = $request->Qty;
        $distrbution->Uom = $request->Uom;
        $distrbution->CreateBy = $request->UpdateBy;
        $distrbution->UpdateBy = $request->UpdateBy;
        $distrbution->save();
        return redirect()->route('distrbutions.index')->with('success', 'Distrbution has been Add successfully');
    }
    public function destroy(Distrbution $distrbution)
    {
        DB::table('distrbution_edits')->where('id', $distrbution->id)->delete();
        $distrbution->delete();
        return redirect()->route('distrbutions.index')->with('success', 'Distrbution has been delete successfully');
    }
}
