<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('author')->latest()->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'target_audience' => 'required|in:All,Students,Instructors',
        ]);

        Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'target_audience' => $request->target_audience,
            'posted_by_user_id' => Auth::id(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Announcement published successfully!');
    }
    // Mark all active announcements as read for current user
    public function markAllRead()
    {
        $userId = auth()->id();
        $target = auth()->user()->hasRole('Student') ? ['All', 'Students'] : ['All', 'Instructors', 'Students'];

        $activeAnnouncements = Announcement::where('is_active', true)
            ->whereIn('target_audience', $target)
            ->pluck('id');

        foreach ($activeAnnouncements as $announcementId) {
            \DB::table('announcement_reads')->updateOrInsert(
                ['user_id' => $userId, 'announcement_id' => $announcementId],
                ['read_at' => now()]
            );
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}