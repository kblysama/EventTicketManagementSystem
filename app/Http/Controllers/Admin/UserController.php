<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->withCount('organizedEvents')->latest();

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($search).'%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%'.mb_strtolower($search).'%']);
            });
        }

        return view('admin.users.index', [
            'users' => $query->get(),
            'search' => $search,
            'roles' => Role::cases(),
            'adminCount' => User::query()->where('role', Role::Admin)->count(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'role' => ['required', Rule::enum(Role::class)],
        ]);

        User::query()->create($data);

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('admin.users.index'), 'Kullanıcı oluşturuldu.')
            : redirect()->route('admin.users.index');
    }

    public function update(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', Password::min(8)],
            'role' => ['required', Rule::enum(Role::class)],
        ]);

        if ($error = $this->roleChangeBlocked($request, $user, Role::from($data['role']))) {
            return $this->reject($request, $error);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('admin.users.index'), 'Kullanıcı güncellendi.')
            : redirect()->route('admin.users.index');
    }

    public function updateRole(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::enum(Role::class)],
        ]);

        if ($error = $this->roleChangeBlocked($request, $user, Role::from($data['role']))) {
            return $this->reject($request, $error);
        }

        $user->update($data);

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('admin.users.index'), 'Rol güncellendi.')
            : redirect()->route('admin.users.index');
    }

    public function destroy(Request $request, User $user): JsonResponse|RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return $this->reject($request, 'Kendi hesabınızı silemezsiniz.');
        }

        if ($user->isAdmin() && User::query()->where('role', Role::Admin)->count() <= 1) {
            return $this->reject($request, 'Son admin kullanıcısı silinemez.');
        }

        if ($user->organizedEvents()->exists()) {
            return $this->reject(
                $request,
                'Bu kullanıcının etkinlikleri var. Önce etkinlikleri silin veya başka bir organizatöre aktarın.'
            );
        }

        if (Schema::hasTable('personal_access_tokens')) {
            $user->tokens()->delete();
        }

        $user->delete();

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('admin.users.index'), 'Kullanıcı silindi.')
            : redirect()->route('admin.users.index');
    }

    private function roleChangeBlocked(Request $request, User $user, Role $newRole): ?string
    {
        if ($user->id === $request->user()->id && $newRole !== Role::Admin) {
            return 'Kendi admin rolünüzü değiştiremezsiniz.';
        }

        if ($user->isAdmin() && $newRole !== Role::Admin && User::query()->where('role', Role::Admin)->count() <= 1) {
            return 'Son admin kullanıcısının rolü değiştirilemez.';
        }

        return null;
    }

    private function reject(Request $request, string $message): JsonResponse|RedirectResponse
    {
        return $request->expectsJson() || $request->ajax()
            ? response()->json(['message' => $message], 422)
            : back()->withErrors(['user' => $message]);
    }
}
