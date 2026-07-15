@extends('exports.xml-layout')

@php
function xe($v) { return htmlspecialchars((string) $v, ENT_XML1 | ENT_COMPAT, 'UTF-8'); }
@endphp

@section('worksheets')
<Worksheet ss:Name="Receipts">
    <Table>
        <Column ss:Width="130"/>
        <Column ss:Width="180"/>
        <Column ss:Width="120"/>
        <Column ss:Width="110"/>
        <Column ss:Width="120"/>

        <Row>
            <Cell ss:StyleID="HotelName" ss:MergeAcross="4"><Data ss:Type="String">{!! xe($hotelName) !!}</Data></Cell>
        </Row>
        @if($hotelAddress)
        <Row>
            <Cell ss:StyleID="HotelAddress" ss:MergeAcross="4"><Data ss:Type="String">{!! xe($hotelAddress) !!}</Data></Cell>
        </Row>
        @endif
        @if($hotelPhone || $hotelEmail)
        <Row>
            <Cell ss:StyleID="HotelAddress" ss:MergeAcross="4"><Data ss:Type="String">{!! xe(implode(' | ', array_filter([$hotelPhone, $hotelEmail]))) !!}</Data></Cell>
        </Row>
        @endif
        <Row>
            <Cell ss:StyleID="SectionTitle" ss:MergeAcross="4"><Data ss:Type="String">Receipts</Data></Cell>
        </Row>
        <Row>
            <Cell ss:StyleID="GeneratedAt" ss:MergeAcross="4"><Data ss:Type="String">Generated: {!! xe($generatedAt) !!}</Data></Cell>
        </Row>
        <Row/>

        <Row>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Receipt #</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Guest</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Method</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Amount</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Date</Data></Cell>
        </Row>
        @forelse($receipts as $payment)
        <Row>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($payment->receipt_number) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($payment->booking?->guest_name ?? '-') !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe(ucfirst(str_replace('_', ' ', $payment->payment_method))) !!}</Data></Cell>
            <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($payment->amount, 2, '.', '') }}</Data></Cell>
            <Cell ss:StyleID="Date"><Data ss:Type="String">{!! xe(date('Y-m-d', strtotime($payment->payment_date ?? $payment->created_at))) !!}</Data></Cell>
        </Row>
        @empty
        <Row>
            <Cell ss:StyleID="Text" ss:MergeAcross="4"><Data ss:Type="String">No receipts found</Data></Cell>
        </Row>
        @endforelse
    </Table>
</Worksheet>
@endsection
