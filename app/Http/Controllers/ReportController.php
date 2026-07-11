<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function dailySummary(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $summary = Payment::summaryForDate($date);

        $byPaymentMethod = Payment::whereDate('created_at', $date)
            ->get()
            ->groupBy('payment_method')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'gross' => $group->sum('subtotal_amount'),
                'net' => $group->sum('total_amount') - $group->sum('refund_amount'),
            ]);

        return view('admin.reports.index', compact('summary', 'byPaymentMethod', 'date'));
    }

    public function transactions(Request $request)
    {
        $query = Payment::with(['order.diningTable', 'processedBy'])
            ->orderByDesc('created_at');

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->query('date_from'))->startOfDay(),
                Carbon::parse($request->query('date_to'))->endOfDay(),
            ]);
        } elseif ($request->filled('date')) {
            $query->whereDate('created_at', $request->query('date'));
        }

        $transactions = $query->paginate(25)->withQueryString();

        return view('admin.reports.transaction', compact('transactions'));
    }
}