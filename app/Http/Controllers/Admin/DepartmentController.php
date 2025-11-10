<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Validation\Rule;


class DepartmentController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('pageItems');

        $query = Department::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($status === '1' || $status === '0') {
            $query->where('status', $status);
        }

        $datas = $query->orderBy('id', 'desc')->paginate($perPage);
        $currentPage = $datas->currentPage();
        $serialNumberStart = ($currentPage - 1) * $perPage + 1;

        $total_count = Department::count();



        return view('masters.department.index', [

            'datas' => $datas,
            'selectedStatus' => $status,
            'search' => $search,
            'total_count' => $total_count,
            'serialNumberStart' => $serialNumberStart,
        ]);
    }

    public function get(Request $request, $id)
    {
        $data = Department::find($id);

        return response()->json($data);
    }

    public function save(Request $request)
    {
        $form_data = $request->validate([
            'name' => [
                'required', 'string', 'max:200',
                Rule::unique('departments')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'status' => 'required|boolean',

        ]);
        try {

            $department = new Department;
            $department->name = $request->input('name');
            $department->status = $request->input('status');
            $department->save();

            return response()->json(['message' => 'Department created successfully', 'data' => $department]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }


    public function update(Request $request, $id)
    {
        $form_data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('departments')->whereNull('deleted_at')->ignore($id),
            ],
            'status' => 'required|boolean',

        ]);
        try {

            $department = Department::find($id);
            $department->name = $request->input('name');
            $department->status = $request->input('status');
            $department->save();
            return response()->json(['message' => 'Department updated successfully', 'data' => $department]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = Department::find($id)->delete();
            return response()->json(['message' => 'Department deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }
}
