@extends('emails.index')

@section('style')
    @parent
    <style>

    </style>
@endsection


@section('content')
    <div>
        <p>Hi {{$task->assignedto->first_name}},</p>
        @if($task->task_rating == 5)
            <p>🤩Congratulations🤩</p>
            <p>
                I am thrilled to inform you that you have earned a 5-star rating ⭐ ⭐ ⭐ ⭐ ⭐ for your on
                this task.
            </p>
            <p>Keep up the excellent work moving forward.</p>
        @elseif($task->task_rating == 4)
            <p>
                I am happy to inform you that you have earned a 4-star rating ⭐ ⭐ ⭐ ⭐ for your on this
                task 🙂.
            </p>
            <p>
                Try aiming for a 5 Star rating. You can do it!
            </p>
        @elseif($task->task_rating == 3)
            <p>You have earned a 3-star rating ⭐ ⭐ ⭐ for this task 😐.</p>
            <p>Try aiming for a 5 Star rating. You can do it!!</p>

        @elseif($task->task_rating == 2)
            <p>
                I am saddened to inform you have earned a 2-star rating ⭐ ⭐ for this task ☹️.
            </p>
            <p>
                Please feel free to talk to the task assigner to understand what must be done to improve your
                star rating.
            </p>
        @elseif($task->task_rating == 1)
            <p>I am heartbroken to inform you have earned a 1-star rating ⭐ for this task 😢.</p>
            <p>
                Please feel free to talk to the task assigner to understand what must be done to improve your
                star rating.
            </p>
        @endif

    </div>

@endsection
