<?php

namespace Modules\Auth\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Lab404\Impersonate\Services\ImpersonateManager;

class AdminImpersonationController extends Controller
{
    public function __construct(
        private readonly ImpersonateManager $manager
    ) {}

    public function take(Request $request, User $user): RedirectResponse
    {
        /** @var User|null $admin */
        $admin = Auth::user();

        if (!$admin || !$admin->canImpersonate()) {
            abort(403, 'Unauthorized to impersonate users.');
        }

        if ($admin->id === $user->id) {
            abort(403, 'Cannot impersonate yourself.');
        }

        if (!$user->canBeImpersonated()) {
            abort(403, 'This user cannot be impersonated.');
        }

        if ($this->manager->isImpersonating()) {
            abort(403, 'Already impersonating a user.');
        }

        Log::info('Admin started impersonation', [
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'target_user_id' => $user->id,
            'target_user_email' => $user->email,
            'ip' => $request->ip(),
        ]);

        $this->manager->take($admin, $user);

        $targetRoute = $user->hasRole('mentor')
            ? route('mentor.dashboard')
            : route('student.dashboard');

        return redirect()->to($targetRoute)
            ->with('info', "You are now logged in as {$user->name}.");
    }

    public function leave(Request $request): RedirectResponse
    {
        if (!$this->manager->isImpersonating()) {
            return redirect()->route('admin.dashboard');
        }

        $impersonatorId = $this->manager->getImpersonatorId();

        $this->manager->leave();

        Log::info('Admin ended impersonation', [
            'admin_id' => $impersonatorId,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Returned to your Admin account.');
    }
}
