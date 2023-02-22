<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ItemsController extends Controller
{

    public function index()
    {

        $item = DB::table('items')
            ->select('items.id','items.ItemName','items.ItemDescription','items.PrimaryUOM','items.SecondaryUOM','items.image')
            ->join('detail_sales', 'detail_sales.IDItem', '=', 'items.ItemName')
            ->DISTINCT()
            // ->groupBy('items.id','items.ItemName','items.ItemDescription','items.PrimaryUOM','items.SecondaryUOM','items.image')
            ->get();
        return view('items.index', compact('item'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ItemName' => 'required',
            'ItemDescription' => 'required',
            'PrimaryUOM' => 'required',
            'SecondaryUOM' => '',
            'Category' => '',
            'Status' => 'required',
            'CreateBy' => 'required',
        ]);

        $item = new Item();
        $item->ItemName = $request->ItemName;
        $item->ItemDescription = $request->ItemDescription;
        $item->PrimaryUOM = $request->PrimaryUOM;
        $item->SecondaryUOM = $request->SecondaryUOM;
        $item->Category = $request->Category;
        $item->Status = $request->Status;
        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = time() . $file->getClientOriginalName();
            $file->move('public/Image', $filename);
            $item->image = $filename;
        }
        $item->CreateBy = $request->CreateBy;
        $item->UpdateBy = $request->CreateBy;


        $item->save();
        return redirect()->route('items.index')->with('success', 'เพิ่มข้อมูลสินค้าเรียบร้อย');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ItemName' => 'required',
            'ItemDescription' => 'required',
            'PrimaryUOM' => 'required',
            'SecondaryUOM' => 'required',
            'Category' => 'required',
            'Status' => 'required',
            'UpdateBy' => 'required',
        ]);

        $item = Item::find($id);
        $item->ItemName = $request->ItemName;
        $item->ItemDescription = $request->ItemDescription;
        $item->PrimaryUOM = $request->PrimaryUOM;
        $item->SecondaryUOM = $request->SecondaryUOM;
        $item->Category = $request->Category;
        $item->Status = $request->Status;

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = time() . $file->getClientOriginalName();
            $file->move('public/Image', $filename);
            $item->image = $filename;
        }
        $item->UpdateBy = $request->UpdateBy;

        $item->save();
        return redirect()->route('items.index')->with('success', 'แก้ไขข้อมูลสินค้าเรียบร้อย');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'ลบข้อมูลสินค้าเรียบร้อย');
    }
}
