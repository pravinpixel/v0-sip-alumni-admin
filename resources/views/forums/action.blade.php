@extends('layouts.index')

@section('title', 'Task Master | Usha Fire')
@section('style')
@parent
@endsection

<style>
    .action_td {
        display: flex;
    }

    .input-group {
        position: relative;
        width: 100%;
    }

    .input-group .form-control {
        width: 100%;
        padding-right: 40px;
        /* Adjust to leave space for the icon */
    }

    .input-group .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        /* Adjust as needed */
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 1.2em;
        /* Adjust for icon size */
        z-index: 999;
    }

    #profile-img {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        overflow-clip-margin: 0 !important;
    }

    /* .contact_master_row {
        transition: opacity 0.7s ease-out, transform 0.5s ease-out;
        opacity: 1;
        transform: scale(1);
    } */







    @media (max-width: 768px) {
        .input-group .toggle-password {
            font-size: 1em;
            /* Adjust size for smaller screens */
            right: 5px;
            /* Adjust for smaller screens */
        }
    }

    @media (max-width: 480px) {
        .input-group .toggle-password {
            font-size: 0.9em;
            /* Further adjust for very small screens */
            right: 3px;
            /* Adjust as needed */
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

@section('content')
<div id="pageLoader" class="page-loader">
    <div class="loader"></div>
</div>

<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <!--begin::Title-->
            @if(isset($organization))
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Edit Organization</h1>
            @else
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Add New Organization</h1>
            @endif
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
                    <a href="{{url('organization')}}" class="text-muted text-hover-primary">Organization</a>
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
    <!--begin::Header-->
    <div class="card-header border-0 pt-5 card_arrow">
        <a href="{{url('organization')}}" style="cursor: pointer;" class="text-muted text-hover-primary">
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
        </a>
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Organization</span>
        </h3>
        <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" title="Click to add a employee">
        </div>
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body py-3">
        <!--begin::Table container-->
        <form method="post" id="dynamic-form" method="post" action="{{ url('/organization/save') }}" class="form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="id" value="{{$organization->id??''}}">
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="customer_code">Customer Code</label>
                        <input type="text" name="customer_code" placeholder="Customer Code" class=" form-control" value="{{ $organization->customer_code ?? old('customer_code') }}" />
                        <span class="field-error" id="customer_code-error" style="color:red"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="company_name">Company Name</label>
                        <input type="text" name="company_name" placeholder="Company Name" class=" form-control" value="{{ $organization->company_name ?? old('company_name') }}" />
                        <span class="field-error" id="company_name-error" style="color:red"></span>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <label  for="address">Address</label>
                        <input type="text" name="address" placeholder="Address" class=" form-control" value="{{ $organization->address ?? old('address') }}" />
                        <span class="field-error" id="address-error" style="color:red"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="status" >Location</label>
                    <select data-control="select2" data-hide-search="false" name="location_id" class="form-control" value="{{ $organization->location ?? old('location') }}" data-placeholder="Choose Location">
                        <option></option>
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ isset($organization) && $location->id == old('location_id', $organization->location_id) ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                    <span class="field-error" id="location_id-error" style="color:red"></span>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="primary_mail_id_1">Primary Contact Mail ID 1</label>
                        <input type="email" name="primary_mail_id_1" placeholder="Primary Contact Mail ID 1" class="form-control" value="{{ $organization->primary_mail_id1 ?? old('primary_mail_id1') }}" />
                        <span class="field-error" id="primary_mail_id_1-error" style="color:red"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="primary_mail_id_2">Primary Contact Mail ID 2</label>
                        <input type="email" name="primary_mail_id_2" placeholder="Primary Contact Mail ID 2" class="form-control" value="{{ $organization->primary_mail_id2 ?? old('primary_mail_id2') }}" />
                        <span class="field-error" id="primary_mail_id_2-error" style="color:red"></span>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <label  for="primary_contact_no_1">Primary Contact Phone no 1</label>
                        <input type="text" name="primary_contact_no_1" placeholder="Primary Contact Phone no 1" class="form-control phone-number" value="{{ $organization->primary_phone1 ?? old('primary_phone1') }}" maxlength="10" />
                        <span class="field-error" id="primary_contact_no_1-error" style="color:red"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label  for="primary_contact_no_2">Primary Contact Phone no 2</label>
                        <input type="text" name="primary_contact_no_2" placeholder="Primary Contact Phone no 2" class="form-control phone-number" value="{{ $organization->primary_phone2 ?? old('primary_phone2') }}" maxlength="10" />
                        <span class="field-error" id="primary_contact_no_2-error" style="color:red"></span>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="primary_contact_name_1">Primary Contact Name 1</label>
                        <input type="text" name="primary_contact_name_1" placeholder="Primary Contact Name 1" class="form-control" value="{{ $organization->primary_name1 ?? old('primary_name1') }}" />
                        <span class="field-error" id="primary_contact_name_1-error" style="color:red"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="primary_contact_name_2">Primary Contact Name 2</label>
                        <input type="text" name="primary_contact_name_2" placeholder="Primary Contact Name 2" class="form-control" value="{{ $organization->primary_name2 ?? old('primary_name2') }}" />
                        <span class="field-error" id="primary_contact_name_2-error" style="color:red"></span>
                    </div>
                </div>
            </div>

            <div class="contact_master_section row mt-5">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Contact Master</h1>
                <div class="contact_master_section_content">
                    <!-- <button type="button" class="btn btn-success add_contact_master">Add</button> -->
                    @if(isset($organization->organizationContacts) && count($organization->organizationContacts) > 0)
                    @foreach($organization->organizationContacts as $key => $contact)
                    <div class="contact_master_row">
                        <div class="row">
                            <input type="hidden" name="contact_master_id[]" value="{{ $contact->id }}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Name</label>
                                    <input type="text" name="contact_master_name[]" placeholder="Name"
                                        class="required form-control" value="{{ $contact->name }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Email ID</label>
                                    <input type="email" name="contact_master_mail_id[]" placeholder="Email ID"
                                        class="required form-control" value="{{ $contact->email_id }}" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Phone Number</label>
                                    <input type="text" name="contact_master_phone_no[]" placeholder="Phone Number"
                                        class="required form-control phone-number" value="{{ $contact->phone_number }}" maxlength="10" />
                                </div>
                            </div>
                            <div class="col-md-3" style="padding-top: 18px;">
                                <button type="button" class="btn btn-danger remove_contact_master">X</button>
                                <button style="display: none;" type="button" class="btn btn-success add_contact_master">+</i>
                                </button>

                            </div>

                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="contact_master_row">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Name</label>
                                    <input type="text" name="contact_master_name[]" placeholder="Name" class="required form-control" value="" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Email ID</label>
                                    <input type="email" name="contact_master_mail_id[]" placeholder="Email ID" class="required form-control" value="" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Phone Number</label>
                                    <input type="text" name="contact_master_phone_no[]" placeholder="Phone Number" class="required form-control phone-number" value="" maxlength="10" />
                                </div>
                            </div>
                            <div class="col-md-3" style="padding-top: 18px;">
                                <button style="display: none;" type="button" class="btn btn-danger remove_contact_master default_remove">X</button>
                                <button type="button" class="btn btn-success add_contact_master">+</i>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="row mt-10">
                <!-- <div class="col-md-4"></div> -->
                <div class="col-md-12">
                    <center>
                        <button type="button" class="btn btn-primary" id="dynamic-submit">Submit</button>
                        <button type="button" class="btn btn-primary" id="clear">Clear</button>
                    </center>
                </div>
                <!-- <div class="col-md-4"></div> -->
            </div>
            <br>
        </form>
        <!--end::Table container-->
    </div>
</div>
@endsection

@section('script')

@parent
<script>
    document.getElementById('clear').addEventListener('click', function() {
        const form = document.getElementById('dynamic-form');
        form.reset();
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            select.value = '';
            $(select).trigger('change');
        });
    });

    $(document).ready(function() {
        // Restrict input to numbers only for phone number fields
        $(document).on('keypress', '.phone-number', function(event) {
            // Allow only numbers (0-9) and control keys (backspace, delete, etc.)
            if (event.charCode != 0 && !/\d/.test(String.fromCharCode(event.charCode))) {
                event.preventDefault();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        showAddButtonInLastRow();
        let contactContainer = document.querySelector(".contact_master_section_content");

        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("add_contact_master")) {
                addContactRow();
            }
            if (event.target.classList.contains("remove_contact_master")) {
                removeContactRow(event.target);
            }
        });

        function addContactRow() {
            let firstRow = document.querySelector(".contact_master_row");
            if (!firstRow) return;

            let newRow = firstRow.cloneNode(true);
            let inputs = newRow.querySelectorAll("input");

            // Clear input values
            inputs.forEach(input => {
                input.value = "";
            });
            newRow.querySelectorAll(".field-error, .email-error").forEach(errorSpan => {
                errorSpan.remove();
            });
            let contactContainer = document.querySelector(".contact_master_section_content");
            if (!contactContainer) return;
            // newRow.style.opacity = "0";
            // newRow.style.transform = "scale(0.95)";
            // requestAnimationFrame(() => {
            //     newRow.style.opacity = "1";
            //     newRow.style.transform = "scale(1)";
            // });
            contactContainer.appendChild(newRow);

            showAddButtonInLastRow();
        }


        function removeContactRow(button) {
            let rowToRemove = button.closest(".contact_master_row");
            let rows = document.querySelectorAll(".contact_master_row");

            if (rows.length > 1) {
                rowToRemove.remove();
                showAddButtonInLastRow();
                // rowToRemove.style.opacity = "0";
                // rowToRemove.style.transform = "scale(0.95)";

                // // Wait for the transition to complete before removing the row
                // rowToRemove.addEventListener('transitionend', function() {

                // },{
                //     once: true
                // });
            } else {
                toastr.error("At least one contact master is required.");
            }
        }

        function showAddButtonInLastRow() {
            let rows = document.querySelectorAll(".contact_master_row");

            rows.forEach((row, index) => {
                let addButton = row.querySelector(".add_contact_master");
                let removeButton = row.querySelector(".remove_contact_master");
                if (index === rows.length - 1) {
                    addButton.style.display = "inline-block";
                } else {
                    addButton.style.display = "none";
                }
                removeButton.style.display = (rows.length > 1) ? "inline-block" : "none";
            });
        }

        // Clear Validation Messages on Changes
        // $('input[name="primary_mail_id_1"], input[name="primary_mail_id_2"], input[name="primary_contact_no_1"], input[name="primary_contact_no_2"], input[name="primary_contact_name_1"], input[name="primary_contact_name_2"]').on('input', function() {
        //     $(this).closest('.form-group').find('.field-error').text('');
        // });

        $('input[name="primary_contact_no_1"],input[name="primary_contact_no_2"]').keypress(function(event) {
            if (event.charCode != 0 && !/\d/.test(event.key)) {
                event.preventDefault();
            }
        });

        $('#dynamic-submit').on('click', function(e) {
            e.preventDefault();
            $('#pageLoader').fadeIn();
            if (!validateForm()) {
                $('#pageLoader').fadeOut();
                return;
            } else {
                saveUpdateOrganization();
            }
        });

        function validateForm() {
            let isValid = true;
            $(".field-error, .email-error").remove(); // Remove all previous error messages
            let customerCode = $('input[name="customer_code"]').val().trim();
            let companyName = $('input[name="company_name"]').val().trim();
            let address = $('input[name="address"]').val().trim();
            let location = $('select[name="location_id"]').val().trim();

            if (customerCode === '') {
                showError($('input[name="customer_code"]')[0], "Customer Code is required.");
                isValid = false;
            }

            if (companyName === '') {
                showError($('input[name="company_name"]')[0], "Company Name is required.");
                isValid = false;
            }

            // Validate Emails
            let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            let contact_email1 = $('input[name="primary_mail_id_1"]').val().trim();
            let contact_email2 = $('input[name="primary_mail_id_2"]').val().trim();

            if (contact_email1 !== '' && !emailPattern.test(contact_email1)) {
                showError($('input[name="primary_mail_id_1"]')[0], "Enter a valid email.");
                isValid = false;
            }

            if (contact_email2 !== '' && !emailPattern.test(contact_email2)) {
                showError($('input[name="primary_mail_id_2"]')[0], "Enter a valid email.");
                isValid = false;
            }

            if (contact_email1 !== '' && contact_email2 !== '' && contact_email1 === contact_email2) {
                showError($('input[name="primary_mail_id_2"]')[0], "Primary Emails should not match.");
                isValid = false;
            }

            // Validate Phone Numbers
            let phonePattern = /^\d{10}$/;
            let contact_phone1 = $('input[name="primary_contact_no_1"]').val().trim();
            let contact_phone2 = $('input[name="primary_contact_no_2"]').val().trim();

            if (contact_phone1 !== '' && contact_phone2 !== '' && contact_phone1 === contact_phone2) {
                showError($('input[name="primary_contact_no_2"]')[0], "Primary Contacts should not match.");
                isValid = false;
            }
            if (contact_phone1 !== '' && !phonePattern.test(contact_phone1)) {
                showError($('input[name="primary_contact_no_1"]')[0], "Enter a valid number (10 digits required).");
                isValid = false;
            }

            if (contact_phone2 !== '' && !phonePattern.test(contact_phone2)) {
                showError($('input[name="primary_contact_no_2"]')[0], "Enter a valid number (10 digits required).");
                isValid = false;
            }

            // Validate Primary Contact Names
            let contact_name1 = $('input[name="primary_contact_name_1"]').val().trim();
            let contact_name2 = $('input[name="primary_contact_name_2"]').val().trim();

            if(contact_email1 === '' && contact_name1 !== '') {
                showError($('input[name="primary_mail_id_1"]')[0], "Primary Email 1 is required.");
                isValid = false;
            }

            if(contact_email2 === '' && contact_name2 !== '') {
                showError($('input[name="primary_mail_id_2"]')[0], "Primary Email 2 is required.");
                isValid = false;
            }

            if(contact_name1 === '' && contact_email1 !== '') {
                showError($('input[name="primary_contact_name_1"]')[0], "Primary Contact Name 1 is required.");
                isValid = false;
            }

            if(contact_name2 === '' && contact_email2 !== '') {
                showError($('input[name="primary_contact_name_2"]')[0], "Primary Contact Name 2 is required.");
                isValid = false;
            }


            // Validate Contact Master Fields (Dynamic)
            let emails = [];
            let phoneNumbers = [];
            let primaryEmails = [contact_email1, contact_email2].filter(email => email !== "");
            let primaryPhones = [contact_phone1, contact_phone2].filter(phone => phone !== "");

            document.querySelectorAll('.contact_master_row').forEach(row => {
                let nameInput = row.querySelector('input[name="contact_master_name[]"]');
                let emailInput = row.querySelector('input[name="contact_master_mail_id[]"]');
                let phoneInput = row.querySelector('input[name="contact_master_phone_no[]"]');

                let name = nameInput.value.trim();
                let email = emailInput.value.trim();
                let phone = phoneInput.value.trim();

                if (name != "" || email != "" || phone != "") {
                    if (name === "") {
                        showError(nameInput, "Name is required!");
                        isValid = false;
                    }

                    if (email === "") {
                        showError(emailInput, "Email is required!");
                        isValid = false;
                    } else if (!emailPattern.test(email)) {
                        showError(emailInput, "Enter a valid email.");
                        isValid = false;
                    }

                    if (email !== "") {
                        if (primaryEmails.includes(email)) {
                            showError(emailInput, "Matches a Primary Contact Email!");
                            isValid = false;
                        }
                        if (emails.includes(email.toLowerCase())) {
                            showError(emailInput, "Email should be unique!");
                            isValid = false;
                        }
                        if (isValid) {
                            emails.push(email.toLowerCase());
                        }
                    }

                    if (phone === "") {
                        showError(phoneInput, "Phone Number is required!");
                        isValid = false;
                    } else if (!phonePattern.test(phone)) {
                        showError(phoneInput, "Enter a valid number (10 digits required).");
                        isValid = false;
                    }

                    if (phone !== "") {
                        if (primaryPhones.includes(phone)) {
                            showError(phoneInput, "Phone Number matches a Primary Contact!");
                            isValid = false;
                        }
                        if (phoneNumbers.includes(phone)) {
                            showError(phoneInput, "Phone Number should be unique!");
                            isValid = false;
                        }
                        phoneNumbers.push(phone);
                    }
                }
            });

            return isValid;
        }


        // Function to show error messages next to input fields
        function showError(input, message) {
            let errorSpan = document.createElement("span");
            errorSpan.className = "field-error";
            errorSpan.style.color = "red";
            errorSpan.innerText = message;
            input.parentElement.appendChild(errorSpan);
        }



        function saveUpdateOrganization() {
            let formData = new FormData(document.getElementById('dynamic-form'));
            formData.delete(`contact_master_name[]`);
            formData.delete(`contact_master_mail_id[]`);
            formData.delete(`contact_master_phone_no[]`);
            document.querySelectorAll('.contact_master_row').forEach((row) => {
                let nameInput = row.querySelector('input[name="contact_master_name[]"]');
                let emailInput = row.querySelector('input[name="contact_master_mail_id[]"]');
                let phoneInput = row.querySelector('input[name="contact_master_phone_no[]"]');
                let name = nameInput.value.trim();
                let email = emailInput.value.trim();
                let phone = phoneInput.value.trim();
                if (name != "" || email != "" || phone != "") {
                    formData.append(`contact_master_name[]`, name);
                    formData.append(`contact_master_mail_id[]`, email);
                    formData.append(`contact_master_phone_no[]`, phone);
                }
            });

            // Perform the AJAX request
            $.ajax({
                url: $('#dynamic-form').attr('action'), // Get the form action URL
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token if needed
                },
                success: function(response) {
                    $('#pageLoader').fadeOut();
                    // Handle success response
                    if (response) {
                        toastr.success(response.message)
                        window.location.href = '{{ route("organization.index") }}';
                    }

                },
                error: function(response) {
                    $('#pageLoader').fadeOut();
                    if (response.status === 422 && response.responseJSON.error) {
                        var errors = response.responseJSON.error;

                        // Clear previous error messages
                        $('#dynamic-form').find(".field-error").remove();

                        $.each(errors, function(key, value) {
                            let field = $('input[name="' + key + '"]');
                            if (field.length > 0) {
                                showError(field[0], value[0]);
                            } else {
                                let email = key.match(/^contact_master_mail_id\.(\d+)$/);
                                let phone = key.match(/^contact_master_phone_no\.(\d+)$/);
                                if (email || phone) {
                                    let index = email ? parseInt(email[1]) : parseInt(phone[1]);
                                    let fieldSelector = email ? $('input[name="contact_master_mail_id[]"]').eq(index) : $('input[name="contact_master_phone_no[]"]').eq(index);
                                    if (fieldSelector.length > 0) {
                                        showError(fieldSelector[0], value[0]);
                                    }
                                }
                            }
                        });
                    } else {
                        toastr.error(response.responseJSON.error);
                    }
                }
            });
        }
    });
</script>
@endsection