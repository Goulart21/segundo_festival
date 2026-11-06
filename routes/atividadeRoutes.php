

<?php

function registrarRotasAtividades(PDO $pdo): void
{
    require_once __DIR__ . '/../controllers/AtividadeController.php';

    $atividadeService = new AtividadeService($pdo);
    $atividadeController = new AtividadeController($atividadeService);

    $metodo = $_SERVER['REQUEST_METHOD'];
    $caminho = $_GET['rota'] ?? '';


    if ($metodo === 'GET' && $caminho === 'atividades') {

        $atividadeController->listarAtividades();

        return;
    }


    if ($metodo === 'GET' && $caminho === 'atividade_por_id') {

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === false || $id === null || $id <= 0) {

            header('Content-Type: application/json; charset=utf-8');

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Informe um ID válido'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $atividadeController->buscarAtividadePorId($id);

        return;
    }

    if ($metodo === 'POST' && $caminho === 'cadastrar_atividade') {

        header('Content-Type: application/json; charset=utf-8');

        $dados = json_decode(file_get_contents('php://input'), true);

        if (
            !is_array($dados) ||
            !isset(
                $dados['nome_atividade'],
                $dados['descricao'],
                $dados['data_atividade'],
                $dados['hora_inicio'],
                $dados['hora_fim'],
                $dados['local_atividade'],
                $dados['capacidade']
            ) ||
            !is_string($dados['nome_atividade']) ||
            !is_string($dados['descricao']) ||
            !is_string($dados['data_atividade']) ||
            !is_string($dados['hora_inicio']) ||
            !is_string($dados['hora_fim']) ||
            !is_string($dados['local_atividade']) ||
            !is_int($dados['capacidade']) ||
            trim($dados['nome_atividade']) === '' ||
            trim($dados['descricao']) === '' ||
            trim($dados['data_atividade']) === '' ||
            trim($dados['hora_inicio']) === '' ||
            trim($dados['hora_fim']) === '' ||
            trim($dados['local_atividade']) === ''
        ) {
            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Dados inválidos. Informe todos os campos corretamente.'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $atividade = new Atividade(
            trim($dados['nome_atividade']),
            trim($dados['descricao']),
            trim($dados['data_atividade']),
            trim($dados['hora_inicio']),
            trim($dados['hora_fim']),
            trim($dados['local_atividade']),
            $dados['capacidade']
        );

        $atividadeController->cadastrarAtividade($atividade);

        return;
    }

    if ($metodo === 'PUT' && $caminho === 'atualizar_atividade') {

        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === false || $id === null || $id <= 0) {

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Informe um ID válido'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $dados = json_decode(file_get_contents('php://input'), true);

        // Validar os campos
        if (
            !is_array($dados) ||
            !isset(
                $dados['nome_atividade'],
                $dados['descricao'],
                $dados['data_atividade'],
                $dados['hora_inicio'],
                $dados['hora_fim'],
                $dados['local_atividade'],
                $dados['capacidade']
            ) ||
            !is_string($dados['nome_atividade']) ||
            !is_string($dados['descricao']) ||
            !is_string($dados['data_atividade']) ||
            !is_string($dados['hora_inicio']) ||
            !is_string($dados['hora_fim']) ||
            !is_string($dados['local_atividade']) ||
            !is_int($dados['capacidade']) ||
            trim($dados['nome_atividade']) === '' ||
            trim($dados['descricao']) === '' ||
            trim($dados['data_atividade']) === '' ||
            trim($dados['hora_inicio']) === '' ||
            trim($dados['hora_fim']) === '' ||
            trim($dados['local_atividade']) === ''
        ) {

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Dados inválidos. Informe todos os campos corretamente.'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $atividadeAtualizada = new Atividade(
            trim($dados['nome_atividade']),
            trim($dados['descricao']),
            trim($dados['data_atividade']),
            trim($dados['hora_inicio']),
            trim($dados['hora_fim']),
            trim($dados['local_atividade']),
            $dados['capacidade']
        );

        $atividadeController->atualizarAtividade(
            $id,
            $atividadeAtualizada
        );

        return;
    }


if ($metodo === 'DELETE' && $caminho === 'excluir_atividade') {

    header('Content-Type: application/json; charset=utf-8');

    // Validar o ID recebido pela URL
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {

        http_response_code(400);

        echo json_encode([
            'mensagem' => 'Informe um ID válido'
        ], JSON_UNESCAPED_UNICODE);

        return;
    }

    // Chamar o controller
    $atividadeController->excluirAtividade($id);

    return;
}

    header('Content-Type: application/json; charset=utf-8');

    http_response_code(404);

    echo json_encode([
        'mensagem' => 'Rota de atividade não encontrada'
    ], JSON_UNESCAPED_UNICODE);
}
