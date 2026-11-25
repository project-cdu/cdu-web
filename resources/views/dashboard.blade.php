<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Painel</h2>
    </x-slot>

    <div class="citizen-dashboard">
        <div class="citizen-dashboard-heading">
            <div>
                <p class="complaints-eyebrow">Acompanhe sua participação</p>
                <h1>Olá, {{ auth()->user()->name }}!</h1>
                <p>Veja as denúncias mais recentes e acompanhe o andamento de cada uma.</p>
            </div>
            <a class="complaints-create-link" href="{{ route('complaints.create') }}"><span aria-hidden="true">＋</span> Fazer denúncia</a>
        </div>

        <section class="citizen-dashboard-panel" aria-labelledby="recent-complaints-title">
            <div class="citizen-dashboard-panel-heading">
                <div>
                    <p class="complaints-eyebrow">Atualizações recentes</p>
                    <h2 id="recent-complaints-title">Suas últimas denúncias</h2>
                </div>
                <a href="{{ route('complaints.mine') }}">Ver todas</a>
            </div>

            @if (empty($recentComplaints))
                <div class="citizen-dashboard-empty">
                    <strong>Você ainda não registrou uma denúncia.</strong>
                    <span>Quando enviar um relato, ele aparecerá aqui.</span>
                </div>
            @else
                <div class="citizen-recent-list">
                    @foreach ($recentComplaints as $complaint)
                        <a class="citizen-recent-item" href="{{ route('complaints.show', ['publicId' => $complaint['publicId']]) }}">
                            <span class="citizen-recent-icon" aria-hidden="true">{{ $complaint['highPriority'] ? '!' : '•' }}</span>
                            <span class="citizen-recent-copy">
                                <strong>{{ $complaint['title'] }}</strong>
                                <small>{{ $complaint['category'] }} · {{ $complaint['location']['district'] ?: 'Local não informado' }}</small>
                            </span>
                            <span class="complaint-status complaint-status--{{ \Illuminate\Support\Str::slug($complaint['status']) }}">{{ $complaint['status'] }}</span>
                            <span class="citizen-recent-arrow" aria-hidden="true">→</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
