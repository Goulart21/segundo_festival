

<?php

function registarRotasParticipantes(PDO $pdo): void
{

    require_once __DIR__ . '/../controllers/ParticipanteController.php';

    $participanteService = new ParticipanteService($pdo);
    $participanteController = new ParticipanteController($participanteService);

    $metodo = $_SERVER['REQUEST_METHOD'];
    $caminho = $_GET['rota'] ?? '';

    if ($metodo === 'POST' && $caminho === 'cadastrar_participante') {

        header('Content-Type: application/json; charset=utf-8');

        $dados = json_decode(file_get_contents('php://input'), true);

        if (
            !is_array($dados) ||
            !isset($dados['nome_participante'], $dados['email'], $dados['telefone']) ||
            !is_string($dados['nome_participante']) ||
            !is_string($dados['email']) ||
            !is_string($dados['telefone']) ||
            trim($dados['nome_participante']) === '' ||
            trim($dados['email']) === '' ||
            trim($dados['telefone']) === '' ||
            !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)
        ) {
            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Preencha todos os campos corretamente'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $participante = new Participante(
            trim($dados['nome_participante']),
            trim($dados['email']),
            trim($dados['telefone'])
        );

        $participanteController->cadastrarParticipantes($participante);
        return;
    }

    if ($metodo === 'GET' && $caminho === 'participantes') {

        $participanteController->listarParticipantes();
        return;
    }

    if ($metodo === 'GET' && $caminho === 'participante_por_id') {

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === null || $id <= 0 || $id === false) {
            header('Content-Type: application/json; charset=utf-8');

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Id invalido'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }
        $participanteController->buscarParticipantePorId($id);
        return;
    }

    if ($metodo === 'PUT' && $caminho === 'atualizar_participante') {

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === null || $id <= 0 || $id === false) {
            header('Content-Type: application/json; charset=utf-8');

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Id invalido'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $dados = json_decode(file_get_contents('php://input'), true);

        if (
            !is_array($dados) || !isset($dados['nome_participante'], $dados['email'], $dados['telefone']) ||
            !is_string($dados['nome_participante']) || !is_string($dados['email'])  || !is_string($dados['email']) ||
            trim($dados['nome_participante']) === '' ||  trim($dados['email']) === '' ||   trim($dados['email']) === '' ||
            !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)
        ) {

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Preencha todos os campos corretamente'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $participanteAtualizado = new Participante(
            trim($dados['nome_participante']),
            trim($dados['email']),
            trim($dados['telefone'])
        );

        $participanteController->atualizarParticipante($id, $participanteAtualizado);
        return;
    }

    if ($metodo === 'DELETE' && $caminho === 'excluir_participante') {

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === null || $id <= 0 || $id === false) {
            header('Content-Type: application/json; charset=utf-8');

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Id invalido'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $participanteController->excluirParticipante($id);
        return;
    }
}
