@extends('emails.index')

@section('style')
@parent
<style>

</style>
@endsection

@section('content')
<div>
    <p>Hi {{$task->assignedto->first_name ?? ''}},</p>
    <p>
        You have a <strong style="color: blue;">new task assigned to you by <u>{{ $task->assignedby->first_name ?? '' }}</u></strong>. The task details are given below.
    </p>
    <table style="border-collapse: collapse;">
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task ID</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><a href="{{ config('app.task_url') . '/task?view_task_id=' . ($task->id ?? '') }}">{{ $task->task_no ?? '' }}</a></td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Subject</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->name ?? '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Details</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->description ?? '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Priority ❗</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->priority ? $task->priority->name : '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Attachment 📎</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">
                @foreach ($task->documents as $document)
                    <a href="{{ $document->document }}" target="_blank">
                                                &#128196;
                    </a>
                @endforeach
            </td>
        </tr>
    </table>
    <p class="warning">
        <i>⚠️ In case if you want to change the Due date please feel free to reply to this email with the
        revised due date. The assigner shall review the same and change the due date</i>
    </p>
    <p>
        Please make sure the above task is completed on or before the Due date.
    </p>
</div>

@endsection
