@extends('exports.xml-layout')

@php
function xe($v) { return htmlspecialchars((string) $v, ENT_XML1 | ENT_COMPAT, 'UTF-8'); }
@endphp

@section('worksheets')
<Worksheet ss:Name="Outstanding Bills">
    <Table>
        <Column ss:Width="120"/>
        <Column ss:Width="180"/>
        <Column ss:Width="80"/>
        <Column ss:Width="100"/>
        <Column ss:Width="110"/>
        <Column ss:Width="110"/>
        <Column ss:Width="110"/>

        <Row>
            <Cell ss:StyleID="HotelName" ss:MergeAcross="6"><Data ss:Type="String">{!! xe($hotelName) !!}</Data></Cell>
        </Row>
        @if($hotelAddress)
        <Row>
            <Cell ss:StyleID="HotelAddress" ss:MergeAcross="6"><Data ss:Type="String">{!! xe($hotelAddress) !!}</Data></Cell>
        </Row>
        @endif
        @if($hotelPhone || $hotelEmail)
        <Row>
            <Cell ss:StyleID="HotelAddress" ss:MergeAcross="6"><Data ss:Type="String">{!! xe(implode(' | ', array_filter([$hotelPhone, $hotelEmail]))) !!}</Data></Cell>
        </Row>
        @endif
        <Row>
            <Cell ss:StyleID="SectionTitle" ss:MergeAcross="6"><Data ss:Type="String">Billing &amp; Reconciliation - Outstanding Bills</Data></Cell>
        </Row>
        <Row>
            <Cell ss:StyleID="GeneratedAt" ss:MergeAcross="6"><Data ss:Type="String">Generated: {!! xe($generatedAt) !!}</Data></Cell>
        </Row>
        <Row/>

        <Row>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Booking Ref</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Guest</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Room</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Status</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Total</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Paid</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Balance</Data></Cell>
        </Row>
        @forelse($outstandingBills as $bill)
        <Row>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($bill->booking_ref) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($bill->guest_name) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($bill->room_number) !!}</Data></Cell>
            <Cell ss:StyleID="Status"><Data ss:Type="String">{!! xe(ucfirst(str_replace('_', ' ', $bill->status))) !!}</Data></Cell>
            <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($bill->total_amount, 2, '.', '') }}</Data></Cell>
            <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($bill->retainer_paid, 2, '.', '') }}</Data></Cell>
            <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($bill->balance_due, 2, '.', '') }}</Data></Cell>
        </Row>
        @empty
        <Row>
            <Cell ss:StyleID="Text" ss:MergeAcross="6"><Data ss:Type="String">No outstanding bills</Data></Cell>
        </Row>
        @endforelse
    </Table>
</Worksheet>

<Worksheet ss:Name="Pending Confirmations">
    <Table>
        <Column ss:Width="120"/>
        <Column ss:Width="180"/>
        <Column ss:Width="80"/>
        <Column ss:Width="110"/>
        <Column ss:Width="110"/>

        <Row>
            <Cell ss:StyleID="HotelName" ss:MergeAcross="4"><Data ss:Type="String">{!! xe($hotelName) !!}</Data></Cell>
        </Row>
        @if($hotelAddress)
        <Row>
            <Cell ss:StyleID="HotelAddress" ss:MergeAcross="4"><Data ss:Type="String">{!! xe($hotelAddress) !!}</Data></Cell>
        </Row>
        @endif
        <Row>
            <Cell ss:StyleID="SectionTitle" ss:MergeAcross="4"><Data ss:Type="String">Billing &amp; Reconciliation - Pending Confirmations</Data></Cell>
        </Row>
        <Row>
            <Cell ss:StyleID="GeneratedAt" ss:MergeAcross="4"><Data ss:Type="String">Generated: {!! xe($generatedAt) !!}</Data></Cell>
        </Row>
        <Row/>

        <Row>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Booking Ref</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Guest</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Room</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Check In</Data></Cell>
            <Cell ss:StyleID="Header"><Data ss:Type="String">Amount</Data></Cell>
        </Row>
        @forelse($pendingConfirmations as $booking)
        <Row>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($booking->booking_ref) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($booking->guest_name) !!}</Data></Cell>
            <Cell ss:StyleID="Text"><Data ss:Type="String">{!! xe($booking->room_number) !!}</Data></Cell>
            <Cell ss:StyleID="Date"><Data ss:Type="String">{!! xe(date('Y-m-d', strtotime($booking->check_in_date))) !!}</Data></Cell>
            <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($booking->total_amount, 2, '.', '') }}</Data></Cell>
        </Row>
        @empty
        <Row>
            <Cell ss:StyleID="Text" ss:MergeAcross="4"><Data ss:Type="String">No pending confirmations</Data></Cell>
        </Row>
        @endforelse
    </Table>
</Worksheet>
@endsection
