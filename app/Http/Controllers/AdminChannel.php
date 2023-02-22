<?php

namespace App\Http\Controllers;

use App\Models\ChannelAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminChannel extends Controller
{
    public function index()
    {
        $data['channelA'] = ChannelAdmin::orderBy('id', 'asc')->get();
        return view('adminchannel.index', $data);
    }
    public function create()
    {
        $admin = DB::table('employees')
            ->select('employees.EmployeeCode', 'employees.EmployeeName')
            ->get();
        $channel_codes = DB::table('channel_codes')
            ->select('channel_codes.id', 'channel_codes.Description')
            ->get();
        return view('adminchannel.create', compact('channel_codes', 'admin'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'Employee' => 'required',
            'Channel' => 'required',
        ]);
        $check = FALSE;
        $admin = DB::table('employees')
            ->get();
        foreach ($admin as $row) {
            if ($row->EmployeeCode == $request->Employee) {
                $check = TRUE;
            }
        }
        if ($check == FALSE) {
            return redirect()->route('adminchannel.create')->with('success', 'ข้อมูลผู้ใช้งานไม่ตรงกับระบบ กรุณาใส่ใหม่');
        }
        $ChannelAdmin = new ChannelAdmin();
        foreach ($admin as $row) {
            if ($row->EmployeeCode == $request->Employee) {
                $ChannelAdmin->employee_code = $request->Employee;
                $ChannelAdmin->employee = $row->EmployeeName;
            }
        }
        $ChannelAdmin->channel = $request->Channel;
        $ChannelAdmin->CreateBy = $request->CreateBy;
        $ChannelAdmin->UpdateBy = $request->CreateBy;
        $ChannelAdmin->save();
        return redirect()->route('adminchannel.index')->with('success', 'เพิ่มข้อมูลการดูแล Channel เรียบร้อย ');
    }

    public function destroy($id)
    {
        ChannelAdmin::where('id', $id)->delete();
        return redirect()->route('adminchannel.index')->with('success', 'ลบข้อมูลการดูแล Channel เรียบร้อย');
    }
}
