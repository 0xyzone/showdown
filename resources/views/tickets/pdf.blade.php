<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Official Tournament Admission Tickets - {{ $purchase->order_number }}</title>
    <style>
        @page {
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.4;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .ticket-wrapper {
            border: 2px dashed #94a3b8;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
            background: #f8fafc;
            page-break-inside: avoid;
        }
        .ticket-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .tournament-title {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .season-badge {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 4px;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .package-badge {
            display: inline-block;
            background: #059669;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            margin-left: 6px;
        }
        .ticket-body {
            display: table;
            width: 100%;
        }
        .ticket-details {
            display: table-cell;
            width: 72%;
            vertical-align: top;
            padding-right: 12px;
        }
        .ticket-qr {
            display: table-cell;
            width: 28%;
            vertical-align: middle;
            text-align: center;
            border-left: 2px dashed #cbd5e1;
            padding-left: 12px;
        }
        .data-row {
            margin-bottom: 6px;
        }
        .data-label {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }
        .data-value {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }
        .ticket-number-box {
            background: #e2e8f0;
            padding: 4px 8px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #0f172a;
            display: inline-block;
        }
        .validity-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 5px 8px;
            border-radius: 6px;
            margin-top: 4px;
        }
        .footer-note {
            font-size: 8px;
            color: #64748b;
            margin-top: 8px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    @foreach($ticketsWithQr as $index => $item)
        @php
            $ticket = $item['ticket'];
            $qrCode = $item['qr_base64'];
            $validDaysText = $item['valid_days_text'];
        @endphp

        <div class="ticket-wrapper">
            <div class="ticket-header">
                <table style="width: 100%;">
                    <tr>
                        <td style="vertical-align: middle;">
                            <h1 class="tournament-title">{{ $tournament->name ?? 'ESPORTS TOURNAMENT' }}</h1>
                            <span class="season-badge">{{ $tournament->season_version ?? 'OFFICIAL EVENT' }}</span>
                            <span class="package-badge">{{ $ticket->package_name ?? ($purchase->package_name ?? 'General Admission') }}</span>
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span style="font-size: 9px; font-weight: bold; color: #64748b;">ADMISSION PASS {{ $index + 1 }} OF {{ count($ticketsWithQr) }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="ticket-body">
                <div class="ticket-details">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 55%; vertical-align: top;">
                                <div class="data-row">
                                    <div class="data-label">Attendee Name</div>
                                    <div class="data-value">{{ $ticket->customer_name }}</div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Customer Phone</div>
                                    <div class="data-value">{{ $ticket->customer_phone }}</div>
                                </div>
                            </td>
                            <td style="width: 45%; vertical-align: top;">
                                <div class="data-row">
                                    <div class="data-label">Ticket Identifier</div>
                                    <div class="ticket-number-box">{{ $ticket->ticket_number }}</div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Order Ref / Price</div>
                                    <div class="data-value" style="font-size: 11px;">{{ $purchase->order_number }} &bull; Rs. {{ number_format($ticket->price, 2) }}</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="validity-box">
                                    <div class="data-label" style="color: #047857;">Valid Event Days / Admission Schedule:</div>
                                    <div style="font-size: 10px; font-weight: bold; color: #065f46;">{{ $validDaysText }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="ticket-qr">
                    <img src="{{ $qrCode }}" alt="QR" style="width: 100px; height: 100px; display: block; margin: 0 auto;">
                    <div style="font-size: 7.5px; font-family: monospace; color: #64748b; margin-top: 3px;">SCAN AT GATE</div>
                </div>
            </div>

            <div class="footer-note">
                Present QR code at gate &bull; Valid only on authorized event days &bull; Sold by: {{ $purchase->seller?->name ?? ($purchase->createdBy?->name ?? 'Staff Cashier') }}
            </div>
        </div>
    @endforeach

</body>
</html>
