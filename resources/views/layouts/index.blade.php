<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title', config('app.name') )</title>
    <meta charset="utf-8" />
    <meta name="description" content="SIP Academy" />
    <meta name="keywords" content="admin" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="SIP Academy" />
    <meta property="og:site_name" content="SIP Academy Alumni" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo/favicon.png') }}">
    @section('style')
    <link rel="stylesheet" href="{{ asset('plugins/global/plugins.bundle.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.bundle.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css"  />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    @show
    @stack('styles')
</head>
<style>

.modal-content {
    padding: 20px;
}

.modal-header{
    padding: 0 !important;
}
</style>


<body id="kt_app_body" data-kt-app-layout="light-sidebar" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            @include('layouts.header')
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                @include('layouts.sidebar')
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div id="kt_app_content" class="app-content flex-column-fluid">
                        <div id="kt_app_content_container" class="app-container container-fluid" style = "padding-top:80px;">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<div class="modal fade" id="globalConfirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header border-0">
        <h5 class="modal-title" style="font-size: 24px;">Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" style="padding-left: 0; font-size: 14px;">
        <p id="globalConfirmMessage">Are you sure?</p>
      </div>

      <div class="modal-footer border-0" style="padding: 0;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-danger" id="globalConfirmYes">Yes</button>
      </div>

    </div>
  </div>
</div>

<script>
    // GLOBAL CONFIRM FUNCTION — usable in ALL PAGES
    function confirmBox(message, callback) {

        $("#globalConfirmMessage").text(message);

        // remove old click events
        $("#globalConfirmYes").off('click');

        // add new callback
        $("#globalConfirmYes").on('click', function () {
            callback();
            $("#globalConfirmModal").modal('hide');
        });

        // show modal
        $("#globalConfirmModal").modal('show');
    }
</script>

    @if($message = Session::get('success'))
    <script type="text/javascript">
        toastr.success("{{ $message }}");
    </script>
    @endif
    @show
    @stack('scripts')
</body>

</html>