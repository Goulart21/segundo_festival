
<?php

require_once __DIR__ . '/../services/ParticipanteService.php';

class ParticipanteController
{

    private ParticipanteService $participanteService;

    public function __construct(ParticipanteService $participanteService)
    {
        $this->participanteService = $participanteService;
    }

    public function cadastrarParticipantes(Participante $participante): void
    {

        $resultado = $this->participanteService->cadastrarParticipante($participante);

        header('Content-Type: application/json; charset=utf-8;');

        if ($resultado === 'CADASTRADO') {
            http_response_code(201);

            echo json_encode([
                'mensagem' => 'Participante cadastrado'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'EMAIL_DUPLICADO') {
            http_response_code(409);

            echo json_encode([
                'mensagem' => 'Emaail já cadastrado'
            ], JSON_UNESCAPED_UNICODE);
        } else {

            http_response_code(500);

            echo json_encode([
                'mensagem' => 'Erro ao cadastrad'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function listarParticipantes(): void
    {

        $resultado = $this->participanteService->listarParticipantes();

        header('Content-Type: application/json; charset=utf-8');

        if ($resultado === null) {

            http_response_code(404);

            echo json_encode([
                'mensagem' => 'Nenhum participante cadastrado'
            ], JSON_UNESCAPED_UNICODE);
        }

        echo json_encode($resultado);
    }

    public function buscarParticipantePorId(int $id): void
    {

        $resultado = $this->participanteService->buscarParticipantePorId($id);

        header('Content-Type: application/json; charset=utf-8');


        if ($resultado === null) {

            http_response_code(404);

            echo json_encode([
                'mensagem' => 'Nenhum participante encontrado'
            ], JSON_UNESCAPED_UNICODE);
        }

        echo json_encode($resultado);
    }

    public function atualizarParticipante(int $id, Participante $participante): void
    {

        $resultado = $this->participanteService->atualizarParticipante($id, $participante);

        header('Content-Type: application/json; charset=utf-8');

        if ($resultado === 'ATUALIZADO') {

            http_response_code(200);

            echo json_encode([
                'mensagem' => 'Participante atualizado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } elseif ($resultado === 'EMAIL_DUPLICADO') {

            http_response_code(409);
            echo json_encode([
                'mensagem' => 'Email já cadastrado'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'NAO_ENCONTRADO') {

            http_response_code(409);
            echo json_encode([
                'mensagem' => 'Participante não encontrado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } else {
            echo json_encode([
                'mensagem' => 'Erro ao atualizar'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function excluirParticipante(int $id): void
    {

        $resultado = $this->participanteService->excluirParticipante($id);

        header('Location: application/json; charset=utf-8');

        if ($resultado === 'EXCLUIDO') {

            http_response_code(200);

            echo json_encode([
                'mensagem' => 'Participante excluido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } elseif ($resultado === 'NAO_ENCONTRADO') {

            http_response_code(409);
            echo json_encode([
                'mensagem' => 'Participante não encontrado'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'mensagem' => 'Erro ao excluir'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
    }
}
