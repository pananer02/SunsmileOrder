<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepotRoute;
use Illuminate\Support\Facades\DB;

use function Psy\debug;

class DepotRoutesController extends Controller
{
  public function index()
  {
    $data['depotroutes'] = DepotRoute::orderBy('id', 'asc')->get();
    return view('depotroutes.index', $data);
  }
  public function create()
  {
    $depots = DB::table('depots')
      ->select('depots.id', 'depots.DepotName')
      ->get();
    return view('depotroutes.create', compact('depots'));
  }
  public function store(Request $request)
  {
    $request->validate([
      'Depot' => 'required',
      'name' => 'required',
      'Status' => 'required',
      'CreateBy' => 'required',
    ]);

    $depotroutes = new DepotRoute();
    $depotroutes->DepotID = $request->Depot;
    $depotroutes->RoutesName = $request->name;
    $depotroutes->Status = $request->Status;
    $depotroutes->CreateBy = $request->CreateBy;
    $depotroutes->UpdateBy = $request->CreateBy;


    $depotroutes->save();
    return redirect()->route('depotroutes.index')->with('success', 'เพิ่มเส้นทางส่งเรียบร้อย');
  }
  public function update(Request $request, $id)
  {
    $request->validate([
      'Depot' => 'required',
      'name' => 'required',
      'Status' => 'required',
      'UpdateBy' => 'required',
    ]);

    $depotroutes = DepotRoute::find($id);
    $depotroutes->DepotID = $request->Depot;
    $depotroutes->RoutesName = $request->name;
    $depotroutes->Status = $request->Status;
    $depotroutes->UpdateBy = $request->UpdateBy;


    $depotroutes->save();
    return redirect()->route('depotroutes.index')->with('success', 'อัพเดพเส้นทางส่งเรียบร้อย');
  }

  public function edit(DepotRoute $depotroute)
  {
    $depots = DB::table('depots')
      ->select('depots.id', 'depots.DepotName')
      ->get();
    $Fdepots = DB::table('depots')
      ->select('depots.id', 'depots.DepotName')
      ->where('depots.id', $depotroute->DepotID)
      ->get();
    return view('depotroutes.edit', compact('depotroute', 'depots', 'Fdepots'));
  }
  public function destroy(DepotRoute $depotroute)
  {
    $depotroute->delete();
    return redirect()->route('depotroutes.index')->with('success', 'ลบเส้นทางส่งเรียบร้อย');
  }
}
