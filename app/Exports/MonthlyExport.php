<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class MonthlyExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Employee::query()->with('tasks')->where('status', '1');

        // Apply month and year filter if provided
        // if (isset($this->filters['month']) || isset($this->filters['year'])) {
        //     $month = $this->filters['month'];
        //     $year = $this->filters['year'];

        //     $query->whereHas('tasks', function ($query) use ($month, $year) {
        //         if ($month) {
        //             $query->whereMonth('created_at', $month);
        //         }
        //         if ($year) {
        //             $query->whereYear('created_at', $year);
        //         }
        //     });
        // }else {
        //     $query->whereHas('tasks', function ($query) {
        //         $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        //     });
        // }

        // Apply employee filter if provided
        if (isset($this->filters['employee'])) {
            $employeeId = $this->filters['employee'];
            $query->where('id', $employeeId);
        }

        // Apply branch filter if provided
        if (isset($this->filters['branch'])) {
            $branch = $this->filters['branch'];
            $query->where('branch_id', 'like', "%{$branch}%");
        }

        // Apply search filter if provided
        if (isset($this->filters['search'])) {
            $searchQuery = $this->filters['search'];
            $query->where(function ($q) use ($searchQuery) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$searchQuery}%")
                  ->orWhereHas('branchLocation', function ($q) use ($searchQuery) {
                      $q->where('name', 'like', "%{$searchQuery}%");
                  });
            });
        }

        // Get the result without pagination (for export)
        return $query->get();
    }

    public function startCell(): string
    {
        return 'A3';  // Data starts from row 3
    }

    public function headings(): array
    {
        return [
            [' ', ' ', 'MONTH', ' ', ' ', 'Overall', ' ', ' ', 'Pending Task Analysis', '', '', ''],
            ['Branch', 'Employee Name', 'Total Task Assigned', 'No of Task Completed', 'Monthly Avg Rating', 'Total Task Assigned', 'Total Completed Task', 'Cumulative Avg Rating', '0-14', '15-30', '30>', 'Total Task Pending'],
        ];
    }

    public function map($task,$month = null, $year = null): array
    {   
        $month = $month ?? $this->filters['month'] ?? null;
        $year = $year ?? $this->filters['year'] ?? null;
        $assignedto = $task;

        // Initialize default values
        $total_assigned_task = $total_completed_task = $overall_assigned_task = $overall_completed_task = 0;
        $tasks_last_14_days = $tasks_last_15_to_30_days = $tasks_graterthan_30_days = $total_pending_task = 0;
        $rating = $overall_rating = 0;

        if ($assignedto) {
            $queryBuilder = $assignedto->tasks()->where('status_id',1)
             ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('date', '<=', now())
                        ->whereNotNull('parent_id');
                })->orWhere('is_recurrence', 0);
            })
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('is_recurrence', 0)
                        ->whereNull('parent_id');
                })
                ->orWhere(function ($query) {
                    $query->where('is_recurrence', 1)
                        ->whereNotNull('parent_id');
                });
            });

            //  Apply month and year filters if provided
            if ($month || $year) {
                
                    if ($month) {
                        $queryBuilder->whereMonth('created_at', $month);
                    }
                    if ($year) {
                        $queryBuilder->whereYear('created_at', $year);
                    }
                
            }else {
                $queryBuilder->whereBetween('created_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ]);
            }

            $total_assigned_task = Task::where('assigned_to', $assignedto->id)
                ->whereIn('status_id', [1, 2])
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)
                            ->whereNotNull('parent_id');
                    });
                });
                if ($month) {
                    $total_assigned_task->whereMonth('created_at', $month);
                }
                elseif ($year) {
                    $total_assigned_task->whereYear('created_at', $year);
                } else {
                    // Default to current month if no specific month/year is provided
                    $total_assigned_task->whereBetween('created_at', [
                        now()->subMonth()->startOfMonth(),
                        now()->subMonth()->endOfMonth()
                    ]);
                }
                $total_assigned_task = $total_assigned_task->count();

            $total_completed_task = Task::where('assigned_to', $assignedto->id)
                ->where('status_id', 1)->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)
                            ->whereNotNull('parent_id');
                    });
                });
                if ($month) {
                    $total_completed_task->whereMonth('created_at', $month);
                }
                elseif ($year) {
                    $total_completed_task->whereYear('created_at', $year);
                } else {
                    // Default to current month if no specific month/year is provided
                    $total_completed_task->whereBetween('created_at', [
                        now()->subMonth()->startOfMonth(),
                        now()->subMonth()->endOfMonth()
                    ]);
                }
                $total_completed_task = $total_completed_task->count();

            // Calculate ratings
            $ratingData = $queryBuilder->clone()
                ->where('assigned_to', $assignedto->id)
                ->where('is_self_assign', 0)
                ->select(DB::raw('SUM(task_rating) as totalRatings'), DB::raw('COUNT(id) as ratingCount'))
                ->first();

            if ($ratingData && $ratingData->ratingCount > 0) {
                $rating = number_format(($ratingData->totalRatings / $ratingData->ratingCount), 1) ?: 0;
            }

            $overall_assigned_task = Task::where('assigned_to', $assignedto->id)
                ->whereIn('status_id', [1, 2])
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)
                            ->whereNotNull('parent_id');
                    });
                })->count();

            $overall_completed_task = Task::where('assigned_to', $assignedto->id)
                ->where('status_id', 1)->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)
                            ->whereNotNull('parent_id');
                    });
                })
                ->count();

            $overall_ratingData = DB::table('tasks')
                ->where('status_id', 1)
                ->where('assigned_to', $assignedto->id)
                ->where('is_self_assign', 0)
                ->select(DB::raw('SUM(task_rating) as totalRatings'), DB::raw('COUNT(id) as ratingCount'))
                ->first();

            if ($overall_ratingData && $overall_ratingData->ratingCount > 0) {
                $overall_rating = number_format(($overall_ratingData->totalRatings / $overall_ratingData->ratingCount), 1) ?: 0;
            }

            $tasks_last_14_days = Task::where('status_id', 2)
                ->where('assigned_to', $assignedto->id)
                ->whereDate('created_at', '>=', now()->subDays(13)->toDateString())
                ->whereDate('created_at', '<=', now()->toDateString())
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)
                            ->whereNotNull('parent_id');
                    });
                })->count();

            $tasks_last_15_to_30_days = Task::where('status_id', 2)
                ->where('assigned_to', $assignedto->id)
                ->whereDate('created_at','>=', now()->subDays(30)->toDateString() )
                ->whereDate('created_at','<=', now()->subDays(14)->toDateString() )
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)
                            ->whereNotNull('parent_id');
                    });
                })
                ->count();

            $tasks_graterthan_30_days = Task::where('status_id', 2)
                ->where('assigned_to', $assignedto->id)
                ->whereDate('created_at', '<', now()->subDays(30)->toDateString())
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('date', '<=', now())
                            ->whereNotNull('parent_id');
                    })->orWhere('is_recurrence', 0);
                })
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id');
                    })
                    ->orWhere(function ($query) {
                        $query->where('is_recurrence', 1)
                            ->whereNotNull('parent_id');
                    });
                })
                ->count();

            $total_pending_task = $tasks_last_14_days + $tasks_last_15_to_30_days + $tasks_graterthan_30_days;
        }

        return [
            isset($assignedto->branch_name) ? $assignedto->branch_name : '',
            ($assignedto->first_name ?? '') . ' ' . ($assignedto->last_name ?? '') ?? '',
            $total_assigned_task ?: '0',
            $total_completed_task ?: '0',
            $rating ?: '0',
            $overall_assigned_task ?: '0',
            $overall_completed_task ?: '0',
            $overall_rating ?: '0',
            $tasks_last_14_days ?: '0',
            $tasks_last_15_to_30_days ?: '0',
            $tasks_graterthan_30_days ?: '0',
            $total_pending_task ?: '0',
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                /** @var Worksheet $sheet */
                $sheet = $event->sheet->getDelegate();

                // Styling the first-level header (Row 1)
                $sheet->mergeCells('A3:B3');
                $sheet->mergeCells('C3:E3');
                $sheet->mergeCells('F3:H3');
                $sheet->mergeCells('I3:L3');

                // Styling for second-level header
                $sheet->getStyle('A3:L4')->applyFromArray([
                    'font' => [
                        'bold' => true,

                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Set the row height for headers
                $sheet->getRowDimension(3)->setRowHeight(30);
                $sheet->getRowDimension(4)->setRowHeight(20);

                // Auto-size columns
                foreach (range('A', 'L') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        ];
    }
}
