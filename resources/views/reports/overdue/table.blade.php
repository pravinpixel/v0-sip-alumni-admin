@if($tasks->isEmpty())
    <tr>
        <td colspan="6" class="text-center">No results found.</td>
    </tr>
@else
    @foreach($tasks as $task)
        <tr>
            <th>Branch</th>
            <th>Employee ID</th>
            <th>Assigned By</th>
            <th>Assignee<br/>Emp ID</th>
            <th>Assigned<br/>To</th>
            <th>Task ID</th>
            <th>Subject</th>
            <th>Details</th>
            <th>Type</th>
            <th>Priority</th>
            <th>Followers</th>
            <th>Additional Followers</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Recurrence</th>
            <th>Age</th>
            <th>Revision Count</th>
            <th>Documents</th>
            <th>Rating</th>
            <th>Rating Remarks</th>
        </tr>
    @endforeach
@endif
