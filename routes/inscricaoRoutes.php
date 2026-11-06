



<?php

function registrarRotasInscricoes(PDO $pdo): void
{

    require_once __DIR__ . '/../controllers/InscricaoController.php';

    $inscricaoService = new InscricaoService($pdo);
    $inscricaoController = new InscricaoController($inscricaoService);

    $metodo = $_SERVER['REQUEST_METHOD'];
    $caminho = $_GET['rota'] ?? '';

    if ($metodo === 'POST' && $caminho === 'cadastrar_inscricao') {

       header('Content-Type: application/json; charset=utf-8');

        $dados = json_decode(file_get_contents('php://input'), true);

        if (
            !is_array($dados) || isset($dados['id_participante'], $dados['id_atividade']) ||
            !is_int($dados['id_participante']) || !is_int($dados['id_atividade']) ||
            ($dados['id_participante'])  <= 0 ||  ($dados['id_atividade'])
        ) {

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Preencha todos os campos corretamente'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $inscricao = new Inscricao(
            $dados['$id_participante'],
            $dados['id_atividade']
        );

        $inscricaoController->cadastrarInscricao($inscricao);
        return;
    }

    if ($metodo === 'GET' && $caminho === 'inscricoes') {

        $inscricaoController->listarInscricoes();
        return;
    }

    if ($metodo === 'GET' && $caminho === 'inscricao_por_id') {

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === null || $id <= 0 || $id === false) {
           header('Content-Type: application/json; charset=utf-8');

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Id invalido'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }
        $inscricaoController->buscarInscricaoPorId($id);
        return;
    }

    if ($metodo === 'PUT' && $caminho === 'cancelar_inscricao') {

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === null || $id <= 0 || $id === false) {
           header('Content-Type: application/json; charset=utf-8');

            http_response_code(400);

            echo json_encode([
                'mensagem' => 'Id invalido'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $inscricaoController->cancelarInscricao($id);
        return;
    }
}
