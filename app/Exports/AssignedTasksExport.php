<?php

namespace App\Exports;

use App\Models\Task;
use App\Models\TaskDueDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssignedTasksExport implements FromQuery, WithHeadings, WithMapping
{

    protected $filters;

    private $serialNumber = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {

        $search = $this->filters['search'] ?? null;
        $priority = $this->filters['priority_id'] ?? null;
        $task_category_id = $this->filters['task_category_id'] ?? null;
        $mark_as_completed = $this->filters['mark_as_completed'] ?? null;
        $due_date_change_request = $this->filters['due_date_change_request'] ?? null;
        $sort_column = $this->filters['sort_column'] ?? 'id';
        $sort_order = $this->filters['sort_order'] ?? 'desc';
        $deadline = $this->filters['deadline'] ?? null;


        $query = Task::query()
            ->where('assigned_by', Auth::user()->id)
            ->whereNotIn('status_id', [1, 3, 8, 9])
            ->with('assignedto', 'dueDates', 'documents', 'status', 'assignedToEmployee.branchLocation')
            ->with(['assignedToEmployee' => function ($query) {
                $query->select('id', 'first_name', 'last_name', 'email', 'profile_image', 'employee_id', 'branch_id', 'location_id')
                    ->selectRaw("CONCAT(first_name, ' ', last_name) as name");
            }])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('task_no', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%')
                        ->orWhereHas('assignedto', function ($q) use ($search) {
                            $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%');
                        });;
                });
            })
            ->when($priority, function ($query) use ($priority) {
                $query->whereIn('priority_id', $priority);
            })->when($task_category_id, function ($query) use ($task_category_id) {
                $query->whereIn('task_category_id', $task_category_id);
            })
            ->when($mark_as_completed, function ($query) use ($mark_as_completed) {
                $query->where('mark_as_completed', $mark_as_completed);
            })
            ->when($due_date_change_request, function ($query) use ($due_date_change_request) {
                $query->where('due_date_change_request', $due_date_change_request);
            })
            ->when($deadline, function ($query) use ($deadline) {
                $this->applyDeadlineFilter($query, $deadline);
            })
            ->where(function ($query) {
                $query->where('is_recurrence', 0)
                    ->whereNull('parent_id')
                    ->orWhere(function ($query) {
                        $query->whereNotNull('parent_id')
                            ->where('is_recurrence', 1);
                    });
            })
            ->select('id', 'task_no', 'name', 'description', 'task_category_id', 'priority_id', 'assigned_to', 'created_at', 'additional_followers', 'deadline', 'is_recurrence', 'status_id', 'followers', 'mark_as_completed_date')
            ->orderBy($sort_column, $sort_order);

        if ($sort_column == 'mark_as_completed') {
            $sort_column = 'deadline';
            $sort_order = 'asc';
        }

        return $query->orderBy($sort_column, $sort_order);

    }

    public function headings(): array
    {
        return [
            'S.No',
            'Branch',
            'Employee ID',
            'Assigned To',
            'Creation Date',
            'Task ID',
            'Task Subject',
            'Task Details',
            'Task Type',
            'Task Priority',
            'Task Followers',
            'Task Additional Followers',
            'Task Due Date',
            'Task Status',
            'Task Recurrence',
            'Task Age',
            'Revision Count',
            'Task Documents',
            'Mark As Completed Date',
            'Task Link'
        ];
    }

    public function map($task): array
    {
        $this->serialNumber++;
        // Handle documents mapping and fallback
        $task_documents = $task->documents->map(function ($document) {
            $url = $document->document;
            return $url ? $url : '';
        })->toArray();
        $comment_documents = $task->comments->flatMap(function ($comment) {
            return $comment->documents->map(function ($document) {
                $url = $document->document;
                return $url ? $url : '';
            });
        })->toArray();
        $merged_documents = array_merge($task_documents, $comment_documents);
        $documents = !empty($merged_documents) ? implode('; ', $merged_documents) : '';

        $age_date_check = Carbon::parse($task->created_at)->format('Y-m-d');
        $daysDifference = Carbon::now()->startOfDay()->diffInDays($age_date_check);

        if ($task->is_recurrence == 1) {
            $age = (Carbon::now()->startOfDay()->diffInDays($task->deadline) ?? '');
        } else {
            $age = ($daysDifference);
        }

        $created_date = Carbon::parse($task->created_at);
        $created_date = $created_date->format('Y-m-d');
        $additional_followers = $task->additional_followers;
        $followers_details = $task->followers_details->pluck('full_name')->implode(', ') ?? '';
        $mark_as_completed_date = $task->mark_as_completed_date ? \Carbon\Carbon::parse($task->mark_as_completed_date)->format('Y-m-d') : '';

        $taskLink = config('app.task_url') . '/task?view_task_id=' . ($task->id ?? '');
        $taskLinkCell = '=HYPERLINK("' . $taskLink . '", "Click to View Task")';
        $branchName = '';
        if ($task->assignedToEmployee) {
            if ($task->assignedToEmployee) {
                $branchName = $task->assignedToEmployee->location ?? '';
            }
        }

        return [
            $this->serialNumber,
            $branchName,
            $task->assignedToEmployee ? $task->assignedToEmployee->employee_id : '',
            $task->assignedToEmployee ? $task->assignedToEmployee->name : '',
            $created_date,
            $task->task_no ? $task->task_no : '',
            $task->name ?? '',
            $task->description ?? '',
            $task->category ? $task->category->name : '',
            $task->priority ? $task->priority->name : '',
            $followers_details,
            $additional_followers,
            $task->deadline ?? '',
            $task->status ? $task->status->name : '',
            $task->is_recurrence == '1' ? 'Yes' : 'No',
            $age ?? '',
            $task->dueDates->count() ?: '',
            $documents ?: '',
            $mark_as_completed_date,
            $taskLinkCell
        ];
    }


    private function applyDeadlineFilter($query, $deadline)
    {
        $today = Carbon::today();

        $query->where(function ($query) use ($deadline, $today) {
            foreach ($deadline as $item) {
                switch ($item) {
                    case 'today':
                        $query->orWhereDate('deadline', $today);
                        break;
                    case 'tomorrow':
                        $query->orWhereDate('deadline', $today->copy()->addDay());
                        break;
                    case 'this_week':
                        $query->orWhereBetween('deadline', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
                        break;
                    case 'next_week':
                        $startOfNextWeek = $today->copy()->addWeek()->startOfWeek();
                        $endOfNextWeek = $today->copy()->addWeek()->endOfWeek();

                        $query->orWhereBetween('deadline', [$startOfNextWeek, $endOfNextWeek]);
                        break;
                    case 'over_due':
                        $query->orWhere(function ($query) use ($today) {
                            $query->whereDate('deadline', '<', $today)
                                ->where('status_id', '!=', 1);
                        });
                        break;
                }
            }
        });
    }

}
