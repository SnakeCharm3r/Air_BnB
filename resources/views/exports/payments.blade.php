@extends('exports.xml-layout')

@php
function xe($v) { return htmlspecialchars((string) $v, ENT_XML1 | ENT_COMPAT, 'UTF-8'); }
@endphp

@section('worksheets')
<Worksheet ss:Name="Payments">
    <Table>
        <Column ss:Width="110"/>
        <Column ss:Width="180"/>
        <Column ss:Width="120"/>
        <Column ss:Width="140"/>
        <Column ss:Width="110"/>
        <Column ss:Width="110"/>

        <Row>
            <Cell ss:StyleID="HotelName" ss:MergeAcross="5"><Data ss:Type="String">{!! xe($hotelName) !!}</Data></Cell>
        </Row>
        @if($hotelAddress)
        <Row>
            <Cell ss:StyleID="HotelAddress" ss:MergeAcross="5"><Data ss:Type="String">{!! xe($hotelAddress) !!}</Data></Cell>
        </Row>
        @endif
        @if($hotelPhone || $hotelEmail)
        <Row>
            <Cell ss:StyleID="HotelAddress" ss:MergeAcross="5"><Data ss:Type="String">{!! xe(implode(' | ', array_filter([$hotelPhone, $hotelEmail]))) !!}</Data></Cell>
        </Row>
        @endif
        <Row>
            <Cell ss:StyleID="SectionTitle" ss:MergeAcross="5"><Data ss:Type="String">Payments</Data></Cell>
        </Row>
        <Row>
            <Cell ss:StyleID="GeneratedAt" ss:MergeAcross="5"><Data ss:Type="String">Generated: {!! xe($generatedAt) !!}</Data></Cell>
        </Row>
        <Row/>

        <Row>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Date</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Guest</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Method</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Receipt #</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Status</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Amount</Data></Cell>
        </Row>
        @forelse($payments as $payment)
        <Row>
            <Cell ss:StyleID="Date"><Data ss:Type="String">{!! xe(date('Y-m-d', strtotime($payment->payment_date ?? $payment->created_at))) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($payment->booking?->guest_name ?? '-') !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe(ucfirst(str_replace('_', ' ', $payment->payment_method))) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($payment->receipt_number ?? 'N/A') !!}</Data></Cell>
            <Cell ss:StyleID="Status"><Data ss:Type="String">{!! xe($payment->is_void ? 'Void' : ucfirst($payment->payment_status)) !!}</Data></Cell>
            <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($payment->amount, 2, '.', '') }}</Data></Cell>
        </Row>
        @empty
        <Row>
            <Cell ss:StyleID="Text" ss:MergeAcross="5"><Data ss:Type="String">No payments found</Data></Cell>
        </Row>
        @endforelse
    </Table>
</Worksheet>
@endsection
