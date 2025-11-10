<?php

namespace App\Http\Controllers\Api;

use App\Helpers\UtilsHelper;
use App\Http\Controllers\Controller;
use App\Mail\QueueMailTesting;
use App\Mail\TaskCompletionEmail;
use App\Mail\TaskCreatedEmail;
use App\Mail\TaskDueChangeEmail;
use App\Mail\TaskRatingEmail;
use App\Models\BranchLocation;
use App\Models\Employee;
use App\Models\Iallert;
use App\Models\InvoiceMention;
use App\Models\Mention;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskDocument;
use App\Models\TaskDueDate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RRule\RRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{

    public function index(Request $request)
    {
        $pageNumber = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        $search = $request->search;
        $sortColumn = $request->get('sort_column', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $tab = $request->get('tab', '');
        if($tab === 'mention'){
            return $this->mentionList($request);
        }
        $priority = $request->input('priority_id', '');
        $task_category_id = $request->input('task_category_id', '');
        $mark_as_completed = $request->input('mark_as_completed');
        $due_date_change_request = $request->input('due_date_change_request');
        $deadline = $request->input('deadline', []);

        switch ($sortColumn) {
            case 'a_to_z':
                $sortColumn = 'name';
                $sortOrder = 'asc';
                break;
            case 'new_to_old':
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
            case 'new_task':
                $sortColumn = 'created_at';
                $sortOrder = 'desc';
                break;
            default:
                if ($tab === 'my_task' || $tab === 'assigned_task') {
                    $sortColumn = 'deadline';
                    $sortOrder = 'asc';
                } elseif ($tab === 'archived') {
                    $sortColumn = 'status_date';
                    $sortOrder = 'desc';
                } else {
                    $sortColumn = 'id';
                    $sortOrder = 'desc';
                }
                break;
        }

        try {
            $query = Task::query()
                ->when($tab === 'my_task' || $tab === '', function ($query) {
                    $query->where('assigned_to', Auth::user()->id)
                        ->whereNotIn('status_id', [1, 3, 8, 9])
                        ->whereRaw('((date <= now() and parent_id is not null) or is_recurrence = 0)');
                })
                ->when($tab === 'assigned_task', function ($query) {
                    $query->where('assigned_by', Auth::user()->id)
                        ->whereNotIn('status_id', [1, 3, 8, 9]);
                })
                ->when($tab === 'archived', function ($query) {
                    $query->where(function ($query) {
                        $query->where('assigned_by', Auth::user()->id)
                            ->orWhere('assigned_to', Auth::user()->id);
                    })
                        ->whereIn('status_id', [1, 3, 8, 9])
                        ->withTrashed();
                })
                ->with(['assignedto', 'assignedby', 'documents', 'status', 'priority'])
                ->when($search, function ($query) use ($search, $tab) {
                    $query->where(function ($query) use ($search, $tab) {
                        $query->where('task_no', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%');

                        if ($tab === 'my_task' || $tab === '') {
                            $query->orWhereHas('assignedby', function ($q) use ($search) {
                                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%');
                            });
                        }

                        if ($tab === 'assigned_task') {
                            $query->orWhereHas('assignedto', function ($q) use ($search) {
                                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%');
                            });
                        }

                        if ($tab === 'archived') {
                            $query->orWhereHas('assignedby', function ($q) use ($search) {
                                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%');
                            })
                                ->orWhereHas('assignedto', function ($q) use ($search) {
                                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%');
                                })
                                ->orWhereHas('status', function ($q) use ($search) {
                                    $q->where('name', 'like', '%' . $search . '%');
                                });
                        }
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
                ->when($tab !== 'my_task' && $tab !== '', function ($query) {
                    $query->where(function ($query) {
                        $query->where('is_recurrence', 0)
                            ->whereNull('parent_id')
                            ->orWhere(function ($query) {
                                $query->whereNotNull('parent_id')
                                    ->where('is_recurrence', 1);
                            });
                    });
                })
                ->select('*')
                ->addSelect(DB::raw('IF((SELECT count(id) FROM task_comments WHERE task_id = tasks.id and is_read = 0 and to_id = ' . Auth::user()->id . ' ) > 0, true, false) as unread'))
                ->addSelect(DB::raw('IF((SELECT count(id) FROM task_comments WHERE task_id = tasks.id ) > 0, false,true) as is_delete'))
                ->orderBy($sortColumn, $sortOrder);
            if($sortColumn == 'mark_as_completed') {
                $query =   $query->orderBy('deadline', 'asc');
            }

            $tasks = $query->paginate($perPage, ['*'], 'page', $pageNumber);
            $tasks = $this->calculateTaskAge($tasks);
            $tasks = $this->getOrganizationName($tasks);
            $tasks = $tasks->toArray();
            $tasks['current_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $tasks['current_date_asia'] = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');
            return $this->returnSuccess($tasks, 'Task List');
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }
    }

    public function mentionList(Request $request)
    {
        try {
            $search = $request->search;
            $pageNumber = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);
            $sortColumn = $request->get('sort_column', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $invoiceMentionData = InvoiceMention::with('mentionedEmployee', 'mentionedInvoice','mentionedBy')
                ->where('mentioned_id', Auth::user()->id)
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('mentionedInvoice', function ($q) use ($search) {
                        $q->where('invoice_number', 'like', '%' . $search . '%');
                    });
                })
                ->orderBy('invoice_id')
                ->orderByDesc('created_at')
                ->get()
                ->unique('invoice_id'); 

            $mentionData = Mention::with('mentionedEmployee','mentionedTask.priority','mentionedBy')
                ->where('mentioned_id', Auth::user()->id)
                ->whereHas('mentionedTask', function ($query) {
                    $query->whereNotNull('id');
                })
                ->when($search, function ($query) use ($search) {
                    $query->whereHas('mentionedTask', function ($q) use ($search) {
                        $q->where('task_no', 'like', '%' . $search . '%');
                    });
                })
                ->orderBy('task_id')
                ->orderByDesc('created_at')
                ->get()
                ->unique('task_id');
            
            $mentionData = $mentionData->map(function($item) {
                if ($item->mentionedTask) {
                    $item->mentionedTask = $this->calculateTaskAge(collect([$item->mentionedTask]))->first();
                }
                return $item;
            });

            $mergedData = $invoiceMentionData->concat($mentionData);

            $mergedData = $mergedData->sortByDesc('created_at');

            $paginatedData = $mergedData->slice(($pageNumber - 1) * $perPage, $perPage)
                ->values();

            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginatedData,
                $mergedData->count(),
                $perPage,
                $pageNumber,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            $data = $paginator;
        } catch (\Throwable $e) {
            return $this->returnError($e->getMessage());
        }
        return $this->returnSuccess($data, 'Mention List');
    }

    private function calculateTaskAge($tasks)
    {
        $tasks->each(function ($task) {
            $ageDateCheck = \Carbon\Carbon::parse($task->created_at)->format('Y-m-d');
            $daysDifference = \Carbon\Carbon::now()->startOfDay()->diffInDays($ageDateCheck);
            $task->age = $task->is_recurrence == 1
                ? \Carbon\Carbon::now()->startOfDay()->diffInDays($task->deadline)
                : $daysDifference;
            // for comment unread count
            $unread_count = TaskComment::where([
                'task_id' => $task->id,
                'is_read' => 0,
                'to_id' => Auth::user()->id
            ])->count();
            $task->unread = $unread_count;
        });
        return $tasks;
    }

    private function getOrganizationName($tasks)
    {
        $tasks->each(function ($task) {
            if($task->ialert_id){
                $data = Iallert::where('id', $task->ialert_id)->first();
                if($data){
                    $organization = Organization::where('customer_code', $data->customer_code)->first();
                    if($organization){
                        $task->organization_name = $organization->company_name;
                    }
                }
            }
        });
        return $tasks;
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

    public function view(Request $request)
    {
        try {
            $id = $request->route('id');
            $task = Task::withTrashed()
            ->select('*')
            ->addSelect(DB::raw('IF((SELECT count(id) FROM task_comments WHERE task_id = tasks.id ) > 0, false, true) as is_delete'))
                ->with('assignedto', 'assignedby', 'documents', 'comments.documents', 'comments.from', 'comments.to', 'priority', 'status','category','dueDates')
                ->where('id', $id)
                ->first();
            if (!$task) {
                return $this->returnError("Task not found");
            }
            $task->tab = match (true) {
                $task->status_id === 1 || $task->status_id === 8 || $task->status_id === 3 || $task->status_id === 9 => 'archived',
                $task->assigned_to === auth()->id() => 'my_task',
                $task->assigned_by === auth()->id() => 'assigned_task',
                default => 'mention',
            };
            $age_date_check = \Carbon\Carbon::parse($task->created_at)->format('Y-m-d');
            $daysDifference = \Carbon\Carbon::now()->startOfDay()->diffInDays($age_date_check);
            if ($task->is_recurrence == 1) {
                $task->age  = \Carbon\Carbon::now()->startOfDay()->diffInDays($task->deadline);
            } else {
                $task->age=$daysDifference;
            }
          
            $task->dueDatesCount = $task->dueDates->count();
            // $userId = auth()->id();
            // if ($task->assigned_to !== $userId && $task->assigned_by !== $userId) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Dear User, You are not able to view the task details. The selected task has not been assigned to you!',
            //     ], 406);
            // }

            $followers_details = [];
            if (!empty($task->followers)) {
                $array = explode(',', $task->followers);

                foreach ($array as $item) {
                    if (!empty($item)) {
                        $employee = Employee::where('id', $item)->first();
                        if ($employee) {
                            $employee_data = $employee->toArray();
                            $employee_data['name'] = $employee->first_name . ' ' . $employee->last_name;
                            $followers_details[] = $employee_data;
                        }
                    }
                }
                $task['followers_details'] = $followers_details;
            }

            $task->current_date = Carbon::now()->format('Y-m-d H:i:s');
            $task->current_date_asia = Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s');

            $unreadComments = TaskComment::where([
                'task_id' => $task->id,
                'is_read' => 0,
                'to_id' => Auth::user()->id
            ])->get();

            if($task->ialert_id){
                $data = Iallert::where('id', $task->ialert_id)->first();
                if($data){
                    $organization = Organization::where('customer_code', $data->customer_code)->first();
                    if($organization){
                        $task->organization_name = $organization->company_name;
                    }
                    $user = Auth::user();
                    $json_branch = json_decode($user->branch_id, true);
                    if (is_array($json_branch)) {
                        $branches = $json_branch;
                    } else {
                        $branches = [$json_branch];
                    }
                    $res = (new AuthController)->getEmployeePermissions($user->role_id);
                    $result = json_decode(json_encode($res), true);
                    $mention_check = InvoiceMention::where('invoice_id', $task->ialert_id)
                        ->where('mentioned_id', $user->id)
                        ->first();
                    $branch_codes = BranchLocation::whereIn('id', $branches)->pluck('branch_code')->toArray();
                    $invoice = Iallert::where('id', $task->ialert_id)->first();
                    $branch_check = in_array($invoice->branch_id, $branch_codes);
                    if ($result) {
                        if ($result['view'] == false && !$mention_check && !$branch_check) {
                            $task->is_invoice_view = false;
                        } elseif ($result['view'] == false && $mention_check && !$branch_check) {
                            $task->is_invoice_view = true;
                        } elseif ($result['view'] == false && $branch_check && !$mention_check) {
                            $task->is_invoice_view = false;
                        } elseif ($result['view'] == true && !$mention_check && !$branch_check) {
                            $task->is_invoice_view = true;
                        } elseif ($result['view'] == false && $mention_check && $branch_check) {
                            $task->is_invoice_view = true;
                        } elseif ($result['view'] == true && !$mention_check && $branch_check) {
                            $task->is_invoice_view = true;
                        } elseif ($result['view'] == true && $mention_check && !$branch_check) {
                            $task->is_invoice_view = true;
                        } else {
                            $task->is_invoice_view = true;
                        }
                    }
                }
            }

            if ($unreadComments->isNotEmpty()) {
                foreach ($unreadComments as $comment) {
                    $comment->is_read = 1;
                    $comment->save();
                }
            }
            return $this->returnSuccess($task, 'Single Task');
        } catch (\Throwable $e) {
            return $this->returnError($e->getMessage());
        }


    }


    public function save(Request $request)
    {
        $request->merge([
            'mentions' => is_string($request->mentions) ? json_decode($request->mentions, true) : $request->mentions
        ]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'description' => 'required|string|max:10000',
            'deadline' => 'nullable|date',
            'mentions' => 'nullable|array',
            'date' => 'nullable|date',
            'priority_id' => 'required',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'assigned_to' => 'required|int',
            'followers' => 'nullable|string|max:200',
            'additional_followers' => 'nullable|string',
            'is_recurrence' => 'nullable|boolean',
            'recurrence' => 'nullable|string',
            'documents.*' => [
                'nullable',
                'file',
                'mimes:pdf,txt,jpeg,jpg,png,xls,xlsx,doc,docx,ppt,pptx',
                'max:5120',
            ],
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        DB::beginTransaction();

        $is_recurrence = $request->input('is_recurrence', "0");
        $ialert_id = $request->input('ialert_id', null);

        try {

            $task = new Task;
            $task->name = $request->input('name');
            $task->description = $request->input('description');
            $task->deadline = $request->input('deadline');
            $task->task_category_id = $request->input('task_category_id');
            $task->date = today();
            $task->ialert_id = $ialert_id;
            $task->priority_id = $request->input('priority_id');
            $task->assigned_to = $request->input('assigned_to');
            $task->assigned_by = Auth::user()->id;
            $task->created_by = Auth::user()->id;
             if($task->assigned_to == $task->assigned_by){
                 $task->is_self_assign = 1;
             }else{
                $task->is_self_assign = 0;
             }
            $task->followers = $request->input('followers');
            $task->additional_followers = $request->input('additional_followers');
            $task->status_id = 2;
            $task->is_recurrence = $is_recurrence;
            $task->recurrence = null;
            if ($is_recurrence == "1") {
                $task->recurrence = $request->input('recurrence');
            } else {
                $task->task_no = UtilsHelper::getTaskMaxNo();
            }
            $task->save();

            if ($request->hasFile('documents')) {
                $allowedFileTypes = ['pdf','txt','jpeg','jpg','png','xls','xlsx','doc','docx','ppt','pptx'];
                $documentPaths = [];

                foreach ($request->file('documents') as $file) {
                    $originalName = $file->getClientOriginalName();

                    if (in_array($file->extension(), $allowedFileTypes)) {
                        $fileName = "document_" . uniqid() . "_" . time() . "." . $file->extension();
                        $path = $file->storeAs('public/admin/', $fileName);

                        $documentPaths[] = [
                            'path' => 'admin/' . $fileName,
                            'original_name' => $originalName,
                        ];
                    }
                }

                if (!empty($documentPaths)) {
                    foreach ($documentPaths as $file) {
                        TaskDocument::create([
                            'task_id' => $task->id,
                            'name' => $file['original_name'],
                            'document' => $file['path'],
                        ]);
                    }
                }
            }
            if ($is_recurrence != 1) {
                $notification = new Notification;
                $notification->module = 'task';
                $notification->action = $task->ialert_id ? 'invoice task created' : 'task created';
                $notification->message = $task->ialert_id ? 'created a task for this invoice' : 'assigned you a new task';
                $notification->task_id = $task->id;
                $notification->to_id = $task->assigned_to;
                $notification->created_by = $task->created_by;
                $notification->save();
            }

            // @mention from task description
            $mentions = $request->input('mentions');
            if (!empty($mentions)) {
                $type = $task->ialert_id ? 'invoice' : 'task';
                foreach ($mentions as $data) {
                    $mention = new Mention;
                    $mention->mentioned_id = $data;
                    $mention->mentioned_by = Auth::user()->id;
                    $mention->type = $type;
                    $mention->task_id = $task->id;
                    $mention->save();

                    $notification = new Notification;
                    $notification->module = 'task';
                    $notification->action = 'task mentioned';
                    $notification->message = 'mentioned you on this task';
                    $notification->task_id = $task->id;
                    $notification->to_id = $data;
                    $notification->created_by = $task->created_by;
                    $notification->save();
                }
            }

            DB::commit();

            $task['auth_user'] = auth()->user()->load('designation');
            if ($is_recurrence === "0") {
                    try {
                        if ($task->is_self_assign == 1) {
                            Mail::to([
                                $task->assignedto->email
                            ])->send(new TaskCreatedEmail($task));
                        } else {
                            Mail::to([
                                $task->assignedto->email,
                            ])->send(new TaskCreatedEmail($task));
                        }
                    } catch (\Exception $e) {
                        $task_mail = Task::find($task->id);
                        $task_mail->is_mail_failed = 1;
                        $task_mail->is_mail_send = 0;
                        $task_mail->save();
                        // Log the exception but do not break the flow
                        Log::error('Task notification failed: ' . $e->getMessage());
                    }
            }

            if ($task->recurrence) {
                unset($task['auth_user']);
                $createdTaskCount = UtilsHelper::recurrenceTask(collect([$task]));
                if ($createdTaskCount == 0) {
                    $specialTask = UtilsHelper::recurrenceTask(collect([$task]), true);
                }
            }

            return $this->returnSuccess($task, 'Task created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('Task creation failed' . $e->getMessage());
        }
    }

    public function save1(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'description' => 'required|string|max:10000',
            'deadline' => 'nullable|date',
            'date' => 'nullable|date',
            'priority_id' => 'required',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'assigned_to' => 'required|int',
            'followers' => 'nullable|string|max:200',
            'additional_followers' => 'nullable|string',
            'is_recurrence' => 'nullable|boolean',
            'recurrence' => 'nullable|string',
            'documents.*' => [
                'nullable',
                'file',
                'mimes:pdf,txt,jpeg,jpg,png,xls,xlsx,doc,docx,ppt,pptx',
                'max:5120',
            ],
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        DB::beginTransaction();

        $is_recurrence = $request->input('is_recurrence', "0");

        try {

            $task = new Task;
            $task->name = $request->input('name');
            $task->description = $request->input('description');
            $task->deadline = $request->input('deadline');
            $task->task_category_id = $request->input('task_category_id');
            $task->date = today();
            $task->priority_id = $request->input('priority_id');
            $task->assigned_to = $request->input('assigned_to');
            $task->assigned_by = Auth::user()->id;
            $task->created_by = Auth::user()->id;
            $task->followers = $request->input('followers');
            $task->additional_followers = $request->input('additional_followers');
            $task->status_id = 2;
            $task->is_recurrence = $is_recurrence;
            $task->recurrence = null;
            if ($is_recurrence == "1") {
                $task->recurrence = $request->input('recurrence');
            } else {
                $task->task_no = UtilsHelper::getTaskMaxNo();
            }
            $task->save();

            if ($request->hasFile('documents')) {
                $allowedFileTypes = ['pdf', 'txt', 'jpeg', 'jpg', 'png', 'xls', 'xlsx'];
                $documentPaths = [];

                foreach ($request->file('documents') as $file) {
                    $originalName = $file->getClientOriginalName();

                    if (in_array($file->extension(), $allowedFileTypes)) {
                        $fileName = "document_" . uniqid() . "_" . time() . "." . $file->extension();
                        $path = $file->storeAs('public/admin/', $fileName);

                        $documentPaths[] = [
                            'path' => 'admin/' . $fileName,
                            'original_name' => $originalName,
                        ];
                    }
                }

                if (!empty($documentPaths)) {
                    foreach ($documentPaths as $file) {
                        TaskDocument::create([
                            'task_id' => $task->id,
                            'name' => $file['original_name'],
                            'document' => $file['path'],
                        ]);
                    }
                }
            }

            DB::commit();

            if ($is_recurrence === "0") {
                try {
                    Mail::to([
                        $task->assignedto->email,
                        $task->assignedby->email,
                    ])->queue(new QueueMailTesting($task));
                } catch (\Exception $e) {
                    $task_mail = Task::find($task->id);
                    $task_mail->is_mail_failed = 1;
                    $task_mail->save();
                    // Log the exception but do not break the flow
                    Log::error('Task notification failed: ' . $e->getMessage());
                }
            }

            if ($task->recurrence) {
                // unset($task['auth_user']);
                $createdTaskCount = UtilsHelper::recurrenceTask(collect([$task]));
                if ($createdTaskCount == 0) {
                    $specialTask = UtilsHelper::recurrenceTask(collect([$task]), true);
                }
            }

            return $this->returnSuccess($task, 'Task created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('Task creation failed' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'mentions' => is_string($request->mentions) ? json_decode($request->mentions, true) : $request->mentions
        ]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'description' => 'required|string|max:10000',
            'deadline' => 'nullable|date',
            'mentions' => 'nullable|array',
            'date' => 'nullable|date',
            'priority_id' => 'required',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'assigned_to' => 'required|int',
            'followers' => 'nullable|string|max:200',
            'additional_followers' => 'nullable|string|max:200',
            'is_recurrence' => 'nullable|boolean',
            'recurrence' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        DB::beginTransaction();

        try {
            $task = Task::find($id);
            if (!$task) {
                return response()->json(['message' => "Task not found"], 400);
            }
            $originalData = $task->getOriginal();

            if ($originalData['is_recurrence'] != $request->input('is_recurrence'))
                return $this->returnError('Recurrence status cannot be changed!');

            $is_recurrence = $request->input('is_recurrence', "0");
            $recurrence = null;
            if ($is_recurrence == "1") {
                $recurrence = $request->input('recurrence');
            }

                $task->name = $request->input('name');
                $task->description = $request->input('description');
                $task->task_category_id = $request->input('task_category_id', null);
                $task->date = today();
                $task->priority_id = $request->input('priority_id');
                $task->assigned_to = $request->input('assigned_to');
                $task->assigned_by = Auth::user()->id;
                $task->created_by = Auth::user()->id;
                 if($task->assigned_to == $task->assigned_by){
                     $task->is_self_assign = 1;
                 }else{
                    $task->is_self_assign = 0;
                 }
                $task->followers = $request->input('followers', null);
                $task->additional_followers = $request->input('additional_followers', null);
                $task->status_id = 2;
                $task->is_recurrence = $is_recurrence;
                if($originalData['assigned_to'] != $task->assigned_to){
                    $task->mark_as_completed = null;
                }
                $task->recurrence = $recurrence;
                $task->deadline = $is_recurrence ? $originalData['deadline'] : $request->input('deadline');


            if ($task->recurrence) {
                if ($originalData['deadline'] >= now()->addDays(3)->format('Y-m-d')) {

                    $startDate = $task->date;
                    $endDate = $task->deadline;
                    $dateArr = [$startDate, $endDate];

                    $first_date = UtilsHelper::isDateInRRule($recurrence, $dateArr, true);

                    if (!empty($first_date['occurrences'])) {
                        $occurrence =$first_date['occurrences'][0]['start'];

                        $dead_line = Carbon::parse($occurrence)->setTimezone('Asia/Kolkata');
                        $task->deadline = $dead_line->format('Y-m-d');
                        $tmp =(clone $dead_line)->modify('-3 days');
                        if($tmp < now()){
                            $tmp = now();
                        }
                        $task->date = $tmp;
                    }
                }
            }
            $task->save();

             // @mention from task description
             $mentions = $request->input('mentions');
             if (!empty($mentions)) {
                 $type = $task->ialert_id ? 'invoice' : 'task';
                 foreach ($mentions as $data) {
                     $mention = new Mention;
                     $mention->mentioned_id = $data;
                     $mention->mentioned_by = Auth::user()->id;
                     $mention->type = $type;
                     $mention->task_id = $task->id;
                     $mention->save();
 
                     $notification = new Notification;
                     $notification->module = 'task';
                     $notification->action = 'task mentioned';
                     $notification->message = 'mentioned you on this task';
                     $notification->task_id = $task->id;
                     $notification->to_id = $data;
                     $notification->created_by = $task->created_by;
                     $notification->save();
                 }
             }

            if($originalData['assigned_to'] != $task->assigned_to){
                $notification = new Notification;
                $notification->module = 'task';
                $notification->action = 'change assigned to';
                $notification->message = 'This task is reassigned to you due to incomplete progress';
                $notification->task_id = $task->id;
                $notification->to_id = $task->assigned_to;
                $notification->created_by = $task->created_by;
                $notification->save();
            }

            if ($originalData['deadline'] != $task->deadline) {
                if($task->due_date_change_request == 1){
                    $notification = new Notification;
                    $notification->module = 'task';
                    $notification->action = 'update due date';
                    $notification->message = 'changed the due date as per your request';
                    $notification->task_id = $task->id;
                    $notification->to_id = $task->assigned_to;
                    $notification->created_by = $task->created_by;
                    $notification->save();
                }else{
                    $notification = new Notification;
                    $notification->module = 'task';
                    $notification->action = 'update due date';
                    $notification->message = 'due date changed this task';
                    $notification->task_id = $task->id;
                    $notification->to_id = $task->assigned_to;
                    $notification->created_by = $task->created_by;
                    $notification->save();
                }
                $task->due_date_change_request = 0;
                $task->save();
                TaskDueDate::create([
                    'task_id' => $task->id,
                    'old_date' => $originalData['deadline'],
                    'new_date' => $task->deadline,
                ]);

                if($task->is_recurrence == 1){

                    $daysDifference = Carbon::parse($originalData['created_at'])->diffInDays(now()->setTimezone('Asia/Kolkata'), false);
                    if ($daysDifference == 0) {
                        $task['deadline_change'] = 'Today';
                    }
                    if ($daysDifference != 0) {
                        $daysChangeMessage = $daysDifference > 0
                            ? "Deadline extended by $daysDifference days."
                            : "Deadline shortened by " . abs($daysDifference) . " days.";
                        $task['deadline_change'] = $daysChangeMessage;
                    }
                    
                }else{

                    $daysDifference = Carbon::parse($originalData['created_at'])->diffInDays(now()->setTimezone('Asia/Kolkata'), false);
                    $task['deadline_change'] = $daysDifference;

                }
            }

            if ($request->hasFile('documents')) {
                $this->handleDocumentUpload($request, $task);
            }

            if ($request->documents_deleted) {
                $this->handleDocumentDeletion($request->documents_deleted);
            }
            if (isset($task->parent_id)) {
                $this->parentUpdate($request, $task->parent_id);
            }

            DB::commit();

            if ($originalData['deadline'] != $task->deadline) {
                $this->sendDueChangeNotification($task);
            }

            if ($originalData['assigned_to'] != $task->assigned_to) {
                $this->sendTaskCreatedNotification($task);
            }


            return $this->returnSuccess($task, 'Task updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('Task update failed: ' . $e->getMessage());
        }
    }

    private function handleDocumentUpload(Request $request, Task $task)
    {
        $documentPaths = [];
        foreach ($request->file('documents') as $file) {
            if ($file->isValid()) {
                $validator = Validator::make(['document' => $file], [
                    'document' => 'file|mimes:pdf,txt,jpeg,jpg,png,xls,xlsx,doc,docx,ppt,pptx|max:5120',
                ]);

                if ($validator->fails()) {
                    return $this->returnError($validator->errors());
                }

                $originalName = $file->getClientOriginalName();
                $fileName = "document_" . uniqid() . "_" . time() . "." . $file->extension();
                $path = $file->storeAs('public/admin/', $fileName);
                $documentPaths[] = [
                    'path' => 'admin/' . $fileName,
                    'original_name' => $originalName,
                ];
            }
        }

        foreach ($documentPaths as $file) {
            TaskDocument::create([
                'task_id' => $task->id,
                'name' => $file['original_name'],
                'document' => $file['path'],
            ]);
        }
    }

    private function handleDocumentDeletion(array $documentIds)
    {
        foreach ($documentIds as $id) {
            $document = TaskDocument::find($id);
            if ($document) {
                $filePath = public_path($document->file_path);
                if (is_file($filePath) && file_exists($filePath)) {
                    unlink($filePath);
                }
                $document->delete();
            }
        }
    }

    private function sendDueChangeNotification(Task $task)
    {
        if($task->is_self_assign == 1){
            try {
                $setting = Setting::where('name', 'signature')->first();
                $task['signature_logo'] = $setting ? $setting->value : null;
                $task['auth_user'] = Auth::user()->load('designation');
                $task['due_change_count'] = TaskDueDate::where('task_id', $task->id)->count();
                if ($task->assignedto) {
                    Mail::to([$task->assignedto->email])
                        ->send(new TaskDueChangeEmail($task));
                }
            } catch (\Exception $e) {
                $task_mail = Task::find($task->id);
                $task_mail->is_mail_failed = 1;
                $task_mail->save();
                // Log the exception but do not break the flow
                Log::error('Task Due Update notification failed: ' . $e->getMessage());
            }

        }else{

            try {
                $setting = Setting::where('name', 'signature')->first();
                $task['signature_logo'] = $setting ? $setting->value : null;
                $task['auth_user'] = Auth::user()->load('designation');
                $task['due_change_count'] = TaskDueDate::where('task_id', $task->id)->count();
                if ($task->assignedto) {
                    Mail::to([$task->assignedto->email])
                        ->send(new TaskDueChangeEmail($task));
                }
            } catch (\Exception $e) {
                $task_mail = Task::find($task->id);
                $task_mail->is_mail_failed = 1;
                $task_mail->save();
                // Log the exception but do not break the flow
                Log::error('Task Due Update notification failed: ' . $e->getMessage());
            }

        }
    }

    private function sendTaskCreatedNotification(Task $task)
    {

        if($task->is_self_assign == 1){

            try {
                $task['auth_user'] = Auth::user()->load('designation');
                if ($task->assignedto) {
                    Mail::to([$task->assignedto->email])
                        ->send(new TaskCreatedEmail($task));
                }
            } catch (\Exception $e) {
                $task_mail = Task::find($task->id);
                $task_mail->is_mail_failed = 1;
                $task_mail->save();
                // Log the exception but do not break the flow
                Log::error('Task Assigned to notification failed: ' . $e->getMessage());
            }

        }else{

            try {
                $task['auth_user'] = Auth::user()->load('designation');
                if ($task->assignedto) {
                    Mail::to([$task->assignedto->email])
                        ->send(new TaskCreatedEmail($task));
                }
            } catch (\Exception $e) {
                $task_mail = Task::find($task->id);
                $task_mail->is_mail_failed = 1;
                $task_mail->save();
                // Log the exception but do not break the flow
                Log::error('Task Assigned to notification failed: ' . $e->getMessage());
            }
            
        }
        
    }


    public function parentUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'description' => 'required|string|max:10000',
            'deadline' => 'nullable|date',
            'date' => 'nullable|date',
            'priority_id' => 'required',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'assigned_to' => 'required|int',
            'followers' => 'nullable|string|max:200',
            'additional_followers' => 'nullable|string|max:200',
            'recurrence' => 'nullable|string',

        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        DB::beginTransaction();

        try {

            $task = Task::find($id);
            if (!$task) {
                return response()->json(['message' => "Parent Task not found"], 400);
            }
            $task->name = $request->input('name');
            $task->description = $request->input('description');
            $task->deadline = $request->input('deadline', null);
            $task->task_category_id = $request->input('task_category_id', null);
            $task->date = today();
            $task->priority_id = $request->input('priority_id');
            $task->assigned_to = $request->input('assigned_to');
            $task->assigned_by = Auth::user()->id;
            $task->created_by = Auth::user()->id;
            if($task->assigned_to == $task->assigned_by){
                $task->is_self_assign = 1;
            }else{
               $task->is_self_assign = 0;
            }
            $task->followers = $request->input('followers', null);
            $task->additional_followers = $request->input('additional_followers', null);
            $task->status_id = 2;
            $task->recurrence = $request->input('recurrence');
            $task->save();

            DB::commit();
            return $this->returnSuccess($task, 'Task parent updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('Task update failed' . ' ' . $e->getMessage());
        }
    }

    public function taskcompleteStatusUpdate(Request $request, $id)
    {

        $task = Task::find($id);
        if(!$task) {
            return $this->returnError(['message' => 'Task not found'], 404);
        }
        if($task->is_self_assign == '1') {
            $validation_arr = [
                'task_rating' => 'nullable|int',
                'rating_remark' => 'nullable|string|max:10000',
            ];
        }else{
            $validation_arr = [
                'task_rating' => 'required|int',
                'rating_remark' => 'required_if:task_rating,1,2|nullable|string|max:10000',
            ];
        }

        $validator = Validator::make($request->all(), $validation_arr);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        DB::beginTransaction();

        try {

            if ($task->created_by == Auth::user()->id) {
                $task->status_id = 1;
                $task->due_date_change_request = 0;
                $task->mark_as_completed = 0;
                $task->status_date = now('Asia/Kolkata');
                $task->task_rating = $request->input('task_rating') ?? 0;
                $task->rating_remark = $request->input('rating_remark');
                $task->save();
            } else {
                return $this->returnError(['message' => 'Unauthorized'], 403);
            }

            $notification = new Notification;
            $notification->module = 'comment';
            $notification->action = 'task completed';
            $notification->message = 'Marked this task as Completed.';
            $notification->task_id = $task->id;
            $notification->to_id = $task->assigned_to;
            $notification->created_by = Auth::user()->id;
            $notification->save();

            $user = auth()->user();
            $task['auth_user'] = Auth::user()->load('designation');
            // $setting = Setting::where('name', 'signature')->first();
            // $task['signature_logo'] = $setting ? $setting->value : null;
            DB::commit();

            try {
                if ($task->assignedto) {
                    if($task->is_self_assign == '0') {
                        Mail::to([
                            $task->assignedto->email
                        ])->send(new TaskCompletionEmail($task));

                        Mail::to([
                            $task->assignedto->email,
                            $task->assignedby->email,
                        ])->send(new TaskRatingEmail($task));

                    }else{

                        Mail::to([
                            $task->assignedto->email
                        ])->send(new TaskCompletionEmail($task));

                        if (!empty($task->task_rating) && !empty($task->rating_remark)) {
                            Mail::to([
                                $task->assignedto->email,
                            ])->send(new TaskRatingEmail($task));
                        }
                    }
                }
            } catch (\Exception $e) {
                $task_mail = Task::find($task->id);
                $task_mail->is_mail_failed = 1;
                $task_mail->save();
                // Log the exception but do not break the flow
                Log::error('Task Completed notification failed: ' . $e->getMessage());
            }

            return $this->returnSuccess($task, 'Task status updated successfully!');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->returnError(['message' => 'Task not found'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('Task status update failed');
        }
    }


    public function closeStatusUpdate(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $task = Task::findOrFail($id);

            if ($task->created_by == Auth::user()->id) {
                $task->status_id = 8;
                $task->due_date_change_request = 0;
                $task->mark_as_completed = 0;
                $task->status_date = now();
                $task->save();
            } else {
                return $this->returnError(['message' => 'Unauthorized'], 403);
            }
            
            $notification = new Notification;
            $notification->module = 'task';
            $notification->action = 'task closed';
            $notification->message = 'closed this task';
            $notification->task_id = $task->id;
            $notification->to_id = $task->assigned_to;
            $notification->created_by = Auth::user()->id;
            $notification->save();


            DB::commit();
            return $this->returnSuccess($task, 'Task status closed successfully!');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->returnError(['message' => 'Task not found'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('Task status update failed');
        }
    }

    public function delete(Request $request, $id, $entire = null)
    {
        DB::beginTransaction();

        try {
            if ($entire === 'delete_all') {
                $task = Task::findOrFail($id);
                $parentTask = Task::find($task->parent_id);

                if ($parentTask) {
                    $parentDocuments = TaskDocument::where('task_id', $parentTask->id)->get();
                    foreach ($parentDocuments as $document) {
                        $document->delete();
                    }
                    $parentTask->delete();
                }
                $taskDocuments = TaskDocument::where('task_id', $task->id)->get();
                foreach ($taskDocuments as $document) {
                    $document->delete();
                }
                $task->status_id = 9;
                $task->status_date = now();
                $task->save();
                $task->delete();

                $upcomingTasks = Task::where('parent_id', $task->parent_id)
                ->where('id', '>', $task->id)
                ->get();

                if($upcomingTasks->count() > 0) {

                    foreach ($upcomingTasks as $upcomingTask) {
                        $upcomingtaskDocuments = TaskDocument::where('task_id', $upcomingTask->id)->get();
                        foreach ($upcomingtaskDocuments as $upcoming) {
                            $upcoming->delete();
                        }
                        $upcomingTask->status_id = 9;
                        $upcomingTask->status_date = now();
                        $upcomingTask->save();
                        $upcomingTask->delete();
                    }

                }

            } else {
                $task = Task::findOrFail($id);
                $taskDocuments = TaskDocument::where('task_id', $task->id)->get();
                if (!$task->comments()->count() > 0) {
                    foreach ($taskDocuments as $document) {
                        $document->delete();
                    }

                    $task->status_id = 9;
                    $task->status_date = now();
                    $task->save();

                    $task->delete();
                } else {
                    return $this->returnError('Task Delete Failed: No comments associated');
                }
            }

            DB::commit();
            return $this->returnSuccess($task, 'Task and related documents deleted successfully!');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return $this->returnError(['message' => 'Task not found'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('Task deletion failed');
        }
    }
}
