<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\DispatchLog;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $userId = $this->userId();

        $contactModel = new Contact();
        $tagModel = new Tag();
        $dispatchModel = new DispatchLog();

        $totalContacts = $contactModel->countForUser($userId);
        $tags = $tagModel->allWithCountForUser($userId);
        $stats = $dispatchModel->getStatsForUser($userId);
        $recentLogs = $dispatchModel->getRecentForUser($userId, 10);

        $flash = $this->getFlash();

        $this->view('dashboard.index', [
            'flash' => $flash,
            'totalContacts' => $totalContacts,
            'totalTags' => count($tags),
            'stats' => $stats ?: ['total' => 0, 'sent' => 0, 'failed' => 0, 'pending' => 0],
            'recentLogs' => $recentLogs,
        ]);
    }
}
