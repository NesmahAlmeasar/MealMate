
$report = [];

try {
    // 1. Check Admin Permission
    $admin = \App\Models\User::whereHas('roles', function($q){ $q->where('name', 'Admin'); })->first();
    $randomUser = \App\Models\User::where('user_id', '!=', $admin->user_id ?? 0)->first();

    if ($admin && $randomUser) {
        $canChat = $admin->canChatWith($randomUser->user_id);
        $report['Admin -> Random User'] = $canChat ? 'PASS: Allowed' : 'FAIL: Blocked';
    } else {
        $report['Admin Test'] = 'SKIPPED: Not enough users';
    }

    // 2. Check Specialist (Without Consultation)
    $specialist = \App\Models\User::whereHas('roles', function($q){ $q->where('name', 'Specialist'); })->first();
    // Find a user who is NOT a client/nutritionist of this specialist and has NO messages
    $stranger = \App\Models\User::where('user_id', '!=', $specialist->user_id ?? 0)->get()->filter(function($u) use ($specialist) {
        return \App\Models\Message::betweenUsers($specialist->user_id, $u->user_id)->count() === 0;
    })->first();

    if ($specialist && $stranger) {
        $canChat = $specialist->canChatWith($stranger->user_id);
        $report['Specialist -> Stranger (No Consult/Msg)'] = !$canChat ? 'PASS: Blocked' : 'FAIL: Allowed';
    } else {
        $report['Specialist Test'] = 'SKIPPED: No suitable stranger found';
    }
    
    echo json_encode($report, JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
