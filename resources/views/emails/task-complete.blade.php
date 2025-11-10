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
        This task has been <span style='color: green;'><b>COMPLETED.</b></span>
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
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task By:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong style="color: blue;">{{ $task->assignedby->first_name ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Task To:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong style="color: blue;">{{ $task->assignedto->first_name ?? '' }}</strong></td>
        </tr>

    </table>
    <p class="info">
        We would like to thank you from the bottom of our heart for the efforts taken to close this task.
    </p>
</div>

@endsection