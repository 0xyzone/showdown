<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Official Tournament Admission Tickets - {{ $purchase->order_number }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 12px;
            line-height: 1.4;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .ticket-wrapper {
            border: 2px dashed #94a3b8;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            background: #f8fafc;
            page-break-inside: avoid;
        }
        .ticket-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .tournament-title {
            font-size: 18px;
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
            font-size: 9px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .ticket-body {
            display: table;
            width: 100%;
        }
        .ticket-details {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            padding-right: 12px;
        }
        .ticket-qr {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
            text-align: center;
            border-left: 2px dashed #cbd5e1;
            padding-left: 12px;
        }
        .data-row {
            margin-bottom: 8px;
        }
        .data-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .data-value {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .ticket-number-box {
            background: #e2e8f0;
            padding: 6px 10px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #0f172a;
            display: inline-block;
        }
        .footer-note {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 10px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    @foreach($ticketsWithQr as $index => $item)
        @php
            $ticket = $item['ticket'];
            $qrCode = $item['qr_base64'];
        @endphp

        <div class="ticket-wrapper">
            <div class="ticket-header">
                <table style="width: 100%;">
                    <tr>
                        <td style="vertical-align: middle;">
                            <h1 class="tournament-title">{{ $tournament->name ?? 'ESPORTS TOURNAMENT' }}</h1>
                            <span class="season-badge">{{ $tournament->season_version ?? 'OFFICIAL EVENT' }}</span>
                        </td>
                        <td style="text-align: right; vertical-align: middle;">
                            <span style="font-size: 10px; font-weight: bold; color: #64748b;">OFFICIAL PASS {{ $index + 1 }} OF {{ count($ticketsWithQr) }}</span>
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
                            <td colspan="2" style="padding-top: 4px;">
                                <div class="data-row">
                                    <div class="data-label">Event Schedule / Location</div>
                                    <div class="data-value" style="font-size: 11px;">
                                        {{ $tournament->start_date ? $tournament->start_date->format('M d, Y • h:i A') : 'Date to be announced' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="ticket-qr">
                    <img src="{{ $qrCode }}" alt="QR" style="width: 110px; height: 110px; display: block; margin: 0 auto;">
                    <div style="font-size: 8px; font-family: monospace; color: #64748b; margin-top: 4px;">SCAN FOR ENTRY</div>
                </div>
            </div>

            <div class="footer-note">
                Present this QR code at the event gate &bull; Non-transferable once checked in &bull; Issued by {{ $purchase->createdBy?->name ?? 'Admin' }}
            </div>
        </div>
    @endforeach

</body>
</html>
