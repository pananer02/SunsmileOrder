<?php

namespace App\Http\Controllers;

use App\Models\Distrbution;
use App\Models\DistrbutionEdit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EditDistrbution extends Controller
{
    public function index()
    {
        $distrbution = DB::table('distrbution_edits')
            ->get();
        $depots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->get();
        $session = "BAD";
        $text = "";
        return view('editDistrbution.index', compact('distrbution', 'depots', 'session', 'text'));
    }
    public function search2(Request $request)
    {
        $distrbution = DB::table('distrbution_edits')
            ->where('distrbution_edits.ShipDate', $request->ShipDate)
            ->where('distrbution_edits.Depot', $request->DepotName)
            ->get();
        $depots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->get();
        $depotroute = DB::table('depot_routes');
        $session = "OK";
        $text = "ค้นหาวันที่ $request->ShipDate , สายส่ง $request->DepotName";
        $ship = "$request->ShipDate";
        $depot = "$request->DepotName";
        $depotsR = DB::table('depot_routes')
            ->where('depot_routes.DepotID', $request->Depot)
            ->get();
        $employee = DB::table('employees')
            ->get();
        return view('editDistrbution.index', compact('distrbution', 'depots', 'session', 'text', 'ship', 'depot', 'depotsR', 'employee'));
    }

    public function fetch(Request $request)
    {
        $id = $request->get('select');
        $result = array();
        $query = DB::table('depots')
            ->select('depots.DepotName')
            ->where('depots.id', $id)
            ->groupBy('depots.DepotName')
            ->get();
        $output = '';
        foreach ($query as $row) {
            $output .= '<option value="' . $row->DepotName . '">' . $row->DepotName . '</option>';
        }
        echo $output;
    }
    public function transporter(Request $request)
    {
        $distrbution = DB::table('distrbution_edits')
            ->where('distrbution_edits.ShipDate', $request->ship)
            ->where('distrbution_edits.Depot', $request->depot)
            ->get();
        foreach ($distrbution as $orders) {
            $orders = DistrbutionEdit::find($orders->id);
            $orders->Route = $request->transporter;
            $orders->Employee = $request->Employee;
            $orders->UpdateBy = Auth::user()->name;
            $orders->save();
        }
        $distrbution = DB::table('distrbution_edits')
            ->get();
        $depots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->get();
        $session = "BAD";
        $text = "";

        return view('editDistrbution.index', compact('distrbution', 'depots', 'session', 'text'));
    }
    public function transporter2(Request $request, $id)
    {
        $distrbution = DB::table('distrbution_edits')
            ->where('distrbution_edits.ShipDate', $request->ship)
            ->where('distrbution_edits.Depot', $request->depot)
            ->get();
        foreach ($distrbution as $orders) {
            $orders = DistrbutionEdit::find($id);
            $orders->Route = $request->transporter;
            $orders->save();
        }
        $distrbution = DB::table('distrbution_edits')
            ->get();
        $depots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->get();
        $session = "BAD";
        $text = "";

        return view('editDistrbution.index', compact('distrbution', 'depots', 'session', 'text'));
    }
    public function editdis(Request $request, $id)
    {
        $item = DB::table('items')
            ->select('items.id', 'items.ItemName', 'items.ItemDescription')
            ->get();
        $distrbution = DB::table('distrbution_edits')
            ->where('distrbution_edits.id', $id)
            ->get();
        $employee = DB::table('employees')
            ->get();
        $depotsR = null;
        foreach ($distrbution as $key) {
            $depotsA = DB::table('depots')
                ->where('depots.DepotName', $key->Depot)
                ->get();
            foreach ($depotsA as $keys) {
                $depotsR = DB::table('depot_routes')
                    ->where('depot_routes.DepotID', $keys->id)
                    ->get();
            }
        }
        $depots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->get();
        return view('editDistrbution.edit', compact('distrbution', 'item', 'depotsR', 'depots','employee'));
    }
    public function updates(Request $request, $id)
    {

        $item = DB::table('items')
            ->where('items.ItemDescription', $request->ItemDescription)
            ->get();
        $item2 = DB::table('items')
            ->get();
        $check2 = FALSE;

        foreach ($item2 as $row) {
            if ($row->ItemDescription == $request->ItemDescription) {
                $check2 = TRUE;
            }
        }
        $distrbution = DistrbutionEdit::find($id);
        foreach ($item as $row) {
            $distrbution->ItemNumber = $row->ItemName;
        }
        if ($check2 == FALSE) {
            return redirect()->route('editdis', $id)->with('success', 'ข้อมูลสินค้าผิด');
        }
        $distrbution->Depot = $request->Depot;
        $distrbution->ShipDate = $request->ShipDate;
        $distrbution->Route = $request->transporter;
        $distrbution->CustomerNo = $request->CustomerNo;
        $distrbution->CustomerName = $request->CustomerName;
        $distrbution->ItemDescription = $request->ItemDescription;
        $distrbution->Qty = $request->Qty;
        $distrbution->Employee = $request->Employee;
        $distrbution->UpdateBy   = $request->UpdateBy;
        $distrbution->save();
        return redirect()->route('dis2.index')->with('success', 'แก้ไขการกระจายเรียบร้อย');
    }
    public function destroy(Request $request, $id)
    {
        DB::table('distrbution_edits')->where('id', $id)->delete();
        return redirect()->route('dis2.index')->with('success', 'Distrbution has been delete successfully');
    }
}
