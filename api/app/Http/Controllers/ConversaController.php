<?php

namespace App\Http\Controllers;

use App\Models\Conversa;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConversaController extends Controller
{
    // Busca pessoas pelo nome para iniciar uma conversa.
    public function usuarios(Request $request)
    {
        $data = $request->validate([
            'busca' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        return User::query()
            ->where('id', '!=', $request->user()->id)
            ->where('name', 'ilike', '%' . $data['busca'] . '%')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(20, ['id', 'name']);
    }

    // Lista apenas as conversas do usuário autenticado.
    public function index(Request $request)
    {
        $usuarioId = (int) $request->user()->id;

        return Conversa::doUsuario($usuarioId)
            ->with([
                'usuario1:id,name',
                'usuario2:id,name',
                'ultimaMensagem.remetente:id,name',
                'ultimaMensagem.publicacao:id,title,content,user_id',
            ])
            ->withCount([
                'mensagens as nao_lidas' => function ($query) use ($usuarioId) {
                    $query->where('remetente_id', '!=', $usuarioId)
                        ->whereNull('lida_em');
                },
            ])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(20);
    }

    // Cria a conversa ou devolve a que já existe entre os dois.
    public function store(Request $request)
    {
        $data = $request->validate([
            'destinatario_id' => [
                'required',
                'integer',
                'exists:usuarios,id',
            ],
        ]);

        $usuarioId = (int) $request->user()->id;
        $destinatarioId = (int) $data['destinatario_id'];

        if ($usuarioId === $destinatarioId) {
            throw ValidationException::withMessages([
                'destinatario_id' => 'Escolha outra pessoa para conversar.',
            ]);
        }

        $conversa = Conversa::firstOrCreate([
            'usuario_1_id' => min($usuarioId, $destinatarioId),
            'usuario_2_id' => max($usuarioId, $destinatarioId),
        ]);

        $status = $conversa->wasRecentlyCreated ? 201 : 200;

        return response()->json(
            $conversa->load(['usuario1:id,name', 'usuario2:id,name']),
            $status
        );
    }

    // Retorna as mensagens, começando pelas mais recentes.
    public function mensagens(Request $request, int $conversa)
    {
        $registro = Conversa::doUsuario((int) $request->user()->id)
            ->findOrFail($conversa);

        return $registro->mensagens()
            ->with([
                'remetente:id,name',
                'publicacao:id,title,content,user_id',
            ])
            ->orderByDesc('id')
            ->paginate(30);
    }

    // Envia texto, publicação ou publicação acompanhada de texto.
    public function enviar(Request $request, int $conversa)
    {
        $usuarioId = (int) $request->user()->id;

        // Verifica a participação antes de validar o conteúdo.
        Conversa::doUsuario($usuarioId)->findOrFail($conversa);

        $data = $request->validate([
            'conteudo' => ['nullable', 'string', 'max:5000'],
            'post_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $conteudo = trim($data['conteudo'] ?? '');
        $postId = $data['post_id'] ?? null;

        if ($conteudo === '' && $postId === null) {
            throw ValidationException::withMessages([
                'conteudo' => 'Digite uma mensagem ou escolha uma publicação.',
            ]);
        }

        $mensagem = DB::transaction(function () use (
            $conversa,
            $usuarioId,
            $conteudo,
            $postId
        ) {
            // Organiza envios simultâneos na mesma conversa.
            $registro = Conversa::doUsuario($usuarioId)
                ->lockForUpdate()
                ->findOrFail($conversa);

            if ($postId !== null) {
                $post = Post::query()
                    ->whereKey($postId)
                    ->where('status', Post::STATUS_APPROVED)
                    ->sharedLock()
                    ->first();

                if (!$post) {
                    throw ValidationException::withMessages([
                        'post_id' => 'Essa publicação não está disponível para compartilhar.',
                    ]);
                }
            }

            $mensagem = $registro->mensagens()->create([
                'remetente_id' => $usuarioId,
                'conteudo' => $conteudo === '' ? null : $conteudo,
                'post_id' => $postId,
                'tipo' => $postId === null ? 'texto' : 'publicacao',
            ]);

            $registro->touch();

            return $mensagem;
        });

        return response()->json(
            $mensagem->load([
                'remetente:id,name',
                'publicacao:id,title,content,user_id',
            ]),
            201
        );
    }

    // Marca como lidas as mensagens recebidas até o ID informado.
    public function ler(Request $request, int $conversa)
    {
        $usuarioId = (int) $request->user()->id;

        $registro = Conversa::doUsuario($usuarioId)
            ->findOrFail($conversa);

        $data = $request->validate([
            'ate_mensagem_id' => ['required', 'integer', 'min:1'],
        ]);

        $mensagem = $registro->mensagens()
            ->findOrFail($data['ate_mensagem_id']);

        $atualizadas = $registro->mensagens()
            ->where('id', '<=', $mensagem->id)
            ->where('remetente_id', '!=', $usuarioId)
            ->whereNull('lida_em')
            ->update(['lida_em' => now()]);

        return response()->json([
            'mensagens_marcadas' => $atualizadas,
        ]);
    }
}