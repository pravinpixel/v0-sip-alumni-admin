@extends('emails.ialert-index')

@section('style')
@parent
<style>

</style>
@endsection


@section('content')
<div>

    <p><b>Body Subject:</b> <span style='color: #980000;'>Management Payment commitment Due for Invoice [{{ $invoice->invoice_number ?? '' }}]</span></p>

    <p>Dear Management Team,</p>
    <p>
        Our team has not yet received the payment for the invoice mentioned below.
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

        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">O/s Value:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: #0a0aff;"><b>{{ $invoice->os_value ?? '' }}</b></strong></td>
        </tr>

        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Age:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: #ff0000;"><b>{{ $invoice->age ?? '' }}</b></strong></td>
        </tr>

{{--        <tr>--}}
{{--            <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice Link:-</td>--}}
{{--            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong--}}
{{--                    style="color: #0a0aff;"><a--}}
{{--                    href="{{ config('app.task_url').'/task?invoice_id='.($invoice->id ?? '')}}">{{ $invoice->invoice_number ?? '' }}</a></strong></td>--}}
{{--        </tr>--}}

{{--        <tr>--}}
{{--            <td style="border: 2px solid black;padding: 8px;text-align: left;">Work Completion Attachment:-</td>--}}
{{--            <td style="border: 2px solid black;padding: 8px;text-align: left;">--}}
{{--                @foreach ($invoice->documents as $document)--}}
{{--                <a href="{{ $document->document }}" target="_blank">--}}
{{--                    &#128196;--}}
{{--                </a>--}}
{{--                @endforeach--}}
{{--            </td>--}}
{{--        </tr>--}}


        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice Link:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: #0a0aff;">
                    @if($invoice->invoice_pdf)
                        <a href="{{$invoice->invoice_pdf}}" target="_blank">&#128196;</a>
                    @endif
                </strong>
            </td>
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


        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Payment Commitment Revision Count:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: #0a0aff;"><b>{{ $invoice->payment_revision_count ?? '' }}</b></strong></td>
        </tr>

        <tr style="background-color:#ffff00">
            <td style="border: 2px solid black;padding: 8px;text-align: left;background-color: #ffff00;" colspan="2"></td>
        </tr>

        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;background-color: #ffff00;">Usha Fire Team Comment:-</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: #0a0aff;">{{ $invoice->remark_for_email ?? '' }}</strong></td>
        </tr>

    </table>
</div>

@endsection
