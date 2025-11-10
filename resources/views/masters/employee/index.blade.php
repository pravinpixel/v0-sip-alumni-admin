@extends('layouts.index')

@section('title', 'Alumni List')

@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading text-dark fw-bold fs-3">Alumni</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ url('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item"> <span class="bullet bg-gray-400 w-5px h-2px"></span> </li>
                        <li class="breadcrumb-item text-muted">Alumni</li>
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ url('/admin/alumni/create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Add Alumni
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="card">
                    <div class="card-body pt-5">

                        <table class="table table-bordered table-striped align-middle">
                            <thead class="text-gray-600 fw-bold">
                                <tr>
                                    <th>Created On</th>
                                    <th>Profile Picture</th>
                                    <th>Name</th>
                                    <th>Year</th>
                                    <th>City & State</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Occupation</th>
                                    <th>Status</th>
                                    <th>Connections</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>


                            <tbody>
                                @forelse($alumnis as $alumni)
                                <tr>
                                    <td>{{ $alumni->created_at ?? '—' }}</td>

                                    <td>
                                        @if(!empty($alumni->profile_picture))
                                        <img src="{{ asset('uploads/alumni/'.$alumni->profile_picture) }}" width="45" height="45" class="rounded-circle">
                                        @else
                                        —
                                        @endif
                                    </td>

                                    <td>{{ $alumni->full_name ?? '—' }}</td>

                                    <td>{{ $alumni->year_of_completion ?? '—' }}</td>

                                    <td>
                                        @if($alumni->city && $alumni->city->state)
                                        {{ $alumni->city->name }} , {{ $alumni->city->state->name }}
                                        @else
                                        —
                                        @endif
                                    </td>

                                    <td>{{ $alumni->email ?? '—' }}</td>

                                    <td>{{ $alumni->mobile_number ?? '—' }}</td>

                                    <td>
                                        @if($alumni->occupation)
                                        {{ $alumni->occupation->name }}
                                        @else
                                        —
                                        @endif
                                    </td>

                                    <td>
                                        @if($alumni->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $alumni->connections_count ?? '—' }}
                                    </td>

                                    <td>
                                        <a href="{{ url('/admin/alumni/edit/'.$alumni->id) }}" class="btn btn-sm btn-light-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <button onclick="deleteAlumni({{ $alumni->id }})"
                                            class="btn btn-sm btn-light-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">No records found</td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>

                        <div class="mt-3">
                            {{ $alumnis->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

<script>
    function deleteAlumni(id) {
        if (!confirm("Are you sure you want to delete?")) return;

        $.ajax({
            url: '/admin/alumni/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                location.reload();
            }
        });
    }
</script>

@endsection