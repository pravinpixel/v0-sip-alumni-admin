@extends('emails.index')

@section('style')
@parent
<style>

</style>
@endsection


@section('content')
<div>
    <p>Hi {{$task->assignedto->first_name}},</p>
    <p>
        The <span style='color: blue;'><b>DUE Date of this has been changed by the assigner</b></span> as per your
        request . Please see the
        task details below.
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
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Revised Due Date 📅</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task Age</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->deadline_change ?? '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Revision Count</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">{{ $task->due_change_count ?? '' }}</td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task By:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong style="color: blue;">{{ $task->assignedby->first_name ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task To:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong style="color: blue;">{{ $task->assignedto->first_name ?? '' }}</strong></td>
        </tr> 

    </table>

    <p>
        Please make sure the above task is completed on or before the revised Due date.
    </p>
</div>

@endsection
