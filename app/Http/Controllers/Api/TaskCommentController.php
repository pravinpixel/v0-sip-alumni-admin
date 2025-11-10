<?php

namespace App\Http\Controllers\Api;

use App\Console\Commands\TaskDue;
use App\Helpers\UtilsHelper;
use App\Http\Controllers\Controller;
use App\Mail\TaskCommentEmail;
use App\Mail\TaskDueChangeRequestEmail;
use App\Models\InvoiceMention;
use App\Models\Mention;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\TaskComment;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Models\TaskCommentDocument;
use App\Models\TaskDueDate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Notifications\TaskCommentNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TaskCommentController extends Controller
{

    public function index(Request $request, $id)
    {
        try {
            $search = $request->get('search', '');
            $pageNumber = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            $sortColumn = $request->get('sort_column', 'created_at');
            $sortOrder = $request->get('sort_order', 'asc');

            $data = TaskComment::where('task_id', $id)
                ->with(['documents', 'from', 'to'])
                ->where(function ($query) use ($search) {
                    $columns = Schema::getColumnListing('task_comments');
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'like', '%' . $search . '%');
                    }
                })
                ->orderBy($sortColumn, $sortOrder)
                ->paginate($perPage, ['*'], 'page', $pageNumber);
        } catch (\Throwable $e) {
            return $this->returnError($e->getMessage());
        }
        return $this->returnSuccess($data, 'Comment List');
    }

    public function save(Request $request)
    {

        $request->merge([
            'mentions' => is_string($request->mentions) ? json_decode($request->mentions, true) : $request->mentions
        ]);
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|min:1|max:10000',
            'task_id' => 'required|int',
            'due_date_change_request' => 'nullable|boolean',
            'mark_as_completed' => 'nullable|boolean',
            'mentions' => 'nullable|array',
            'send_private' => 'nullable|boolean',
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

        try {
            $task = Task::find($request->task_id);
            if (!$task) {
                return response()->json(['message' => "Please Enter Valid Task ID"], 400);
            }
            if ((Auth::user()->id != $task->assigned_to && Auth::user()->id != $task->assigned_by) &&
                !Mention::where('task_id', $request->task_id)->where('mentioned_id', Auth::user()->id)->exists()
            ) {
                return response()->json(['message' => "You are not allowed to comment on this task"], 400);
            }

            $task->mark_as_completed = $request->input('mark_as_completed') ?? 0;
            if ($request->input('mark_as_completed') == 1) {

                $notification = new Notification;
                $notification->module = 'comment';
                $notification->action = 'mark as completed';
                $notification->message = 'Requested to mark this task as completed.';
                $notification->task_id = $task->id;
                $notification->to_id = $task->assigned_by;
                $notification->created_by = Auth::user()->id;
                $notification->save();



                $task->mark_as_completed_date = now()->format('Y-m-d H:i:s');
            } else {
                $task->mark_as_completed_date = null;
            }
            if (Auth::user()->id == $task->assigned_to) {
                $task->due_date_change_request = $request->input('due_date_change_request') ?? 0;
            }
            if (Auth::user()->id == $task->assigned_by && $task->due_date_change_request == 1) {
                $task->due_date_change_request = 0;
            }
            $task->save();

            $setting = Setting::where('name', 'signature')->first();
            $task['signature_logo'] = $setting ? $setting->value : null;
            $task['auth_user'] = Auth::user()->load('designation');
            $task['due_change_count'] = TaskDueDate::where('task_id', $task->id)->count();

            $comment = new TaskComment;
            $comment->comment = $request->input('comment');
            $comment->task_id = $request->input('task_id');
            $comment->send_private = $request->input('send_private') ?? 0;
            $comment->from_id = Auth::user()->id;
            if (Auth::user()->id == $task->assigned_to) {
                $comment->to_id = $task->assigned_by;
            } elseif (Auth::user()->id == $task->assigned_by) {
                $comment->to_id = $task->assigned_to;
            } else {
                $comment->to_id = $task->assigned_to;
            }
            $comment->is_read = 0;
            $comment->save();
            $documentPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    if ($file->getClientMimeType() === 'application/pdf' || $file->extension() === 'pdf') {
                        $originalName = $file->getClientOriginalName();
                        $fileName = "document_" . uniqid() . "_" . time() . "." . $file->extension();
                        $path = $file->storeAs('public/admin/', $fileName);
                        $documentPaths[] = [
                            'path' => 'admin/' . $fileName,
                            'original_name' => $originalName,
                        ];
                    } else {
                        $originalName = $file->getClientOriginalName();
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
                        TaskCommentDocument::create([
                            'comment_id' => $comment->id,
                            'name' => $file['original_name'],
                            'document' => $file['path']
                        ]);
                    }
                }
            }

            $mentions = $request->input('mentions');
            if (!empty($mentions)) {
                $mentions = array_unique($mentions);
            }
            $owners = [$task->assigned_by, $task->assigned_to];
            $owners = array_unique($owners);
            if ($task->due_date_change_request != 1 && $task->mark_as_completed != 1) {
                foreach ($owners as $owner) {
                    $mentions = $request->input('mentions') ?? [0];
                    if ($mentions) {
                        $exists = in_array($owner, $mentions);
                    }
                    if (!$exists && $owner != Auth::user()->id) {
                        $notification = new Notification;
                        $notification->module = 'comment';
                        $notification->action = 'create task comment';
                        $notification->message = 'commented on this task';
                        $notification->task_id = $task->id;
                        $notification->to_id = $owner;
                        $notification->created_by = Auth::user()->id;
                        $notification->save();
                    }
                    if ($owner == Auth::user()->id && $task->is_self_assign == 1 && !$exists) {
                        $notification = new Notification;
                        $notification->module = 'comment';
                        $notification->action = 'create task comment';
                        $notification->message = 'commented on this task';
                        $notification->task_id = $task->id;
                        $notification->to_id = $owner;
                        $notification->created_by = Auth::user()->id;
                        $notification->save();
                    }
                }
            }
            if ($task->due_date_change_request == 1) {
                $notification = new Notification;
                $notification->module = 'due_date_change_request';
                $notification->action = 'send due date change request';
                $notification->message = 'requested a due date change for this task';
                $notification->task_id = $task->id;
                $notification->to_id = $comment->to_id;
                $notification->created_by = Auth::user()->id;
                $notification->save();
            }
            if (!empty($mentions) && $mentions[0] != 0) {
                if (!empty($task->ialert_id)) {
                    $type = 'invoice';
                } else {
                    $type = 'task';
                }
                foreach ($mentions as $data) {
                    $mention = new Mention;
                    $mention->comment_id = $comment->id;
                    $mention->mentioned_id = $data;
                    $mention->mentioned_by = Auth::user()->id;
                    $mention->type = $type;
                    $mention->task_id = $request->input('task_id');
                    $mention->save();

                    $notification = new Notification;
                    $notification->module = 'comment';
                    $notification->action = 'mentioned in task comment';
                    $notification->message = 'mentioned in task comment';
                    $notification->task_id = $task->id;
                    $notification->to_id = $data;
                    $notification->created_by = Auth::user()->id;
                    $notification->save();
                }
            }

            $comment->mark_as_completed = $task->mark_as_completed;
            $comment->task_documents = $task->documents;
            $user = auth()->user();
            $comment['task'] = $task;
            $task['comment'] = $comment->comment;
            $comment['auth_user'] = Auth::user()->load('designation');

            try {
                if ($task->due_date_change_request == 1) {
                    Mail::to([
                        $task->assignedto->email,
                        $task->assignedby->email
                    ])->send(new TaskDueChangeRequestEmail($task));
                } else {
                    if ($task->assigned_to != $task->assigned_by) {
                        Mail::to([
                            $task->assignedto->email,
                            $task->assignedby->email
                        ])->send(new TaskCommentEmail($comment));
                    } else {
                        Mail::to([
                            $task->assignedto->email
                        ])->send(new TaskCommentEmail($comment));
                    }
                }
            } catch (\Exception $e) {
                // Log the exception but do not break the flow
                Log::error('Task Comment notification failed: ' . $e->getMessage());
            }

            //$user->notify(new TaskCommentNotification($comment));

            DB::commit();
            return $this->returnSuccess($comment, 'Task Comment created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError('Task Comment creation failed' . $e->getMessage());
        }
    }
}
