<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Mail\NewsletterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Display newsletter subscribers
     */
    public function index(Request $request)
    {
        $query = Newsletter::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('email', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->where('created_at', '>=', now()->startOfDay());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
            }
        }

        $subscribers = $query->orderBy('created_at', 'desc')->paginate(20);
        $activeCount = Newsletter::active()->count();
        $unsubscribedCount = Newsletter::unsubscribed()->count();

        return view('admin.newsletter.index', compact('subscribers', 'activeCount', 'unsubscribedCount'));
    }

    /**
     * Show the form for sending newsletter
     */
    public function create()
    {
        $activeSubscribers = Newsletter::active()->count();
        
        return view('admin.newsletter.create', compact('activeSubscribers'));
    }

    /**
     * Send newsletter to all active subscribers
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ], [
            'subject.required' => 'Subjek diperlukan.',
            'subject.max' => 'Subjek terlalu panjang.',
            'content.required' => 'Kandungan diperlukan.',
            'content.min' => 'Kandungan terlalu pendek.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $activeSubscribers = Newsletter::active()->get();
        
        if ($activeSubscribers->isEmpty()) {
            return back()->with('error', 'Tiada pelanggan aktif untuk dihantar newsletter.');
        }

        $subject = $request->subject;
        $content = $request->content;
        $sentCount = 0;
        $failedCount = 0;

        foreach ($activeSubscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)->send(new NewsletterMail($subject, $content, $subscriber));
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                // Log the error
                \Log::error('Failed to send newsletter to ' . $subscriber->email . ': ' . $e->getMessage());
            }
        }

        $message = "Newsletter berjaya dihantar kepada {$sentCount} pelanggan.";
        if ($failedCount > 0) {
            $message .= " {$failedCount} emel gagal dihantar.";
        }

        return redirect()->route('admin.newsletter.index')->with('success', $message);
    }

    /**
     * Remove subscriber
     */
    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();
        
        return redirect()->route('admin.newsletter.index')->with('success', 'Pelanggan berjaya dikeluarkan.');
    }

    /**
     * Toggle subscription status
     */
    public function toggleStatus(Newsletter $newsletter)
    {
        if ($newsletter->status === 'active') {
            $newsletter->unsubscribe();
            $message = 'Pelanggan telah berhenti melanggani.';
        } else {
            $newsletter->resubscribe();
            $message = 'Pelanggan telah melanggani semula.';
        }

        return redirect()->route('admin.newsletter.index')->with('success', $message);
    }

    /**
     * Export subscribers
     */
    public function export()
    {
        $subscribers = Newsletter::orderBy('created_at', 'desc')->get();
        
        $filename = 'newsletter_subscribers_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subscribers) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Email', 'Status', 'Tarikh Langganan', 'Tarikh Berhenti']);
            
            // Add data
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->status,
                    $subscriber->subscribed_at->format('Y-m-d H:i:s'),
                    $subscriber->unsubscribed_at ? $subscriber->unsubscribed_at->format('Y-m-d H:i:s') : ''
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
