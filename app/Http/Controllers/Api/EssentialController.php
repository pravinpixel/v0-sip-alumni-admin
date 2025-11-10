<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Status;
use App\Models\TaskCategory;
use Illuminate\Support\Facades\DB;

class EssentialController extends Controller
{
    public function Essential(Request $request)
    {
        try {
            if (!isset($request->keys) || empty($request->keys)) {
                return response()->json(['message' => 'No keys provided'], 400);
            }

            if (!is_array($request->keys)) {
                $keyArray = explode(',', $request->keys);
            } else {
                $keyArray = $request->keys;
            }

            $data = [];
            $invalidKeys = [];

            foreach ($keyArray as $key) {
                switch ($key) {
                    case 'employee':
                        $employeeData = Employee::with('role','designation')->where(['status' => '1'])
                           // ->where('id', '!=', auth()->id())
                            ->select('id', 'first_name', 'last_name', 'email', 'role_id', 'profile_image','designation_id')
                            ->selectRaw("CONCAT(first_name, ' ', last_name) as name")
                            ->orderBy('name', 'asc')
                            ->get();
                        $data[$key] = $employeeData;
                        break;
                    case 'task-category':
                        $task_category = TaskCategory::where(['status' => '1'])->select('id', 'name')->get();
                        $data[$key] = $task_category;
                        break;
                    case 'status':
                        $status = Status::where(['type' => 'status'])->select('id', 'name')->get();
                        $data[$key] = $status;
                        break;
                    case 'priority':
                        $priorityOrder = ['High', 'Medium', 'Low'];
                        $priority = Status::where('type', 'priority')
                            ->whereIn('name', $priorityOrder)
                            ->orderByRaw("FIELD(name, 'High', 'Medium', 'Low')")
                            ->select('id', 'name')
                            ->get();
                        $data[$key] = $priority;
                        break;
                    default:
                        $invalidKeys[] = $key;
                        break;
                }
            }

            if (!empty($invalidKeys)) {
                return response()->json(['message' => 'Invalid keys provided: ' . implode(', ', $invalidKeys)], 400);
            }

            return $this->returnSuccess($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
