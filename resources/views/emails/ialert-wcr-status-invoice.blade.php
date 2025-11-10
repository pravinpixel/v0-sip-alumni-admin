@extends('emails.ialert-index')

@section('style')
@parent
<style>

</style>
@endsection


@section('content')
<div>

    <p><b>Body Subject:</b> <span style='color: #980000;'>Update required on WCR Status for Invoice [{{ $invoice->invoice_number ?? '' }}]</span></p>

    <p>Hi Logistic Team,</p>
    <p>
        This is a gentle reminder to provide an update on the WCR status for the invoice mentioned below.
    </p>


    <table style="border-collapse: collapse;">
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice Age</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: #ff0000;">{{ $invoice->age ?? '' }}</strong></td>
        </tr>
        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice No.</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: #0a0aff;">{{ $invoice->invoice_number ?? '' }}</strong></td>
        </tr>

        <tr>
            <td style="border: 2px solid black;padding: 8px;text-align: left;">Company Name</td>
            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                    style="color: #0a0aff;">{{ $invoice->customer_name ?? '' }}</strong></td>
        </tr>


{{--        <tr>--}}
{{--            <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice Link</td>--}}
{{--            <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong--}}
{{--                    style="color: #0a0aff;"><a--}}
{{--                    href="{{ config('app.task_url').'/task?invoice_id='.($invoice->id ?? '')}}">{{ $invoice->invoice_number ?? '' }}</a></strong></td>--}}
{{--        </tr>--}}

                <tr>
                    <td style="border: 2px solid black;padding: 8px;text-align: left;">Invoice Link</td>
                    <td style="border: 2px solid black;padding: 8px;text-align: left;"><strong
                            style="color: #0a0aff;">
                            @if($invoice->invoice_pdf)
                                <a href="{{$invoice->invoice_pdf}}" target="_blank">&#128196;</a>
                            @endif
                        </strong></td>
                </tr>

    </table>
    <p>
        <strong style="color: #38761d;">If the work has been completed, </strong>please ensure that the WCR is attached to the SAP/ i Alert tool at the earliest.
    </p>
    <p>
        <strong style="color: #ff0000;">If the work is still pending, </strong>kindly share the revised due date comment in the follow-up tool, ensuring it falls within a 7-day window.
    </p>
</div>

@endsection
