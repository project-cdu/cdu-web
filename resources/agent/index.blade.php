<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Agentes
        </h2>
    </x-slot>

    <div class="agents-index-page py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div role="status" class="p-4 rounded-md bg-green-50 text-green-800 dark:bg-green-900/40 dark:text-green-200 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Novo agente: já abre quando houve erro de validação --}}
            <details class="bg-white dark:bg-gray-800 shadow sm:rounded-lg" @if ($errors->any() && ! request()->routeIs('agents.edit')) open @endif>
                <summary class="cursor-pointer select-none p-4 sm:p-6 min-h-[44px] font-medium text-gray-900 dark:text-gray-100">
                    + Cadastrar novo agente
                </summary>
                <div class="px-4 pb-4 sm:px-6 sm:pb-6">
                    <x-agent-form :action="route('agents.store')" submit="Cadastrar agente" />
                </div>
            </details>

            <div class="agents-list-card overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-gray-800">
                <div class="agents-list-heading flex items-center justify-between gap-4 border-b border-gray-100 px-6 py-5 dark:border-gray-700">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Agentes cadastrados</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Consulte os agentes autorizados a operar o painel.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto px-4 pb-4 sm:px-6">
                    <table class="agents-table w-full text-sm text-start" data-agent-users-datatable data-datatable-url="{{ route('agents.table') }}">
                        <thead class="bg-gray-50/80 text-gray-600 dark:bg-gray-700/70 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-start font-medium">Nome</th>
                                <th class="px-4 py-3 text-start font-medium">E-mail</th>
                                <th class="px-4 py-3 text-start font-medium">Perfil</th>
                                <th class="px-4 py-3 text-start font-medium">Verificação</th>
                                <th class="px-4 py-3 text-start font-medium">Cadastro</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-900 dark:text-gray-100"></tbody>
                    </table>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>
