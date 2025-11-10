<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaskCategory;
use Illuminate\Validation\Rule;

class TaskCategoryController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('pageItems');

        $query = TaskCategory::query();

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

        $total_count = TaskCategory::count();

        return view('masters.task-category.index', [

            'datas' => $datas,
            'selectedStatus' => $status,
            'search' => $search,
            'total_count' => $total_count,
            'serialNumberStart' => $serialNumberStart,
        ]);
    }

    public function get(Request $request, $id)
    {
        $data = TaskCategory::find($id);

        return response()->json($data);
    }

    public function save(Request $request)
    {
        $form_data = $request->validate([
            'name' => [
                'required', 'string', 'max:200',
                Rule::unique('task_categories')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'status' => 'required|boolean',
        ]);
        try {

            $task_category = new TaskCategory;
            $task_category->name = $request->input('name');
            $task_category->status = $request->input('status');
            $task_category->save();

            return response()->json(['message' => 'Task Category created successfully', 'data' => $task_category]);
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
                Rule::unique('task_categories')->whereNull('deleted_at')->ignore($id),
            ],
            'status' => 'required|boolean',
        ]);
        try {

            $task_category = TaskCategory::find($id);
            $task_category->name = $request->input('name');
            $task_category->status = $request->input('status');
            $task_category->save();
            return response()->json(['message' => 'Task Category updated successfully', 'data' => $task_category]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = TaskCategory::find($id)->delete();
            return response()->json(['message' => 'Task Category deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }
}

