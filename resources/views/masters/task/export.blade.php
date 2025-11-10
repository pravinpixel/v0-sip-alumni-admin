<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<center>
<form action="{{ route('tasks.export') }}" method="GET" style="margin-top: 10%;">
    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" id="start_date">

    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" id="end_date">

    <button type="submit" class="btn btn-primary">Export Tasks to Excel</button>
</form>
</center>
</body>
</html>