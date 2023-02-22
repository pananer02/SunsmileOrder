<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

use function Psy\debug;

class EmployeesController extends Controller
{
  public function index()
  {
    $data['employees'] = Employee::orderBy('id', 'asc')->get();
    return view('employees.index', $data);
  }
  public function create()
  {
    return view('employees.create');
  }
  public function store(Request $request)
  {
    $request->validate([
      'EmployeeCode' => 'required',
      'name' => 'required',
      'CreateBy' => 'required',
      
    ]);

    $employees = new Employee();
    $employees->EmployeeCode = $request->EmployeeCode;
    $employees->EmployeeName = $request->name;
    $employees->Status = "พนักงาน";
    $employees->CreateBy = $request->CreateBy;
    $employees->UpdateBy = $request->CreateBy;


    $employees->save();
    return redirect()->route('employees.index')->with('success', 'เพิ่มข้อมูลพนักงานเรียบร้อย');
  }
  public function update(Request $request, $id)
  {
    $request->validate([
      'EmployeeCode' => 'required',
      'name' => 'required',
      'UpdateBy' => 'required',
    ]);

    $employees = Employee::find($id);
    $employees->EmployeeCode = $request->EmployeeCode;
    $employees->EmployeeName = $request->name;
    $employees->UpdateBy = $request->UpdateBy;


    $employees->save();
    return redirect()->route('employees.index')->with('success', 'แก้ไขข้อมูลพนักงานเรียบร้อย');
  }

  public function edit(Employee $employee)
  {
    return view('employees.edit', compact('employee'));
  }
  public function destroy(Employee $employee)
  {
    $employee->delete();
    return redirect()->route('employees.index')->with('success', 'ลบข้อมูลพนักงานเรียบร้อย');
  }
}
