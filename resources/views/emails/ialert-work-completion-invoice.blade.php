@extends('emails.ialert-index')

@section('style')
    @parent
    <style>

    </style>
@endsection


@section('content')
    <div>

        <p><b>Body Subject:</b> <span style='color: #38761d;'>Work Completion update  for Invoice [{{ $invoice->invoice_number ?? '' }}]</span>
        </p>

        <p>Dear Customer,</p>
        <p>
            We are pleased to inform you that our team has successfully completed the work associated with the following
            invoice.
        </p>


        <table style="border-collapse: collapse;">
            <tr>
                <td style="border: 2px solid black;padding: 8px;text-align: left;">Company Name</td>
                <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                        style="color: #0a0aff;">{{ $invoice->customer_name ?? '' }}</strong></td>
            </tr>
            <tr>
                <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice No.</td>
                <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                        style="color: #0a0aff;">{{ $invoice->invoice_number ?? '' }}</strong></td>
            </tr>

            <tr>
                <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice Value:-</td>
                <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                        style="color: #0a0aff;">{{ $invoice->invoice_value ?? '' }}</strong></td>
            </tr>

{{--            <tr>--}}
{{--                <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice Link:-</td>--}}
{{--                <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong--}}
{{--                        style="color: #0a0aff;"><a--}}
{{--                            href="{{ config('app.task_url').'/task?invoice_id='.($invoice->id ?? '')}}">{{ $invoice->invoice_number ?? '' }}</a>--}}
{{--                    </strong></td>--}}
{{--            </tr>--}}

{{--            <tr>--}}
{{--                <td style="border: 2px solid black;padding: 8px;text-align: left;">Work Completion Attachment:-</td>--}}
{{--                <td style="border: 2px solid black;padding: 8px;text-align: left;">--}}
{{--                    @foreach ($invoice->documents as $document)--}}
{{--                        <a href="{{ $document->document }}" target="_blank">--}}
{{--                            &#128196;--}}
{{--                        </a>--}}
{{--                    @endforeach--}}
{{--                </td>--}}
{{--            </tr>--}}


            <tr>
                <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice Link:-</td>
                <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                        style="color: #0a0aff;">
                        @if($invoice->invoice_pdf)
                            <a href="{{$invoice->invoice_pdf}}" target="_blank">&#128196;</a>
                        @endif
                    </strong></td>
            </tr>

            <tr>
                <td style="border: 2px solid black;padding: 8px;text-align: left;">Work Completion Attachment:-</td>
                <td style="border: 2px solid black;padding: 8px;text-align: left;">

                    @if($invoice->sap_attachments)
                        @php
                            $attachments = explode(',', $invoice->sap_attachments);
                        @endphp
                        @foreach ($attachments as $attachment)
                            <a href="{{ $attachment }}" target="_blank">
                                &#128196;
                            </a>
                        @endforeach
                    @endif
                </td>
            </tr>


        </table>
        <p style="color: #ff0000;">If any work is pending please do not hesitate to escalate the matter by replying to
            this email.</p>
    </div>

@endsection
