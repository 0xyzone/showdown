<?php

namespace App\Exports;

use App\Models\TicketPurchase;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketSalesReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        protected array $filters = []
    ) {}

    public function collection()
    {
        $query = TicketPurchase::with(['tournament', 'seller', 'ticketPackage', 'paymentMethod', 'tickets.attendances.eventDay'])
            ->orderBy('created_at', 'desc');

        if (! empty($this->filters['tournament_id'])) {
            $query->where('tournament_id', $this->filters['tournament_id']);
        }

        if (! empty($this->filters['ticket_package_id'])) {
            $query->where('ticket_package_id', $this->filters['ticket_package_id']);
        }

        if (! empty($this->filters['seller_id'])) {
            $query->where('seller_id', $this->filters['seller_id']);
        }

        if (! empty($this->filters['payment_status'])) {
            $query->where('payment_status', $this->filters['payment_status']);
        }

        if (! empty($this->filters['payment_method_id'])) {
            $query->where('payment_method_id', $this->filters['payment_method_id']);
        }

        if (! empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($this->filters['date_from'])->toDateString());
        }

        if (! empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($this->filters['date_to'])->toDateString());
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Order #',
            'Tournament',
            'Package Tier',
            'Customer Name',
            'Customer Phone',
            'Tickets Qty',
            'Unit Price (NPR)',
            'Total Amount (NPR)',
            'Payment Status',
            'Payment Source',
            'Payment Reference',
            'Sold By Staff',
            'Checked In Count',
            'Sold Date & Time',
        ];
    }

    /**
     * @param  TicketPurchase  $purchase
     */
    public function map($purchase): array
    {
        $checkedInCount = $purchase->tickets->where('is_used', true)->count();

        return [
            $purchase->order_number,
            $purchase->tournament?->name ?? 'N/A',
            $purchase->package_name ?? ($purchase->ticketPackage?->name ?? 'Standard'),
            $purchase->customer_name,
            $purchase->customer_phone,
            $purchase->quantity,
            number_format((float) $purchase->unit_price, 2, '.', ''),
            number_format((float) $purchase->total_amount, 2, '.', ''),
            strtoupper($purchase->payment_status),
            $purchase->payment_source ?? 'N/A',
            $purchase->payment_reference ?? '—',
            $purchase->seller?->name ?? 'Admin',
            "{$checkedInCount}/{$purchase->quantity}",
            $purchase->created_at ? $purchase->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0F172A'], // Dark slate
                ],
            ],
        ];
    }
}
