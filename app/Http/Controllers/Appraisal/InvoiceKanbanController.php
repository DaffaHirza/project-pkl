<?php

namespace App\Http\Controllers\Appraisal;

use App\Http\Controllers\Controller;
use App\Models\ProjectKanban;
use App\Models\InvoiceKanban;
use App\Models\ActivityKanban;
use Illuminate\Http\Request;

class InvoiceKanbanController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = InvoiceKanban::with(['project.client']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('overdue') && $request->overdue === 'yes') {
            $query->overdue();
        }

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%");
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total_unpaid' => InvoiceKanban::unpaid()->count(),
            'total_overdue' => InvoiceKanban::overdue()->count(),
            'total_paid_this_month' => InvoiceKanban::paid()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->count(),
        ];

        return view('appraisal.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create(ProjectKanban $project)
    {
        return view('appraisal.invoices.create', compact('project'));
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request, ProjectKanban $project)
    {
        $validated = $request->validate([
            'payment_due_date' => 'required|date|after:today',
        ]);

        $invoice = InvoiceKanban::create([
            'project_id' => $project->id,
            'invoice_number' => InvoiceKanban::generateInvoiceNumber(),
            'status' => 'unpaid',
            'payment_due_date' => $validated['payment_due_date'],
        ]);

        // Log activity
        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'upload',
            'stage_context' => 'invoicing',
            'description' => "Invoice {$invoice->invoice_number} terbit. Jatuh tempo: " . $invoice->payment_due_date->format('d/m/Y'),
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Invoice berhasil dibuat: ' . $invoice->invoice_number);
    }

    /**
     * Display the specified invoice
     */
    public function show(InvoiceKanban $invoice)
    {
        $invoice->load(['project.client']);
        return view('appraisal.invoices.show', compact('invoice'));
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, InvoiceKanban $invoice)
    {
        $validated = $request->validate([
            'payment_due_date' => 'required|date',
            'status' => 'required|in:unpaid,paid,cancelled',
        ]);

        $oldStatus = $invoice->status;

        if ($validated['status'] === 'paid' && $oldStatus !== 'paid') {
            $validated['paid_at'] = now();
        }

        $invoice->update($validated);

        // If paid, move project to done
        if ($validated['status'] === 'paid' && $oldStatus !== 'paid') {
            $project = $invoice->project;
            
            if ($project->current_stage === 'invoicing') {
                $project->update(['current_stage' => 'done']);
                ActivityKanban::logStageMove($project, $request->user(), 'invoicing', 'done');
            }

            ActivityKanban::create([
                'project_id' => $project->id,
                'user_id' => $request->user()->id,
                'activity_type' => 'approval',
                'stage_context' => 'invoicing',
                'description' => "Invoice {$invoice->invoice_number} lunas. Project selesai.",
            ]);
        }

        return redirect()
            ->route('appraisal.projects.show', $invoice->project)
            ->with('success', 'Invoice berhasil diperbarui.');
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Request $request, InvoiceKanban $invoice)
    {
        $invoice->markAsPaid();

        $project = $invoice->project;

        // Move project to done
        if ($project->current_stage === 'invoicing') {
            $project->update(['current_stage' => 'done']);
            ActivityKanban::logStageMove($project, $request->user(), 'invoicing', 'done');
        }

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'approval',
            'stage_context' => 'done',
            'description' => "Pembayaran {$invoice->invoice_number} diterima. Project selesai.",
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Invoice ditandai lunas.',
            ]);
        }

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Pembayaran berhasil dicatat. Project selesai!');
    }

    /**
     * Cancel invoice
     */
    public function cancel(Request $request, InvoiceKanban $invoice)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $invoice->update(['status' => 'cancelled']);

        $project = $invoice->project;

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => $project->current_stage,
            'description' => "Invoice {$invoice->invoice_number} dibatalkan." . ($validated['reason'] ? " Alasan: {$validated['reason']}" : ''),
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Invoice berhasil dibatalkan.');
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Request $request, InvoiceKanban $invoice)
    {
        $project = $invoice->project;
        $invoiceNumber = $invoice->invoice_number;
        
        $invoice->delete();

        ActivityKanban::create([
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
            'activity_type' => 'comment',
            'stage_context' => $project->current_stage,
            'description' => "Invoice {$invoiceNumber} dihapus.",
        ]);

        return redirect()
            ->route('appraisal.projects.show', $project)
            ->with('success', 'Invoice berhasil dihapus.');
    }

    /**
     * Get overdue invoices for notifications
     */
    public function overdue()
    {
        $invoices = InvoiceKanban::with(['project.client'])
            ->overdue()
            ->orderBy('payment_due_date')
            ->get();

        return response()->json($invoices);
    }
}
