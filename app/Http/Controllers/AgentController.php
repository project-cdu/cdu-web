<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Rules\Cpf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Cadastro e consulta de agentes. Agentes podem consultar/cadastrar; apenas admins editam/removem.
 * Usa o model User: a senha sai criptografada (cast "hashed") e o login é o mesmo do resto do sistema.
 */
class AgentController extends Controller
{
    public function index(): View
    {
        return view()->file(resource_path('agent/index.blade.php'));
    }

    public function table(Request $request): JsonResponse
    {
        $query = User::query()
            ->agents()
            ->select(['id_user', 'name', 'email', 'role', 'email_verified_at', 'created_at']);

        $recordsTotal = (clone $query)->count();
        $search = trim((string) $request->input('search.value', ''));

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();
        $orderColumns = [
            0 => 'name',
            1 => 'email',
            2 => 'role',
            3 => 'email_verified_at',
            4 => 'created_at',
        ];
        $orderColumn = $orderColumns[(int) $request->input('order.0.column', 4)] ?? 'created_at';
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $length = max(1, min((int) $request->input('length', 10), 100));
        $start = max(0, (int) $request->input('start', 0));

        $rows = $query
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get()
            ->map(fn (User $agent): array => [
                'name' => $agent->name,
                'email' => $agent->email,
                'role' => 'Agente',
                'verified' => $agent->email_verified_at ? 'Verificado' : 'Pendente',
                'date' => $agent->created_at?->format('d/m/Y') ?? '—',
                'editUrl' => auth()->user()->isAdmin() ? route('agents.edit', $agent) : null,
                'deleteUrl' => auth()->user()->isAdmin() ? route('agents.destroy', $agent) : null,
            ])
            ->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['cpf' => Cpf::digits($request->input('cpf'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'string', new Cpf, 'unique:users,cpf'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $agent = new User($data);
        $agent->role = UserRole::AGENT;
        $agent->email_verified_at = now(); // quem cadastra é o admin: o e-mail dele vale como confirmação
        $agent->save();

        return redirect()->route('agents.index')->with('status', __('Agente cadastrado com sucesso.'));
    }

    public function edit(User $agent): View
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->ensureAgent($agent);

        return view()->file(resource_path('agent/edit.blade.php'), ['agent' => $agent]);
    }

    public function update(Request $request, User $agent): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->ensureAgent($agent);

        $request->merge(['cpf' => Cpf::digits($request->input('cpf'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($agent->getKey(), 'id_user')],
            'cpf' => ['required', 'string', new Cpf,
                Rule::unique('users', 'cpf')->ignore($agent->getKey(), 'id_user')],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
        ]);

        if (empty($data['password'])) {
            unset($data['password']); // em branco = mantém a senha atual
        }

        $agent->fill($data);

        if ($agent->isDirty('email')) {
            $agent->email_verified_at = now();
        }

        $agent->save();

        return redirect()->route('agents.index')->with('status', __('Agente atualizado com sucesso.'));
    }

    public function destroy(User $agent): RedirectResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $this->ensureAgent($agent);

        $agent->delete();

        return redirect()->route('agents.index')->with('status', __('Agente removido.'));
    }

    /**
     * Nunca mexe em cidadão ou admin por esta rota: qualquer id que não seja de agente vira 404.
     */
    private function ensureAgent(User $user): void
    {
        abort_unless($user->isAgent(), 404);
    }
}
