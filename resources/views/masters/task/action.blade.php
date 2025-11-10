@extends('layouts.index')

@section('title', 'Task Master | Usha Fire')
@section('style')
@parent
<style>
    .start {
        border: 1px solid #e0e0de;
        border-radius: 2%;
        height: 100%;
    }

    .pdfbox {
        list-style: none;
        display: flex;
        gap: 10px;
        border: 1px solid #e0e0de;
        padding: 2%;
        border-radius: 2%;
        width: 66%;
    }
</style>
@endsection


@section('content')

<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <!--begin::Title-->
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Task View</h1>
            <!--end::Title-->
            <!--begin::Breadcrumb-->
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <!--begin::Item-->
                <li class="breadcrumb-item text-muted">
                    <a href="{{url('dashboard')}}" class="text-muted text-hover-primary">Dashbord</a>
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{url('task')}}" class="text-muted text-hover-primary">Tasks</a>
                </li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <!--begin::Filter menu-->
            <div class="m-0">
                <!--begin::Menu toggle-->
                <!--end::Menu 1-->
            </div>
            <!--end::Filter menu-->
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Toolbar container-->
</div>
<!--end::Toolbar-->
<div class="card mb-5 mb-xl-8">
    <!-- Back Arrow -->
    <!--begin::Header-->
    <div style="display: flex;align-items:baseline;margin-left: 28px;">
        <a href="{{url('task')}}" style="cursor: pointer;" class="text-muted text-hover-primary">
            <i class="fa fa-arrow-left"  aria-hidden="true"></i>
        </a>
        
        <h3 style="margin-left: 10px;" class="card-title align-items-start flex-column pt-6">
        <center> <span class="card-label fw-bold fs-3 mb-1">Task View </span></center>
        </h3>
    </div>
    
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body py-3">
        <!--begin::Table container-->
        <div class="row mt-5">

            <div class="col-md-3">
                <label class="">Task Name</label>
                <input readonly type="text" class="form-control" value="{{ $user->name ?? old('name') }}" /> <br>

                <label class="">Task Subject</label>
                <textarea readonly class="form-control">{{ $user->description ?? old('description') }}</textarea> <br>

                <label class="">Task Due Date</label>
                <input readonly type="text" class="form-control" value="{{ $user->deadline ? \Carbon\Carbon::parse($user->deadline)->format('d/m/Y') : old('deadline') }}" /> <br>

                <label class="">Task Followers</label>
                <input readonly type="text" class="form-control" value="{{ $user->task_followers_names ?? old('followers') }}" /> <br>
                <label class="">Task Details</label>
                <input readonly type="text" class="form-control" value="{{ $user->name ?? old('name') }}" /> <br>

            </div>
            <div class="col-md-3">
                <label class="">Task Priority</label>
                <input readonly type="text" class="form-control" value="{{ $user->priority->name ?? old('priority') }}" /> <br>

                <label class="">Task Assigned To</label>
                
                <input readonly type="text" class="form-control" value="{{ $user->assignedto->name ?? old('assigned_to') }}" /> <br> <br>

                <label class="">Task Assigned By</label>
                <input readonly type="text" class="form-control" value="{{ $user->assignedby->name ?? old('name') }}" /> <br>
                
                <label class="">Task Additional Followers</label>
                <input readonly type="text" class="form-control" value="{{ $user->additional_followers ?? old('additional_followers') }}" /> <br>

            </div>

            <div class="col-md-6">
                <div class="start">
                    <!--begin::Card body-->
                    <div class="card-body" id="kt_drawer_chat_messenger_body">
                        <!--begin::Messages-->
                        <div class="scroll-y me-n5 pe-5" data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_drawer_chat_messenger_footer" data-kt-scroll-wrappers="#kt_drawer_chat_messenger_body" data-kt-scroll-offset="0px">
                            @foreach ($user->comments as $comment)
                            @if($user->created_by == $comment->from_id)
                            <!--begin::Message(in)-->
                            <div class="d-flex justify-content-start mb-10">
                                <div class="d-flex flex-column align-items-start">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="symbol symbol-30px symbol-circle">
                                        <img alt="Pic" src="{{ $user->assignedby->profile_image ? url($user->assignedby->profile_image) :  asset('images/avatar/blank.png') }}" />
                                        </div>
                                        <div class="ms-3">
                                            <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">{{ $user->assignedby->name ?? '' }}</a>
                                            <span class="text-muted fs-7 mb-1">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start">
                                        <p>{{ $comment->comment }}</p>
                                        @if($comment->documents->isNotEmpty())
                                        <ul style="list-style: none; margin-left:-9%;">
                                            @foreach ($comment->documents as $document)
                                            <li>
                                                <a href="{{ $document->document }}" target="_blank"><img src="{{asset('images/logo/pdf.webp')}}" alt="Document" class="h-35px"></a>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!--end::Message(in)-->
                            @else

                            <!--begin::Message(out)-->
                            <div class="d-flex justify-content-end mb-10">
                                <div class="d-flex flex-column align-items-end">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3">
                                            <span class="text-muted fs-7 mb-1">{{ $comment->created_at->diffForHumans() }}</span>
                                            <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1">{{ $user->assignedto->name ?? '' }}</a>
                                        </div>
                                        <div class="symbol symbol-30px symbol-circle">
                                        <img alt="Pic" src="{{ $user->assignedto->profile_image ? url($user->assignedto->profile_image) :  asset('images/avatar/blank.png') }}" />
                                        </div>
                                    </div>

                                    <div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end" data-kt-element="message-text">
                                        <p>{{ $comment->comment }}</p>
                                        @if($comment->documents->isNotEmpty())
                                        <ul style="list-style: none;">
                                            @foreach ($comment->documents as $document)
                                            <li>
                                                <a href="{{ $document->document }}" target="_blank"><img src="{{asset('images/logo/pdf.webp')}}" alt="Document" class="h-35px"></a>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <!--end::Message(out)-->
                            @endif
                            @endforeach
                        </div>
                        <!--end::Messages-->

                    </div>
                    <!--end::Card body-->
                </div>

            </div>
        </div>
        <!--end::Table container-->

        <div class="row mt-5">
        @if($user->is_recurrence == 1)
            <div class="col-sm-6"> 
                
                <h3>Recurrence Details</h3>
                <label class="">Start Date</label>
                <input readonly type="text" class="form-control" value="{{ $user['recurrence_details']['startDate'] ?? old('startDate') }}" /> <br>

                @if($user['recurrence_details']['endDate'] != null)
                <label class="">End Date</label>
                <input readonly type="text" class="form-control" value="{{ $user['recurrence_details']['endDate'] ?? old('endDate') }}" /> <br>
                @else
                <label class="">End Date</label>
                <input readonly type="text" class="form-control" value="Never" /> <br>
                @endif

                @php
                    $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $days = $user['recurrence_details']['days'] ?? [];
                @endphp

                @if(!empty(array_intersect($days, $validDays)))
                    <label class="">Days</label>
                    <input readonly type="text" class="form-control" 
                        value="{{ implode(', ', $days) }}" />
                @else
                    <label class="">Occurrence</label>
                    <input readonly type="text" class="form-control" 
                        value="{{ $user['recurrence_details']['days'][0] ?? old('days') }}" />
                @endif
                <br>

                <label class="">Recurrence Type</label>
                <input readonly type="text" class="form-control" value="{{ $user['recurrence_details']['recurrenceType'] ?? old('recurrenceType') }}" /> <br>
                
            </div>
        @endif
            <div class="col-sm-6">
                <br><br>
                <label class="">Attachment</label>
                    <ul class="pdfbox" style="list-style: none; display: flex; gap: 10px;">
                        @forelse($user->documents as $document)
                        @php
                        $extension = pathinfo($document->document, PATHINFO_EXTENSION);
                        $icon =asset('images/logo/default.png');
                        switch($extension) {
                        case 'pdf':
                        $icon = asset('images/logo/pdf.webp');
                        break;
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                        $icon = $document->document;
                        break;
                        }
                        @endphp
                        <li>
                            <a href="{{ $document->document }}" target="_blank">
                                <img src="{{$icon}}" alt="Document" class="h-45px">
                            </a>
                        </li>
                        @empty
                        <li style="color: red;">No documents available</li>
                        @endforelse
                    </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@parent
@endsection