---
description: How to use the upgraded PMS billing module (folios, charges, payments, and invoices).
---

# Billing Module Workflow

This workflow documents how to operate the upgraded hotel accounting system.

## Core Concepts

- **Booking** = reservation information only (guest, dates, room, operational status).
- **Guest Folio** = the financial account for a single booking.
- **Booking Charges** = every billable item posted to a folio (room, restaurant, laundry, etc.).
- **Payments** = money received that only reduces the folio balance.
- **Invoice** = a snapshot generated from the folio at checkout.

## 1. Open a Folio

Folios are opened automatically when a booking is checked in, but you can also open one manually:

```php
use App\Models\Booking;
use App\Services\FolioService;

$booking = Booking::find($bookingId);
$folio = app(FolioService::class)->openFolio($booking);
```

## 2. Post a Charge

Charges are posted to the folio. They cannot be deleted; use `reverseCharge()` to create a reversing entry.

```php
use App\Services\ChargePostingService;

$charge = app(ChargePostingService::class)->postCharge([
    'booking_id'  => $booking->id,
    'folio_id'    => $folio->id,
    'description' => 'Laundry service',
    'charge_type' => 'laundry',
    'quantity'    => 2,
    'unit_price'  => 15000,
    'posting_date' => now()->toDateString(),
    'posted_by'   => auth()->id(),
]);
```

The folio `subtotal`, `total_amount`, `balance_due`, and `payment_status` are recalculated automatically.

## 3. Reverse a Charge

```php
use App\Services\ChargePostingService;

$reversal = app(ChargePostingService::class)->reverseCharge(
    $charge,
    auth()->user(),
    'Guest disputed the item'
);
```

## 4. Record a Payment

Payments only reduce the balance. They never edit charges.

```php
use App\Services\PaymentService;

$payment = app(PaymentService::class)->recordPayment([
    'booking_id'      => $booking->id,
    'folio_id'        => $folio->id,
    'amount'          => 50000,
    'payment_method'  => 'cash',
    'payment_date'    => now()->toDateString(),
    'payment_gateway' => 'manual',
    'receipt_number'  => 'RCP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
]);
```

When the folio balance reaches zero, `payment_status` is automatically set to `paid`.

## 5. Refund a Payment

Refunds create a new negative payment entry.

```php
use App\Services\PaymentService;

$refund = app(PaymentService::class)->refundPayment(
    $payment,
    25000,
    auth()->user(),
    'Overpayment refunded to guest'
);
```

## 6. Void a Payment

```php
use App\Services\PaymentService;

$payment = app(PaymentService::class)->voidPayment(
    $payment,
    'Cashier error - re-entered correctly',
    auth()->user()
);
```

## 7. Generate an Invoice

Invoices are generated from the folio at checkout and become immutable once issued.

```php
use App\Services\InvoiceService;

$invoice = app(InvoiceService::class)->generateFromFolio($folio, auth()->user());
$invoice = app(InvoiceService::class)->issueInvoice($invoice, auth()->user());
```

## 8. Reporting

Use the `BillingService` for dashboard and report data.

```php
use App\Services\BillingService;

$service = app(BillingService::class);

$summary = $service->getDashboardSummary(
    now()->startOfMonth()->toDateString(),
    now()->endOfMonth()->toDateString()
);

$revenueByType = $service->getRevenueByChargeType('2026-07-01', '2026-07-31');
$paymentsByMethod = $service->getPaymentsByMethod('2026-07-01', '2026-07-31');
```

## 9. Database Backfill Commands

If you have legacy data, run these to populate the new columns:

```bash
# Backfill charge totals
php artisan tinker --execute="DB::table('booking_charges')->where('total_amount', 0)->where('amount', '>', 0)->update(['total_amount' => DB::raw('amount'), 'unit_price' => DB::raw('amount'), 'quantity' => 1]);"

# Backfill folio_id on charges and payments
php artisan tinker --execute="DB::table('booking_charges')->where('folio_id', null)->update(['folio_id' => DB::raw('(select id from guest_folios where guest_folios.booking_id = booking_charges.booking_id limit 1)')]);"
php artisan tinker --execute="DB::table('payments')->where('folio_id', null)->update(['folio_id' => DB::raw('(select id from guest_folios where guest_folios.booking_id = payments.booking_id limit 1)')]);"

# Recalculate all folios
php artisan tinker --execute="foreach(App\Models\GuestFolio::where('status', 'open')->get() as \$folio){ app(App\Services\FolioService::class)->recalculate(\$folio); }"
```

## Important Rules

- **Charges can never be deleted.** Use reversing entries.
- **Payments can never edit charges.** Refunds create new payments.
- **Invoices become immutable once issued.** Void and re-issue if needed.
- **Closed or void folios are read-only.**
