<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\provinces;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{

    public function index()
    {
        $data['customers'] = Customers::orderBy('id', 'asc')->get();
        return view('customers.index', $data);
    }

    public function create()
    {
        $provinces = DB::table('provinces')
            ->select('provinces.id', 'provinces.name_th')
            ->get();
        $customer_grades = DB::table('customer_grades')
            ->select('customer_grades.id', 'customer_grades.Grade')
            ->get();
        $categories = DB::table('categories')
            ->select('categories.id', 'categories.Description')
            ->get();
        $channel_codes = DB::table('channel_codes')
            ->select('channel_codes.id', 'channel_codes.Description')
            ->get();
        $sub_channel_codes = DB::table('sub_channel_codes')
            ->select('sub_channel_codes.id', 'sub_channel_codes.Description')
            ->get();
        $sales_channels = DB::table('sales_channels')
            ->select('sales_channels.id', 'sales_channels.Description')
            ->get();
        $depots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->get();
        $depotroute = DB::table('depot_routes')
            ->select('depot_routes.id', 'depot_routes.RoutesName')
            ->get();

        return view('customers.create', compact(
            'provinces',
            'customer_grades',
            'categories',
            'channel_codes',
            'sub_channel_codes',
            'sales_channels',
            'depots',
            'depotroute'
        ));
    }

    public function edit(Customers $customer)
    {
        $admin = DB::table('employees')
            ->get();
        $provinces = DB::table('provinces')
            ->select('provinces.id', 'provinces.name_th')
            ->get();
        $Fprovinces = DB::table('provinces')
            ->select('provinces.id', 'provinces.name_th')
            ->where('provinces.name_th', $customer->Province)
            ->get();
        $customer_grades = DB::table('customer_grades')
            ->select('customer_grades.id', 'customer_grades.Grade')
            ->get();
        $categories = DB::table('categories')
            ->select('categories.id', 'categories.Description')
            ->get();

        $channel_codes = DB::table('channel_codes')
            ->select('channel_codes.id', 'channel_codes.Description')
            ->get();
        $sub_channel_codes = DB::table('sub_channel_codes')
            ->select('sub_channel_codes.id', 'sub_channel_codes.Description')
            ->get();
        $sales_channels = DB::table('sales_channels')
            ->select('sales_channels.id', 'sales_channels.Description')
            ->get();
        $depots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->get();
        $Fdepots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->where('depots.DepotName', $customer->Depot)
            ->get();
        $depotroute = DB::table('depot_routes')
            ->select('depot_routes.id', 'depot_routes.RoutesName')
            ->get();
        return view('customers.edit', compact(
            'customer',
            'provinces',
            'customer_grades',
            'categories',
            'channel_codes',
            'sub_channel_codes',
            'sales_channels',
            'Fprovinces',
            'depots',
            'depotroute',
            'Fdepots',
            'admin'
        ));
    }
    public function destroy(Customers $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'ลบข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
    public function fetch(Request $request)
    {
        $id = $request->get('select');
        $result = array();
        $query = DB::table('amphures')
            ->select('amphures.name_th')
            ->where('amphures.province_id', $id)
            ->groupBy('amphures.name_th')
            ->get();
        $output = '<option value="#">เลือกอำเภอ</option>';
        foreach ($query as $row) {
            $output .= '<option value="' . $row->name_th . '">' . $row->name_th . '</option>';
        }
        echo $output;
    }
    public function fetch1(Request $request)
    {
        $id = $request->get('select');
        $result = array();
        $query = DB::table('sub_channel_codes')
            ->select('sub_channel_codes.Description')
            ->where('sub_channel_codes.ID_channel', $id)
            ->groupBy('sub_channel_codes.Description')
            ->get();
        $output = '<option value="#">เลือก Sub Channel Codes</option>';
        foreach ($query as $row) {
            $output .= '<option value="' . $row->Description . '">' . $row->Description . '</option>';
        }
        echo $output;
    }
    public function fetch2(Request $request)
    {
        $id = $request->get('select');
        $result = array();
        $query = DB::table('depot_routes')
            ->select('depot_routes.RoutesName')
            ->where('depot_routes.DepotID', $id)
            ->groupBy('depot_routes.RoutesName')
            ->get();
        $output = '<option value="#">เลือกเส้นทางรถ</option>';
        foreach ($query as $row) {
            $output .= '<option value="' . $row->RoutesName . '">' . $row->RoutesName . '</option>';
        }
        echo $output;
    }
    //Store resource
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'Description' => 'required',
            'customer_grades' => ' required',
            'sales_channels' => 'required',
            'Ref' => ' ',
            'channel_codes' => ' required',
            'sub_channel_codes' => ' required',
            'Add1' => '',
            'Add2' => '',
            'Add3' => '',
            'Add4' => '',
            'province' => ' required',
            'amphures' => ' required',
            'Country' => ' ',
            'State' => ' ',
            'Pos' => ' ',
            'ven' => ' ',
            'Depot' => ' required',
            'DeRoute' => ' required',
            'categories' => ' required',
            'Territory' => ' ',
            'Status' => ' required',
            'CreateBy' => 'required',
        ]);

        $customer = new Customers();
        $customer->AccountNumber = $request->number;
        $customer->AccountDescription = $request->Description;
        $customer->CustomerGrade = $request->customer_grades;
        $customer->SalesChannel = $request->sales_channels;
        $customer->Reference = $request->Ref;
        $Fchannel_codes = DB::table('channel_codes')
            ->select('channel_codes.id', 'channel_codes.Description')
            ->where('channel_codes.Description', $request->channel_codes)
            ->get();
        foreach ($Fchannel_codes as $row) {
            $customer->ChannelCode = $row->Description;
        }

        $customer->SubChannelCode = $request->sub_channel_codes;
        $customer->AddressLine1 = $request->Add1;
        $customer->AddressLine2 = $request->Add2;
        $customer->AddressLine3 = $request->Add3;
        $customer->AddressLine4 = $request->Add4;
        $customer->City = $request->amphures;
        $customer->Country = $request->Country;
        $customer->State = $request->State;
        $Fprovinces = DB::table('provinces')
            ->select('provinces.id', 'provinces.name_th')
            ->where('provinces.id', $request->province)
            ->get();
        foreach ($Fprovinces as $provinces) {
            $customer->Province = $provinces->name_th;
        }
        $customer->PostalCode = $request->Pos;
        $customer->VendorCode = $request->ven;
        $Fdepots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->where('depots.id', $request->Depot)
            ->get();
        foreach ($Fdepots as $depot) {
            $customer->Depot = $depot->DepotName;
        }
        $customer->DepotRoute = $request->DeRoute;
        $customer->EmployeeRoute = $request->Employee;
        $customer->Category = $request->categories;
        $customer->Territory = $request->Territory;
        $customer->status = $request->Status;
        $customer->CreateBy = $request->CreateBy;
        $customer->UpdateBy = $request->CreateBy;

        $customer->save();
        return redirect()->route('customers.index')->with('success', 'สร้างข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'number' => 'required',
            'Description' => 'required',
            'customer_grades' => ' required',
            'sales_channels' => 'required',
            'Ref' => ' ',
            'channel_codes' => ' required',
            'sub_channel_codes' => ' required',
            'Add1' => '',
            'Add2' => '',
            'Add3' => '',
            'Add4' => '',
            'province' => ' required',
            'amphures' => ' required',
            'Country' => ' ',
            'State' => ' ',
            'Pos' => ' ',
            'ven' => ' ',
            'Depot' => ' required',
            'DeRoute' => ' required',
            'categories' => ' required',
            'Territory' => ' ',
            'Status' => ' required',
        ]);
        $customer = Customers::find($id);
        $customer->AccountNumber = $request->number;
        $customer->AccountDescription = $request->Description;
        $customer->CustomerGrade = $request->customer_grades;
        $Fchannel_codes = DB::table('channel_codes')
            ->select('channel_codes.id', 'channel_codes.Description')
            ->where('channel_codes.Description', $request->channel_codes)
            ->get();
        foreach ($Fchannel_codes as $row) {

            $customer->ChannelCode = $row->Description;
        }

        $customer->Reference = $request->Ref;
        $customer->SubChannelCode = $request->sub_channel_codes;
        $customer->AddressLine1 = $request->Add1;
        $customer->AddressLine2 = $request->Add2;
        $customer->AddressLine3 = $request->Add3;
        $customer->AddressLine4 = $request->Add4;
        $customer->City = $request->amphures;
        $customer->Country = $request->Country;
        $customer->State = $request->State;
        $Fprovinces = DB::table('provinces')
            ->select('provinces.id', 'provinces.name_th')
            ->where('provinces.id', $request->province)
            ->get();
        foreach ($Fprovinces as $provinces) {
            $customer->Province = $provinces->name_th;
        }
        $customer->PostalCode = $request->Pos;
        $customer->VendorCode = $request->ven;
        $Fdepots = DB::table('depots')
            ->select('depots.id', 'depots.DepotName')
            ->where('depots.id', $request->Depot)
            ->get();
        foreach ($Fdepots as $depot) {
            $customer->Depot = $depot->DepotName;
        }
        $customer->DepotRoute = $request->DeRoute;
        $customer->Category = $request->categories;
        $customer->Territory = $request->Territory;
        $customer->queue = $request->queue;
        $customer->status = $request->Status;
        $customer->UpdateBy = $request->UpdateBy;
        $customer->save();
        return redirect()->route('customers.index')->with('success', 'อัพเดพข้อมูลลูกค้าเรียบร้อย');
    }
}
