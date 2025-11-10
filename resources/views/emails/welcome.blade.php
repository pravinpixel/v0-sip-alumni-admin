@extends('emails.index')

@section('style')
    @parent
    <style>

       li {
            margin-bottom: 10px;
            padding: 5px;
        }

    </style>
@endsection

@section('content')
    <div>
        <p>Hi {{'$task->name'}} 👋,</p>
        <p>
            Welcome to Task Master, your ultimate task management tool!!
        </p>
        <p>
            🚀📈 We’re thrilled to have you with us. Your Task Master login credentials are given below.
        </p>
        <br>
        <br>
        <hr>
        <p>Login :-</p>
        <p>Password :-</p>
        <hr>
        <br>
        <p>
            Here’s a quick guide to get you started:
        </p>
        <ol>
            <li>📝 Create Tasks with Ease: Set up tasks with just a few clicks. Add deadlines, priorities, and
                notes to keep everything on track.
            </li>
            <li>🔔 Never Miss a Deadline: Set Due Date reminders so you never miss a task.</li>
            <li>🌐 Collaborate with Ease: Share tasks with your team. Work together seamlessly and
                communicate effectively.
            </li>
            <li>⭐️ Task Rating: On the completion of a task you will have a power to give a star rating to the
                task assignee.
            </li>
        </ol>
        <p>
            Need help? Our support team is here for you. Just reach out through the email on
            enquiry@ushafire.in
        </p>
        <p>
            😁 Happy Tasking
        </p>

    </div>

@endsection
