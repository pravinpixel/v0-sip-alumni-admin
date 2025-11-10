<?php

namespace App\Exports;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OverdueExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    private $serialNumber = 0;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    public function collection()
    {
        $currentDate = now()->toDateString();

        return Task::whereDate('deadline', '<', $currentDate)->where(function ($query) {
            $query->where(function ($query) {
                $query->where('is_recurrence', 0)
                    ->whereNull('parent_id');
            })->orWhere(function ($query) {
                $query->whereNotNull('parent_id')
                    ->where('is_recurrence', 1);
            });
        })
            ->where('status_id', 2)
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
            ->when($this->filters['start_date'], function ($query) {
                $query->whereDate('deadline', '>=', $this->filters['start_date']);
            })
            ->when($this->filters['end_date'], function ($query) {
                $query->whereDate('deadline', '<=', $this->filters['end_date']);
            })
            ->when($this->filters['assigned_by'], function ($query) {
                $query->where('assigned_by', '=', $this->filters['assigned_by']);
            })
            ->when($this->filters['assigned_to'], function ($query) {
                $query->where('assigned_to', '=', $this->filters['assigned_to']);
            })
            ->when($this->filters['priority'], function ($query) {
                $query->where('priority_id', '=', $this->filters['priority']);
            })
            ->when($this->filters['task_type'], function ($query) {
                $query->where('task_category_id', '=', $this->filters['task_type']);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'Sr.No',
            'Reporting Manager',
            'Branch',
            'Task ID',
            'Assigner Employee ID',
            'AssignedBy',
            'Assignee Reporting Manager',
            'Assignee Employee ID',
            'Assignee Branch',
            'AssignedTo',
            'Creation Date',
            'Task Subject',
            'Task Description',
            'Task Type',
            'Task Priority',
            'Task Due date',
            'Task Age',
            'Revision Count',
            'Mark Us Completed At',
            'Task Documents',
            'Task Link'
            // Add more headings as per your task fields
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
            $age = ($task->deadline ? Carbon::now()->startOfDay()->diffInDays($task->deadline) : '');
        } else {
            $age = ($daysDifference);
        }
        $taskLink = config('app.task_url') . '/task?view_task_id=' . ($task->id ?? '');
        $taskLinkCell = '=HYPERLINK("' . $taskLink . '", "' . $taskLink . '")';
        return [
            $this->serialNumber,
            // Safely accessing reportingManager
            $task->assignedby && $task->assignedby->reporting_managers
                ? $task->assignedby->reporting_managers
                : '',
            // Safely accessing branchLocation
            $task->assignedby && $task->assignedby->location
                ? $task->assignedby->location
                : '',
            $task->task_no ?: '',
            $task->assignedby?->employee_id ?: '',
            $task->assignedby?->name ?: '',
            // Safely accessing reportingManager
            $task->assignedto && $task->assignedto->reporting_managers
                ? $task->assignedto->reporting_managers
                : '',
            $task->assignedto?->employee_id ?: '',
            // Safely accessing branchLocation
            $task->assignedto && $task->assignedto->location
                ? $task->assignedto->location
                : '',
            $task->assignedto?->name ?: '',
            $task->created_at ? $task->created_at->format('Y-m-d') : '',
            $task->name ?: '',
            $task->description ?: '',
            $task->category?->name ?: '',
            $task->priority->name ?: '',
            $task->deadline ?: '',
            $age,
            $task->dueDates->count() ?: '',
            $task->mark_as_completed_date ? Carbon::parse($task->mark_as_completed_date)->format('d-m-Y') : '',
            $documents ?: '',
            $taskLinkCell
        ];
    }
}
