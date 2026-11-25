<?php

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('consulta o ViaCEP e retorna todos os dados do CEP', function () {
    Http::fake([
        'https://viacep.com.br/*' => Http::response([
            'cep' => '06310200',
            'uf' => 'SP',
            'localidade' => 'Carapicuíba',
            'bairro' => 'Centro',
            'logradouro' => 'Rua João Acácio de Almeida',
            'ibge' => '3510609',
            'ddd' => '11',
        ]),
    ]);

    $this->actingAs(User::factory()->create())
        ->getJson(route('complaints.cep', ['cep' => '06310-200']))
        ->assertOk()
        ->assertJsonPath('address.ibge', '3510609')
        ->assertJsonPath('address.logradouro', 'Rua João Acácio de Almeida')
        ->assertJsonPath('address.ddd', '11');
});

test('a geocodificação usa o endereço completo retornado pelo ViaCEP', function () {
    Http::fake([
        'https://nominatim.openstreetmap.org/*' => Http::response([
            ['lat' => '-23.550520', 'lon' => '-46.633308'],
        ]),
    ]);

    $viaCep = [
        'cep' => '01001000',
        'logradouro' => 'Praça da Sé',
        'complemento' => 'lado par',
        'bairro' => 'Sé',
        'localidade' => 'São Paulo',
        'uf' => 'SP',
        'ibge' => '3550308',
        'gia' => '1004',
        'ddd' => '11',
        'siafi' => '7107',
    ];

    $this->actingAs(User::factory()->create())
        ->getJson(route('complaints.geocode', [
            'endereco' => 'Praça da Sé, 100',
            'bairro' => $viaCep['bairro'],
            'cidade' => $viaCep['localidade'],
            'estado' => $viaCep['uf'],
            'cep' => $viaCep['cep'],
            'dados_viacep' => json_encode($viaCep),
        ]))
        ->assertOk()
        ->assertJsonPath('coordinates.latitude', -23.55052)
        ->assertJsonPath('coordinates.longitude', -46.633308);

    Http::assertSent(fn ($request) => $request['street'] === 'Praça da Sé, 100, lado par'
        && $request['city'] === 'São Paulo'
        && $request['state'] === 'SP'
        && $request['postalcode'] === '01001000');
});

test('usa o BuscarCep como segunda tentativa quando o OpenStreetMap não encontra o endereço', function () {
    Http::fake([
        'https://nominatim.openstreetmap.org/*' => Http::response([]),
        'https://buscarcep.com.br/*' => Http::response([
            'resultado' => '1',
            'latitude' => '-23.53007',
            'longitude' => '-46.83773',
        ]),
    ]);

    $this->actingAs(User::factory()->create())
        ->getJson(route('complaints.geocode', [
            'endereco' => 'Terminal João Acácio de Almeida',
            'bairro' => 'Centro',
            'cidade' => 'Carapicuíba',
            'estado' => 'SP',
            'cep' => '06310-200',
            'dados_viacep' => json_encode([
                'logradouro' => 'Terminal João Acácio de Almeida',
                'bairro' => 'Centro',
                'localidade' => 'Carapicuíba',
                'uf' => 'SP',
            ]),
        ]))
        ->assertOk()
        ->assertJsonPath('coordinates.latitude', -23.53007)
        ->assertJsonPath('coordinates.longitude', -46.83773)
        ->assertJsonPath('coordinates.source', 'buscarcep');
});

test('o andamento exibe as alterações de status salvas', function () {
    $agent = User::factory()->agent()->create();
    $complaint = Complaint::factory()->create();

    $this->actingAs($agent)
        ->patch(route('complaints.status', $complaint), [
            'status' => ComplaintStatus::IN_PROGRESS->value,
        ])
        ->assertRedirect(route('complaints.show', ['publicId' => $complaint->public_id]));

    $this->actingAs(User::find($complaint->id_user))
        ->get(route('complaints.show', ['publicId' => $complaint->public_id]))
        ->assertOk()
        ->assertSee('Status atualizado: Em andamento');
});

test('cria o relato com os valores padrão e os casts corretos', function () {
    $complaint = Complaint::factory()->create()->fresh();

    expect($complaint->status)->toBe(ComplaintStatus::PENDING)
        ->and($complaint->high_priority)->toBeFalse()
        ->and($complaint->latitude)->toBeFloat();
});

test('o relato pertence ao seu autor', function () {
    $user = User::factory()->create();
    $complaint = Complaint::factory()->create(['id_user' => $user->id_user]);

    expect($complaint->user->is($user))->toBeTrue();
});

test('os estados da factory definem o status e a prioridade esperados', function () {
    expect(Complaint::factory()->inProgress()->create()->status)->toBe(ComplaintStatus::IN_PROGRESS)
        ->and(Complaint::factory()->resolved()->create()->status)->toBe(ComplaintStatus::RESOLVED)
        ->and(Complaint::factory()->rejected()->create()->status)->toBe(ComplaintStatus::REJECTED)
        ->and(Complaint::factory()->highPriority()->create()->high_priority)->toBeTrue();
});

test('status, prioridade e autor não podem ser preenchidos em massa', function () {
    $complaint = new Complaint([
        'title' => 'Buraco na rua',
        'status' => ComplaintStatus::RESOLVED->value,
        'high_priority' => true,
        'id_user' => User::factory()->create()->id_user,
    ]);

    expect($complaint->title)->toBe('Buraco na rua')
        ->and($complaint->status)->toBeNull()
        ->and($complaint->high_priority)->toBeNull()
        ->and($complaint->id_user)->toBeNull();
});

test('o status pode ser alterado explicitamente', function () {
    $complaint = Complaint::factory()->create();

    $complaint->status = ComplaintStatus::REJECTED;
    $complaint->save();

    expect($complaint->fresh()->status)->toBe(ComplaintStatus::REJECTED)
        ->and(ComplaintStatus::REJECTED->label())->toBe('Rejeitado');
});
