<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AllTasksExport implements FromCollection, WithHeadings, WithMapping, WithChunkReading
{
    protected $filters;
    private $employees;

    private $emptyEmployee = [
        'name' => '',
        'employee_id' => '',
        'branch_name' => '',
        'reporting_managers' => '',
    ];

    public function __construct($filters)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $this->filters = $filters;
    }

    public function collection()
    {

        $this->employees = Employee::all()->select('id', 'employee_id', 'first_name', 'last_name', 'reporting_managers', 'branch_id', 'location_id', 'location', 'branch_name')->toArray();

        return Task::query()
            ->with([
                'status:id,name',
                'priority:id,name',
                'category:id,name',
                'documents:id,task_id,document',
                'comments:id,task_id',
                'comments.documents:id,comment_id,document'
            ])
            ->withCount('dueDates')
            ->where(function ($query)  {
                $query->where(function ($subQuery) {
                    $subQuery->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })->orWhere(function ($subQuery)  {
                    $subQuery->whereNotNull('parent_id')
                        ->where('is_recurrence', 1);
                });
            })
            ->when(isset($this->filters['search']), function ($query) {
                $searchQuery = $this->filters['search'];

                $query->where(function ($subQuery) use ($searchQuery) {
                    $subQuery->where('task_no', 'like', "%{$searchQuery}%")

                        ->orWhereHas('assignedby', function ($q) use ($searchQuery) {
                            $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$searchQuery}%")
                                ->orWhereHas('branchLocation', function ($q) use ($searchQuery) {
                                    $q->where('name', 'like', "%{$searchQuery}%");
                                })
                                ->orWhere('employee_id', 'like', "%{$searchQuery}%");
                        })

                        ->orWhereHas('assignedto', function ($q) use ($searchQuery) {
                            $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$searchQuery}%")
                            ->orWhereHas('branchLocation', function ($q) use ($searchQuery) {
                                    $q->where('name', 'like', "%{$searchQuery}%");
                                });
                        })

                        ->orWhereHas('category', function ($q) use ($searchQuery) {
                            $q->where('name', 'like', "%{$searchQuery}%");
                        })

                        ->orWhereHas('priority', function ($q) use ($searchQuery) {
                            $q->where('name', 'like', "%{$searchQuery}%");
                        })

                        ->orWhereHas('status', function ($q) use ($searchQuery) {
                            $q->where('name', 'like', "%{$searchQuery}%");
                        })

                        ->orWhere('additional_followers', 'like', "%{$searchQuery}%")

                        ->orWhereHas('documents', function ($q) use ($searchQuery) {
                            $q->where('name', 'like', "%{$searchQuery}%");
                        });
                });
            })

            ->when(isset($this->filters['start_date']), function ($query)  {
                $query->whereDate('deadline', '>=', $this->filters['start_date']);
            })
            ->when(isset($this->filters['end_date']), function ($query)  {
                $query->whereDate('deadline', '<=', $this->filters['end_date']);
            })
            ->when(isset($this->filters['assigned_by']), function ($query)  {
                $query->where('assigned_by', '=', $this->filters['assigned_by']);
            })
            ->when(isset($this->filters['assigned_to']), function ($query)  {
                $query->where('assigned_to', '=', $this->filters['assigned_to']);
            })
            ->when(isset($this->filters['priority']), function ($query)  {
                $query->where('priority_id', '=', $this->filters['priority']);
            })
            ->when(isset($this->filters['recurrence']), function ($query)  {
                $query->where('is_recurrence', '=', $this->filters['recurrence']);
            })
            ->when(isset($this->filters['task_type']), function ($query)  {
                $query->where('task_category_id', '=', $this->filters['task_type']);
            })
            ->when(isset($this->filters['status']), function ($query)  {
                $query->where('status_id', '=', $this->filters['status']);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'Reporting Manager',
            'Branch',
            'Employee ID',
            'Assigned By',
            'Assignee Reporting Manager',
            'Assignee Branch',
            'Task Assignee Emp ID',
            'Task Assigned To',
            'Creation Date',
            'Task ID',
            'Task Subject',
            'Task Details',
            'Task Type',
            'Task Priority',
            'Task Followers ',
            'Task Additional Followers',
            'Task Due Date',
            'Task Status',
            'Task Recurrence',
            'Task Age',
            'Revision Count',
            'Task Documents',
            'Task Rating',
            'Rating Remarks',
            'Mark Us Completed Date',
            'Completed Date',
            'Task Link',
        ];
    }

    public function map($task): array
    {
        static $employeeById = null;
        if ($employeeById === null) {
            $employeeById = [];
            foreach ($this->employees as $emp) {
                $employeeById[$emp['id']] = [
                    'name' => $emp['first_name'] . ' ' . $emp['last_name'],
                    'employee_id' => $emp['employee_id'] ?? '',
                    'branch_name' => $emp['branch_name'] ?? '',
                    'location' => $emp['location'] ?? '',
                    'reporting_managers' => $emp['reporting_managers'] ?? '',
                ];
            }
        }

        $assigned_by = $employeeById[$task->assigned_by] ?? $this->emptyEmployee;
        $assigned_to = $employeeById[$task->assigned_to] ?? $this->emptyEmployee;

        $follow = explode(',', $task->followers);
        $follower_names = [];
        foreach ($follow as $fid) {
            $fid = trim($fid);
            if ($fid && isset($employeeById[$fid])) {
                $follower_names[] = $employeeById[$fid]['name'];
            }
        }

        $task_documents = $task->documents->pluck('document')->filter()->toArray();
        $comment_documents = $task->comments->flatMap(function ($comment) {
            return $comment->documents->pluck('document')->filter();
        })->toArray();
        $merged_documents = array_merge($task_documents, $comment_documents);
        $documents = !empty($merged_documents) ? implode('; ', $merged_documents) : '-';


        $age_date_check = Carbon::parse($task->created_at)->format('Y-m-d');
        $daysDifference = Carbon::now()->startOfDay()->diffInDays($age_date_check);

        if ($task->is_recurrence == 1) {
            $age = ($task->deadline ? Carbon::now()->startOfDay()->diffInDays($task->deadline) : '-');
        } else {
            $age = ($daysDifference);
        }
        $taskLink = config('app.task_url') . '/task?view_task_id=' . ($task->id ?? '');
        $taskLinkCell = '=HYPERLINK("' . $taskLink . '", "' . $taskLink . '")';


        return [
            $assigned_by['reporting_managers'] ?? '',
            $assigned_by['location'] ?? '',
            $assigned_by['employee_id'],
            $assigned_by['name'],

            $assigned_to['reporting_managers'] ?? '',
            $assigned_to['location'] ?? '',
            $assigned_to['employee_id'],
            $assigned_to['name'],

            $task->created_at ? $task->created_at->format('Y-m-d') : '',
            $task->task_no ?? '',
            $task->name ?? '',
            $task->description ?? '',
            $task->category->name ?? '',
            $task->priority->name ?? '',

            $follower_names ? implode(', ', $follower_names) : '',
            $task->additional_followers ?? '',
            $task->deadline ?? '',
            $task->status->name ?? '',
            $task->is_recurrence ? 'Yes' : 'No',
            $age,
            $task->due_dates_count ?: '',
            $documents,
            $task->task_rating ?? '',
            $task->rating_remark ?? '',
            $task->mark_as_completed_date ? Carbon::parse($task->mark_as_completed_date)->format('d-m-Y') : '',

            $task->status_id == 1
                ? ($task->status_date ? Carbon::parse($task->status_date)->format('d-m-Y') : '')
                : '',
            $taskLinkCell
        ];
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}
