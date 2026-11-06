
<?php

require_once __DIR__ . '/../config/config.php';

$rota = $_GET['rota'] ?? null;

if ($rota !== null) {

    $rotasParticipantes = [
        'cadastrar_participante',
        'participantes',
        'participante_por_id',
        'atualizar_participante',
        'excluir_participante'
    ];

    $rotasAtividades = [
        'cadastrar_atividade',
        'atividades',
        'atividade_por_id',
        'atualizar_atividade',
        'excluir_atividade'
    ];

    $rotasInscricoes = [
        'cadastrar_inscricao',
        'inscricoes',
        'inscricao_por_id',
        'cancelar_inscricao'
    ];

    if (in_array($rota, $rotasParticipantes, true)) {

        require_once __DIR__ . '/../routes/participanteRoutes.php';

        registarRotasParticipantes($pdo);
    } elseif (in_array($rota, $rotasAtividades, true)) {

        require_once __DIR__ . '/../routes/atividadeRoutes.php';

        registrarRotasAtividades($pdo);
    } elseif (in_array($rota, $rotasInscricoes, true)) {

        require_once __DIR__ . '/../routes/inscricaoRoutes.php';

        registrarRotasInscricoes($pdo);
    } else {

        header('Content-Type: application/json; charset=utf-8');

        http_response_code(404);

        echo json_encode([
            'mensagem' => 'Rota não encontrada'
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

