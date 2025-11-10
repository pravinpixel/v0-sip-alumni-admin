<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Designation;
use Illuminate\Validation\Rule;

class DesignationController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('pageItems');

        $query = Designation::query();

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

        $total_count = Designation::count();



        return view('masters.designation.index', [

            'datas' => $datas,
            'selectedStatus' => $status,
            'search' => $search,
            'total_count' => $total_count,
            'serialNumberStart' => $serialNumberStart,
        ]);
    }

    public function get(Request $request, $id)
    {
        $data = Designation::find($id);

        return response()->json($data);
    }

    public function save(Request $request)
    {
        $form_data = $request->validate([
            'name' => [
                'required', 'string', 'max:200',
                Rule::unique('designations')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'status' => 'required|boolean',

        ]);
        try {

            $designation = new Designation;
            $designation->name = $request->input('name');
            $designation->status = $request->input('status');
            $designation->save();

            return response()->json(['message' => 'Designation created successfully', 'data' => $designation]);
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
                Rule::unique('designations')->whereNull('deleted_at')->ignore($id),
            ],
            'status' => 'required|boolean',

        ]);
        try {

            $designation = Designation::find($id);
            $designation->name = $request->input('name');
            $designation->status = $request->input('status');
            $designation->save();
            return response()->json(['message' => 'Designation updated successfully', 'data' => $designation]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = Designation::find($id)->delete();
            return response()->json(['message' => 'Designation deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }
}
