<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depot;

use function Psy\debug;

class DepotsController extends Controller
{
  public function index()
  {
    $data['depot'] = Depot::orderBy('id', 'asc')->get();
    return view('depots.index', $data);
  }
  public function create()
  {
    return view('depots.create');
  }
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required',
      'DepotCode' => 'required',
      'Status' => 'required',
      'CreateBy' => 'required',

    ]);

    $depot = new Depot();
    $depot->DepotName = $request->name;
    $depot->DepotCode = $request->DepotCode;
    $depot->Status = $request->Status;
    $depot->CreateBy = $request->CreateBy;
    $depot->UpdateBy = $request->CreateBy;


    $depot->save();
    return redirect()->route('depots.index')->with('success', 'เพิ่ม Depot เรียบร้อย');
  }
  public function update(Request $request, $id)
  {
    $request->validate([
      'name' => 'required',
      'Status' => 'required',
      'UpdateBy' => 'required',
    ]);

    $depot = Depot::find($id);
    $depot->DepotName = $request->name;
    $depot->Status = $request->Status;
    $depot->UpdateBy = $request->UpdateBy;

    $depot->save();
    return redirect()->route('depots.index')->with('success', 'แก้ไข Depot เรียบร้อย');
  }

  public function edit(Depot $depot)
  {
    return view('depots.edit', compact('depot'));
  }
  public function destroy(Depot $depot)
  {
    $depot->delete();
    return redirect()->route('depots.index')->with('success', 'ลบ Depot เรียบร้อย');
  }
}
