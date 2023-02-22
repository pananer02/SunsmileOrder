<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $data['users'] = User::orderBy('id', 'asc')->get();
        return view('users.index', $data);
    }
    public function create()
    {
        return view('users.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'Role' => 'required',
            'UserID' => 'required',
        ]);
        $check = FALSE;
        $customers = DB::table(('customers'))
            ->get();
            $admin = DB::table('employees')
                ->get();
        if ($request->Role == "Admin") {
            foreach ($admin as $row) {
                if ($row->id == $request->UserID) {
                    $check = TRUE;
                }
            }
        } else {
            foreach ($customers as $row) {
                if ($row->id == $request->UserID) {
                    $check = TRUE;
                }
            }
        }
        if ($check == FALSE) {
            return redirect()->route('users.create')->with('success', 'ข้อมูลผู้ใช้งานไม่ตรงกับระบบ กรุณาใส่ใหม่');
        }
        $users = new User();
        $users->email = $request->email;
        $users->password = Hash::make($request->password);
        $users->Role = $request->Role;

        if ($request->Role == "Admin") {
            $query = DB::table('employees')
                ->where('employees.id', $request->UserID)
                ->get();
            foreach ($query as $row) {
                $users->name = $row->EmployeeName;
                $users->RoleID = $request->UserID;
                $users->UserID = $row->EmployeeCode;
            }
        } else {
            $query = DB::table('customers')
                ->where('customers.id', $request->UserID)
                ->get();
            foreach ($query as $row) {
                $users->name = $row->AccountDescription;
                $users->RoleID = $request->UserID;
                $users->UserID = $row->AccountNumber;
            }
        }
        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = time() . $file->getClientOriginalName();
            $file->move('public/Image', $filename);
            $users->image = $filename;
        }
        $users->save();

        return redirect()->route('users.index')->with('success', 'users has been created successfully');
    }
    public function edit(User $user)
    {

        return view('users.edit', compact('user'));
    }

    public function destroy(Request $request, User $users)
    {
        User::where('id', $request->id)->delete();
        return redirect()->route('users.index')->with('success', 'users has been delete successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'required',
            'Role' => 'required',
            'UserID' => 'required',
        ]);
        $check = FALSE;
        $customers = DB::table(('customers'))
            ->get();
        $admin = DB::table('employees')
            ->get();
        if ($request->Role == "Admin") {
            foreach ($admin as $row) {
                if ($row->id == $request->UserID) {
                    $check = TRUE;
                }
            }
        } else {
            foreach ($customers as $row) {
                if ($row->id == $request->UserID) {
                    $check = TRUE;
                }
            }
        }
        if ($check == FALSE) {
            return redirect()->route('users.edit', $id)->with('success', 'ข้อมูลผู้ใช้งานไม่ตรงกับระบบ กรุณาใส่ใหม่');
        }
        if ($request->Role == "Customer") {
            $query = DB::table('customers')
                ->where('customers.id', $request->UserID)
                ->get();

            $user = User::find($id);
            foreach ($query as $row) {

                $user->name = $row->AccountDescription;
                $user->RoleID = $request->UserID;
                $user->UserID = $row->AccountNumber;
            }
        } elseif ($request->Role == "Admin") {
            $query = DB::table('employees')
                ->where('employees.id', $request->UserID)
                ->get();
            $user = User::find($id);
            foreach ($query as $row) {
                $user->name = $row->EmployeeName;
                $user->RoleID = $request->UserID;
                $user->UserID = $row->EmployeeCode;
            }
        }
        if ($request->password != null) {
            $user->password = Hash::make($request->password);
        }
        $user->email = $request->email;
        $user->Role = $request->Role;
        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = time() . $file->getClientOriginalName();
            $file->move('public/Image', $filename);
            $user->image = $filename;
        }
        $user->save();
        return redirect()->route('users.index')->with('success', 'users has been Update successfully');
    }
    public function fetch(Request $request)
    {
        $id = $request->get('select');
        $result = array();
        if ($id == "Admin") {
            $query = DB::table('employees')
                ->get();
            $outputs = null;
            foreach ($query as $row) {
                $outputs .= '<option value="' . $row->id . '">' . $row->EmployeeCode . " | " . $row->EmployeeName . '</option>';
            }
            $output = '<datalist id="l">'
                . $outputs . '
            </datalist>';
            echo $output;
        } else {
            $query = DB::table('customers')
                ->get();
            $outputs = null;
            foreach ($query as $row) {
                $outputs .= '<option value="' . $row->id . '">' . $row->AccountNumber . " | " . $row->AccountDescription . " | " . $row->Depot . " | " . $row->DepotRoute . '</option>';
            }
            $output = '<datalist id="l">'
                . $outputs . '
            </datalist>';
            echo $output;
        }
    }
}
