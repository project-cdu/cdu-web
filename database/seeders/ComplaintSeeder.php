<?php

namespace Database\Seeders;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComplaintSeeder extends Seeder
{
    private const DEMO_TITLE_PREFIX = '[Demonstração]';

    public function run(): void
    {
        $citizen = User::query()->where('email', 'cidadao@example.com')->firstOrFail();
        $agent = User::query()->where('email', 'agente@example.com')->first();
        $imageSources = $this->imageSources();

        $this->removePreviousDemoData($citizen);

        $categories = [
            'Buraco na via' => [
                'titles' => ['Buraco aberto na pista', 'Cratera aumentando na rua', 'Asfalto danificado próximo à escola'],
                'descriptions' => ['O buraco está dificultando a passagem e oferece risco de acidentes.', 'A abertura aumentou após as últimas chuvas e precisa de manutenção.'],
            ],
            'Iluminação pública' => [
                'titles' => ['Poste sem iluminação', 'Lâmpadas apagadas na via', 'Rua escura durante a noite'],
                'descriptions' => ['A iluminação está apagada há vários dias, deixando o trecho inseguro.', 'Moradores relatam falta de iluminação no período noturno.'],
            ],
            'Lixo acumulado' => [
                'titles' => ['Lixo acumulado na calçada', 'Descarte irregular de resíduos', 'Coleta atrasada no bairro'],
                'descriptions' => ['Resíduos estão acumulados e causando mau cheiro para quem passa pelo local.', 'O descarte irregular atrai animais e precisa de limpeza.'],
            ],
            'Vazamento de água' => [
                'titles' => ['Vazamento de água na rua', 'Água escorrendo pela calçada', 'Vazamento próximo ao imóvel'],
                'descriptions' => ['Há água saindo do pavimento continuamente e existe risco de desperdício.', 'O vazamento está deixando a via molhada e dificultando a circulação.'],
            ],
            'Pragas e animais' => [
                'titles' => ['Foco de mosquitos em água parada', 'Animais soltos na via', 'Infestação próxima às residências'],
                'descriptions' => ['A situação preocupa os moradores e pode trazer riscos à saúde pública.', 'É necessária uma vistoria para avaliar e resolver o problema.'],
            ],
            'Outros' => [
                'titles' => ['Árvore caída na passagem', 'Sinalização danificada', 'Calçada com desnível'],
                'descriptions' => ['O problema está atrapalhando a circulação de pedestres e precisa de atendimento.', 'O local apresenta risco e deve ser avaliado pela equipe responsável.'],
            ],
        ];

        $districts = [
            ['name' => 'Centro', 'lat' => -23.5505, 'lng' => -46.6333],
            ['name' => 'Bela Vista', 'lat' => -23.5630, 'lng' => -46.6440],
            ['name' => 'Pinheiros', 'lat' => -23.5675, 'lng' => -46.6910],
            ['name' => 'Mooca', 'lat' => -23.5600, 'lng' => -46.5980],
            ['name' => 'Santana', 'lat' => -23.5000, 'lng' => -46.6250],
            ['name' => 'Vila Mariana', 'lat' => -23.5890, 'lng' => -46.6340],
            ['name' => 'Tatuapé', 'lat' => -23.5400, 'lng' => -46.5750],
            ['name' => 'Lapa', 'lat' => -23.5250, 'lng' => -46.7050],
            ['name' => 'Butantã', 'lat' => -23.5700, 'lng' => -46.7200],
            ['name' => 'Itaquera', 'lat' => -23.5400, 'lng' => -46.4550],
        ];
        $statusPattern = [
            ComplaintStatus::PENDING,
            ComplaintStatus::PENDING,
            ComplaintStatus::UNDER_REVIEW,
            ComplaintStatus::IN_PROGRESS,
            ComplaintStatus::IN_PROGRESS,
            ComplaintStatus::IN_PROGRESS,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::RESOLVED,
            ComplaintStatus::REJECTED,
            ComplaintStatus::DUPLICATE,
        ];
        $streets = ['Rua das Flores', 'Avenida Brasil', 'Rua das Acácias', 'Avenida Paulista', 'Rua do Mercado', 'Rua dos Ipês', 'Avenida Central', 'Rua da Paz'];
        $categoryNames = array_keys($categories);

        for ($index = 1; $index <= 240; $index++) {
            $category = $categoryNames[($index - 1) % count($categoryNames)];
            $content = $categories[$category];
            $district = $districts[($index - 1) % count($districts)];
            $status = $statusPattern[($index - 1) % count($statusPattern)];
            $imagePath = $this->copyDemoImage($imageSources[($index - 1) % max(1, count($imageSources))] ?? null, $index);
            $createdAt = now()->subDays((($index * 11) + (($index * $index) % 23)) % 540)->setTime(8 + ($index % 10), ($index * 7) % 60);

            $complaint = Complaint::forceCreate([
                'public_id' => (string) Str::uuid(),
                'id_user' => $citizen->id_user,
                'category' => $category,
                'title' => self::DEMO_TITLE_PREFIX.' '.$content['titles'][($index - 1) % count($content['titles'])],
                'description' => $content['descriptions'][($index - 1) % count($content['descriptions'])],
                'image_url' => null,
                'status' => $status,
                'high_priority' => $index % 4 === 0,
                'latitude' => $district['lat'] + (($index % 7) - 3) * 0.0012,
                'longitude' => $district['lng'] + (($index % 5) - 2) * 0.0014,
                'address' => $streets[($index - 1) % count($streets)].', '.(20 + $index),
                'district' => $district['name'],
                'cep' => str_pad((string) (1000000 + $index), 8, '0', STR_PAD_LEFT),
                'complement' => $index % 3 === 0 ? 'Próximo ao ponto de ônibus' : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            if ($imagePath !== null) {
                $complaint->images()->create(['path' => $imagePath, 'disk' => 'public']);
            }

            if ($status !== ComplaintStatus::PENDING && $agent !== null) {
                $justification = $this->statusJustification($status, $category, $index);

                DB::table('complaint_status_histories')->insert([
                    'complaint_id' => $complaint->complaint_id,
                    'from_status' => ComplaintStatus::PENDING->value,
                    'to_status' => $status->value,
                    'justification' => $justification,
                    'changed_by' => $agent->id_user,
                    'created_at' => $createdAt->copy()->addHours(2),
                    'updated_at' => $createdAt->copy()->addHours(2),
                ]);
            }
        }
    }

    private function statusJustification(ComplaintStatus $status, string $category, int $index): ?string
    {
        $messages = match ($status) {
            ComplaintStatus::RESOLVED => [
                'Buraco na via' => [
                    'O trecho foi recuperado e o buraco deixou de oferecer risco aos motoristas.',
                    'A equipe de manutenção refez o pavimento e liberou a passagem com segurança.',
                ],
                'Iluminação pública' => [
                    'A lâmpada foi substituída e o trecho voltou a receber iluminação durante a noite.',
                    'A manutenção corrigiu o defeito no poste e restabeleceu a iluminação da via.',
                ],
                'Lixo acumulado' => [
                    'A limpeza foi realizada e os resíduos foram removidos do local informado.',
                    'A equipe de zeladoria recolheu o material e deixou a calçada novamente livre.',
                ],
                'Vazamento de água' => [
                    'O vazamento foi reparado e o desperdício de água foi interrompido.',
                    'A equipe técnica corrigiu a tubulação e normalizou as condições da via.',
                ],
                'Pragas e animais' => [
                    'Foi realizada uma vistoria no local e adotadas medidas para eliminar o risco identificado.',
                    'A ocorrência recebeu atendimento da equipe responsável e a situação foi controlada.',
                ],
                'Outros' => [
                    'A equipe responsável realizou o reparo necessário e confirmou a normalização do local.',
                    'O atendimento foi concluído após a correção do problema relatado pelo cidadão.',
                ],
            ],
            ComplaintStatus::REJECTED => [
                'Buraco na via' => [
                    'A vistoria não confirmou a existência de um buraco no ponto indicado na denúncia.',
                    'O local foi avaliado e o problema informado não se enquadra em manutenção viária municipal.',
                ],
                'Iluminação pública' => [
                    'A equipe verificou que o ponto possui iluminação funcionando normalmente no período informado.',
                    'A solicitação foi rejeitada porque o poste indicado pertence a uma área de responsabilidade privada.',
                ],
                'Lixo acumulado' => [
                    'A vistoria constatou que o material não era responsabilidade do serviço público de coleta.',
                    'A solicitação foi encerrada porque a área já havia sido limpa antes da análise da ocorrência.',
                ],
                'Vazamento de água' => [
                    'A equipe não encontrou vazamento de responsabilidade da rede pública no endereço informado.',
                    'A ocorrência foi encaminhada para o responsável pelo imóvel por se tratar de instalação particular.',
                ],
                'Pragas e animais' => [
                    'A análise não identificou risco sanitário ou situação que exigisse atendimento municipal.',
                    'A solicitação foi rejeitada porque o caso deve ser tratado diretamente pelo responsável pelo imóvel.',
                ],
                'Outros' => [
                    'Após análise, a solicitação não se enquadrou nos serviços atendidos pela plataforma.',
                    'A ocorrência foi rejeitada por falta de informações suficientes para confirmar o problema relatado.',
                ],
            ],
            default => null,
        };

        if ($messages === null) {
            return null;
        }

        $categoryMessages = $messages[$category] ?? $messages['Outros'];

        return $categoryMessages[$index % count($categoryMessages)];
    }

    private function removePreviousDemoData(User $citizen): void
    {
        $ids = Complaint::query()
            ->where('id_user', $citizen->id_user)
            ->where('title', 'like', self::DEMO_TITLE_PREFIX.'%')
            ->pluck('complaint_id');

        if ($ids->isNotEmpty()) {
            ComplaintImage::query()->whereIn('complaint_id', $ids)->get()->each(function (ComplaintImage $image): void {
                Storage::disk($image->disk)->delete($image->path);
                $image->delete();
            });
            Complaint::query()->whereIn('complaint_id', $ids)->delete();
        }
    }

    /** @return array<int, string> */
    private function imageSources(): array
    {
        return collect(File::files(resource_path('denuncias')))
            ->filter(fn (\SplFileInfo $file): bool => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->reject(function (\SplFileInfo $file): bool {
                $name = Str::lower($file->getFilename());

                return Str::startsWith($name, 'captura de tela')
                    || Str::contains($name, ['dashboard', 'tela', 'login', 'modelo', 'cadastro']);
            })
            ->sortBy(fn (\SplFileInfo $file): string => $file->getFilename())
            ->map(fn (\SplFileInfo $file): string => $file->getPathname())
            ->values()
            ->all();
    }

    private function copyDemoImage(?string $source, int $index): ?string
    {
        if ($source === null || ! is_file($source)) {
            return null;
        }

        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        $path = 'denuncias/demo/demo-'.str_pad((string) $index, 3, '0', STR_PAD_LEFT).'.'.$extension;
        Storage::disk('public')->put($path, File::get($source));

        return $path;
    }
}
