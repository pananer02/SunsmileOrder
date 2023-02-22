<?php

namespace App\Http\Controllers;

use App\Imports\DetailSalesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PricePage;
use Maatwebsite\Excel\Facades\Excel;

class PricePages extends Controller
{
    private $test;

    public function __construct()
    {
        //blockio init
        $this->test = time();
    }
    public function index()
    {
        $pricepage = DB::table('price_pages')
            ->get();
        $depots = DB::table('depots')
            ->get();
        $channel_code = DB::table('channel_codes')
            ->get();
        $session = "BAD";
        return view('pricepages.index', compact('depots', 'channel_code', 'pricepage', 'session'));
    }
    public function indexprice($id)
    {
        $customer = DB::table('customers')
            ->where('customers.id', $id)
            ->get();
        $pricepage = null;
        foreach ($customer as $op) {
            $pricepage = DB::table('price_pages')
                ->where('price_pages.Status', 'Active')
                ->where('price_pages.channel', $op->ChannelCode)
                ->where('price_pages.depot', $op->Depot)
                ->get();
            if ($pricepage != null) {
                break;
            }
        }


        return view('pricepages.index1', compact('pricepage'));
    }
    public function open(Request $request, $id)
    {
        $pricepage = DB::table('price_pages')
            ->where('price_pages.id', $id)
            ->get();
        foreach ($pricepage as $row) {
            $price = PricePage::find($id);
            if ($row->Status == "Active")
                $price->Status = "InActive";
            else
                $price->Status = "Active";
            $price->save();
        }
        return redirect()->route('pricepages.index')->with('success', 'แก้ไขสถานะเรียบร้อย');
    }
    public function edit(Request $request)
    {
        $depot = $request->Depot;
        $depots = DB::table('depots')
            ->get();
        $channel = $request->channel_code;
        $channel_code = DB::table('channel_codes')
            ->get();
        $session = "BAD";
        return view('pricepages.index', compact('depots', 'channel_code', 'session', 'depot', 'channel'));
    }
    public function create()
    {
        $depots = DB::table('depots')
            ->get();
        $channel_code = DB::table('channel_codes')
            ->get();
        $session = "OK";
        return view('pricepages.create', compact('depots', 'channel_code',  'session'));
    }
    public function search3(Request $request)
    {
        $pricepage = DB::table('price_pages')
            ->where('price_pages.depot', $request->Depot)
            ->where('price_pages.channel', $request->channel_code)
            ->get();
        $depot = $request->Depot;
        $depots = DB::table('depots')
            ->get();
        $channel = $request->channel_code;
        $channel_code = DB::table('channel_codes')
            ->get();
        $session = "OK";
        return view('pricepages.index', compact('depots', 'channel_code', 'pricepage', 'session', 'depot', 'channel'));
    }
    public function store(Request $request)
    {
        if ($request->file('doc_file') == null && $request->file('doc_img') == null) {
            return redirect()->route('pricepages.create')->with('success', 'Upload ไฟล์ด้วย!!!');
        }
        if ($request->Depots == "#" || $request->channel == "#") {
            return redirect()->route('pricepages.create')->with('success', 'เลือก Depot หรือ Channel Code ก่อนอัพโหลด');
        }
        if ($request->file('doc_img')) {
            $image = $request->file('doc_img');
            $typeimg = strrchr($_FILES['doc_img']['name'], ".");
            $file = null;
            $typefile = null;
            $filename = null;
            if ($request->file('doc_file')) {
                $file = $request->file('doc_file');
                $typefile = strrchr($_FILES['doc_file']['name'], ".");
                if ($typefile == '.xls' || $typefile == '.xlsx') {
                    Excel::import(new DetailSalesImport, request()->file('doc_file'));
                    $filename = time() . $file->getClientOriginalName();
                    $file->move('public/pdf', $filename);
                } else {
                    return redirect()->route('pricepages.create')->with('success', 'คุณอัพโหลดไฟล์ไม่ถูกต้อง');
                }
            }
            if ($typeimg == '.png' || $typeimg == '.jpg' || $typeimg == '.pdf') {
                $filenames = time() . $image->getClientOriginalName();
                $image->move('public/pdf', $filenames);
                $price = new PricePage();
                $price->channel = $request->channel;
                $price->depot = $request->Depots;
                $price->PriceID =  $this->test;
                $price->name_file = $request->doc_name;
                $price->Status = "Active";
                $price->file = $filename;
                $price->image = $filenames;
                $price->save();
            } else {
                return redirect()->route('pricepages.create')->with('success', 'คุณอัพโหลดไฟล์ไม่ถูกต้อง');
            }
        } else {
            return redirect()->route('pricepages.create')->with('success', 'อัพโหลดใบประกาศราคาด้วย');
        }


        return redirect()->route('pricepages.index')->with('success', 'อัพโหลดไฟล์เอกสารสำเร็จ');
    }

    public function destroy(Request $request)
    {
        PricePage::where('id', $request->id)->delete();
        return redirect()->route('pricepages.index')->with('success', 'ลบหน้าประกาศราคาเรียบร้อย');
    }
}
