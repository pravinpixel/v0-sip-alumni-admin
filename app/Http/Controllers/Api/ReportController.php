<?php

namespace App\Http\Controllers\api;

use App\Exports\AssignedTasksExport;
use App\Exports\IalertExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\MyTasksExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function export(Request $request, $type)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $filters = [
            'search' => $request->input('search'),
            'bde_name' => $request->input('bde_name', []),
            'branch' => $request->input('branch', []),
            'invoice_number' => $request->input('invoice_number', []),
            'age' => $request->input('age', []),
            'value' => $request->input('value', []),
            'status' => $request->input('status', []),
            'follow_up' => $request->input('follow_up', []),
            'customer_name' => $request->input('customer_name', []),
            'sort_column' => $request->input('sort_column', 'id'),
            'sort_order' => $request->input('sort_order', 'desc'),
        ];

        $sortColumn = $request->input('sort_column', 'id');
        switch ($sortColumn) {
            case 'a_to_z':
                $sortColumn = 'name';
                $sortOrder = 'asc';
                break;
            case 'new_to_old' || 'new_task':
                $sortColumn = 'created_at';
                $sortOrder = 'desc';
                break;
            case 'old_to_new':
                $sortColumn = 'created_at';
                $sortOrder = 'asc';
                break;
            case 'mark_as_completed':
                $sortColumn = 'mark_as_completed';
                $sortOrder = 'desc';
                break;
            case 'due_date_change_request':
                $sortColumn = 'due_date_change_request';
                $sortOrder = 'desc';
                break;
            case 'z_to_a':
                $sortColumn = 'name';
                $sortOrder = 'desc';
                break;
            case 'last_update':
                $sortColumn = 'updated_at';
                $sortOrder = 'desc';
                break;
            default:
                $sortColumn = 'deadline';
                $sortOrder = 'asc';
                break;
        }
        $task_filters = [
            'search' => $request->input('search'),
            'priority_id' => $request->input('priority_id', []),
            'task_category_id' => $request->input('task_category_id', []),
            'mark_as_completed' => $request->input('mark_as_completed'),
            'due_date_change_request' => $request->input('due_date_change_request'),
            'deadline' => $request->input('deadline', []),
            'sort_column' => $sortColumn,
            'sort_order' => $sortOrder,
        ];


        if ($type == 'my_task') {
            return Excel::download(new MyTasksExport($task_filters), 'My Tasks Report.xlsx');
        } elseif ($type == 'assigned_task') {
            return Excel::download(new AssignedTasksExport($task_filters), 'Assigned Tasks Report.xlsx');
        } elseif ($type == 'ialert') {
            return Excel::download(new IalertExport($filters), 'Ialert Report.xlsx');
        } else {
            return $this->returnError('Invalid key provided.', 400);
        }
    }
}
