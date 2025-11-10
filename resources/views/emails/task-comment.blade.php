@extends('emails.index')

@section('style')
@parent
<style>

</style>
@endsection


@section('content')
<div>
    @if($task->task->mark_as_completed == '1')
    <p>Hi {{$task->task->assignedby->first_name ?? ''}},</p>
    <p>
        There is a <span style='color: #9900ff;'><b>Task Completed Request</b></span> to this task. The details of the task completion comments are given below
    </p>
    @else
    <p>Hi All,</p>
    <p>
        There is a comment added to this task. The details of the comment are given below
    </p>
    @endif

    <table style="border-collapse: collapse;">
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task ID</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><a
                    href="{{ config('app.task_url') . '/task?view_task_id=' . ($task->task->id ?? '') }}">{{ $task->task->task_no ?? '' }}</a>
            </td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Subject</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->task->name ?? '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Due Date 📅</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->task->deadline ? \Carbon\Carbon::parse($task->task->deadline)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Attachment 📎</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">
                @foreach ($task->task->documents as $document)
                <a href="{{ $document->document }}" target="_blank">
                    &#128196;
                </a>
                @endforeach
            </td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Age 📅</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->due_date_string ? $task->due_date_string : '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Commented By:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: blue;">{{ $task->from->first_name ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Commented To:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: blue;">{{ $task->to->first_name ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Comment Details:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->comment ?? '' }}</td>
        </tr>
        <tr>

            <td style="border: 2px solid black;padding: 8px;text-align: left;">Comment Attachment 📎</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">
                @foreach ($task->documents as $document)
                <a href="{{ $document->document }}" target="_blank">
                    &#128196;
                </a>
                @endforeach
            </td>
        </tr>
    </table>
    <p>
        Please make sure the above task is completed on or before the revised Due date.
    </p>
</div>

@endsection