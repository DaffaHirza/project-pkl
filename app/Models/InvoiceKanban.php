<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceKanban extends Model
{
    use HasFactory;

    protected $table = 'invoices_kanban';

    protected $fillable = [
        'project_id',
        'invoice_number',
        'status',
        'payment_due_date',
        'paid_at',
    ];

    protected $casts = [
        'payment_due_date' => 'date',
        'paid_at' => 'date',
    ];

    /**
     * Invoice status options
     */
    public const STATUS = [
        'unpaid' => 'Belum Dibayar',
        'paid' => 'Lunas',
        'cancelled' => 'Dibatalkan',
    ];

    /**
     * Get the project for this invoice
     */
    public function project()
    {
        return $this->belongsTo(ProjectKanban::class, 'project_id');
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'unpaid' && $this->payment_due_date < now();
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): bool
    {
        $this->status = 'paid';
        $this->paid_at = now();
        return $this->save();
    }

    /**
     * Generate invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return sprintf('INV-%s%s-%04d', $year, $month, $sequence);
    }

    /**
     * Scope for unpaid
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope for paid
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'unpaid')
            ->where('payment_due_date', '<', now());
    }
}
