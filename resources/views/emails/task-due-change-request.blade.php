@extends('emails.index')

@section('style')
@parent
<style>

</style>
@endsection


@section('content')
<div>
    <p>Hi {{$task->assignedby->first_name}},</p>
    <p>
        There is a <span style='color: blue;'><b>Due Date Change Request</b></span> by the assignee.Please see the task details below.
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
            @if($task->documents->isNotEmpty())
                @foreach ($task->documents as $document)
                    <a href="{{ $document->document }}" target="_blank">
                                                &#128196;
                    </a>
                @endforeach
            @endif
            </td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Original Due Date 📅</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Due Date Revision Count</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->due_change_count ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Revised Due Date request 📅</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->comment ?? '' }}</td>
        </tr>
    </table>
</div>

@endsection
