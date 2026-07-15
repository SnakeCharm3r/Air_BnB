@extends('exports.xml-layout')

@php
function xe($v) { return htmlspecialchars((string) $v, ENT_XML1 | ENT_COMPAT, 'UTF-8'); }
@endphp

@section('worksheets')
<Worksheet ss:Name="Invoices">
    <Table>
        <Column ss:Width="120"/>
        <Column ss:Width="180"/>
        <Column ss:Width="80"/>
        <Column ss:Width="110"/>
        <Column ss:Width="130"/>
        <Column ss:Width="110"/>
        <Column ss:Width="110"/>
        <Column ss:Width="110"/>

        <Row>
            <Cell ss:StyleID="HotelName" ss:MergeAcross="7"><Data ss:Type="String">{!! xe($hotelName) !!}</Data></Cell>
        </Row>
        @if($hotelAddress)
        <Row>
            <Cell ss:StyleID="HotelAddress" ss:MergeAcross="7"><Data ss:Type="String">{!! xe($hotelAddress) !!}</Data></Cell>
        </Row>
        @endif
        @if($hotelPhone || $hotelEmail)
        <Row>
            <Cell ss:StyleID="HotelAddress" ss:MergeAcross="7"><Data ss:Type="String">{!! xe(implode(' | ', array_filter([$hotelPhone, $hotelEmail]))) !!}</Data></Cell>
        </Row>
        @endif
        <Row>
            <Cell ss:StyleID="SectionTitle" ss:MergeAcross="7"><Data ss:Type="String">Invoices</Data></Cell>
        </Row>
        <Row>
            <Cell ss:StyleID="GeneratedAt" ss:MergeAcross="7"><Data ss:Type="String">Generated: {!! xe($generatedAt) !!}</Data></Cell>
        </Row>
        <Row/>

        <Row>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Invoice #</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Guest</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Room</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Status</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Charges</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Total</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Paid</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Balance</Data></Cell>
        </Row>
        @forelse($invoices as $invoice)
        <Row>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($invoice->invoice_number) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($invoice->guest_name) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($invoice->room_number) !!}</Data></Cell>
            <Cell ss:StyleID="Status"><Data ss:Type="String">{!! xe(ucfirst($invoice->invoice_status)) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($invoice->folio?->charges->pluck('description')->filter()->implode(', ') ?? '-') !!}</Data></Cell>
            <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($invoice->grand_total, 2, '.', '') }}</Data></Cell>
            <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($invoice->amount_paid, 2, '.', '') }}</Data></Cell>
            <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($invoice->balance_due, 2, '.', '') }}</Data></Cell>
        </Row>
        @empty
        <Row>
            <Cell ss:StyleID="Text" ss:MergeAcross="7"><Data ss:Type="String">No invoices found</Data></Cell>
        </Row>
        @endforelse
    </Table>
</Worksheet>
@endsection
