


<?php

require_once __DIR__ . '/../services/InscricaoService.php';

class InscricaoController
{

    private InscricaoService $inscricaoService;

    public function __construct(InscricaoService $inscricaoService)
    {
        $this->inscricaoService = $inscricaoService;
    }

    public function cadastrarInscricao(Inscricao $inscricao): void
    {

        $resultado = $this->inscricaoService->cadastrarInscricao($inscricao);

        header('Content-Type: application/json; charset=utf-8');

        if ($resultado === 'CADASTRADO') {
            http_response_code(201);

            echo json_encode([
                'mensagem' => 'Inscricão cadastrada'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'PARTICIPANTE_NAO_ENCOTRADO') {
            http_response_code(404);

            echo json_encode([
                'mensagem' => 'Participante não encontrado'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'ATIVIDADE_NAO_ENCOTRADO') {
            http_response_code(404);

            echo json_encode([
                'mensagem' => 'Atividade não encontrada'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'INSCRICAO_DUPLICADA') {
            http_response_code(409);

            echo json_encode([
                'mensagem' => 'Inscrição existente'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'ATIVIDADE_SEM_VAGAS') {
            http_response_code(409);

            echo json_encode([
                'mensagem' => 'Atividade sem vagas'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'REATIVADA') {
            http_response_code(409);

            echo json_encode([
                'mensagem' => 'Atividade sem vagas'
            ], JSON_UNESCAPED_UNICODE);
        } else {

            http_response_code(500);

            echo json_encode([
                'mensagem' => 'Erro ao cadastrad'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function listarInscricoes(): void
    {

        $resultado = $this->inscricaoService->listarInscricoes();

        header('Content-Type: application/json; charset=utf-8');

        if ($resultado === null) {

            http_response_code(404);

            echo json_encode([
                'mensagem' => 'Nenhuma inscrição cadastrada'
            ], JSON_UNESCAPED_UNICODE);
        }

        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    public function buscarInscricaoPorId(int $id): void
    {

        $resultado = $this->inscricaoService->buscarInscricaoPorId($id);

        header('Content-Type: application/json; charset=utf-8');


        if ($resultado === null) {

            http_response_code(404);

            echo json_encode([
                'mensagem' => 'Nenhuma inscrição encontrada'
            ], JSON_UNESCAPED_UNICODE);
        }

        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }


    public function cancelarInscricao(int $id): void
    {

        $resultado = $this->inscricaoService->cancelarInscricao($id);

        header('Content-Type: application/json; charset=utf-8');

        if ($resultado === 'CANCELADA') {

            http_response_code(200);

            echo json_encode([
                'mensagem' => 'Inscrição cancelada'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } elseif ($resultado === 'NAO_ENCONTRADO') {

            http_response_code(409);
            echo json_encode([
                'mensagem' => 'Inscrição não encontrada'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'JA_CANCELADA') {

            http_response_code(409);
            echo json_encode([
                'mensagem' => 'Inscrição já cancelada'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'mensagem' => 'Erro ao cancelar'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
    }
}
