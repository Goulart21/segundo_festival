

<?php

require_once __DIR__ . '/../services/AtividadeService.php';

class ParticipanteController
{

    private AtividadeService $atividadeService;

    public function __construct(AtividadeService $atividadeService)
    {
        $this->atividadeService = $atividadeService;
    }

    public function cadastrarAtividade(Atividade $atividade): void
    {

        $resultado = $this->atividadeService->cadastarAtividade($atividade);

        header('Location: application/json; charset=utf-8');

        if ($resultado === 'CADASTRADO') {
            http_response_code(201);

            echo json_encode([
                'mensagem' => 'Atividade cadastrada'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'CAPACIDADE_INVALIDA') {
            http_response_code(409);

            echo json_encode([
                'mensagem' => 'Capacidade invalida'
            ], JSON_UNESCAPED_UNICODE);
        } else {

            http_response_code(500);

            echo json_encode([
                'mensagem' => 'Erro ao cadastrar'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function listarAtividades(): void
    {

        $resultado = $this->atividadeService->listarAtividades();

        header('Location: application/json; charset=utf-8');

        if ($resultado === null) {

            http_response_code(404);

            echo json_encode([
                'mensagem' => 'Nenhuma atividade cadastrado'
            ], JSON_UNESCAPED_UNICODE);
        }

        echo json_encode($resultado);
    }

    public function buscarAtividadePorId(int $id): void
    {

        $resultado = $this->atividadeService->buscarAtividadePorId($id);

        header('Location: application/json; charset=utf-8');


        if ($resultado === null) {

            http_response_code(404);

            echo json_encode([
                'mensagem' => 'Nenhuma atividade encontrada'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode($resultado);
    }

    public function atualizarAtividade(int $id, Atividade $atividade): void
    {

        $resultado = $this->atividadeService->atualizarAtividade($id, $atividade);

        header('Location: application/json; charset=utf-8');

        if ($resultado === 'ATUALIZADO') {

            http_response_code(200);

            echo json_encode([
                'mensagem' => 'Atividade atualizado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } elseif ($resultado === 'CAPACIDADE_INVALIDA') {

            http_response_code(409);
            echo json_encode([
                'mensagem' => 'Capacidade invalida'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'NAO_ENCONTRADO') {

            http_response_code(409);
            echo json_encode([
                'mensagem' => 'Aividade não encontrado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } elseif ($resultado === 'CAPACIDADE_MENOR_QUE_INSCRITOS') {

            http_response_code(409);
            echo json_encode([
                'mensagem' => 'Capacidade menor que inscritos'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } else {
            echo json_encode([
                'mensagem' => 'Erro ao atualizar'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function excluirAtividade(int $id): void
    {

        $resultado = $this->atividadeService->excluirAtividade($id);

        header('Location: application/json; charset=utf-8');

        if ($resultado === 'EXCLUIDO') {

            http_response_code(200);

            echo json_encode([
                'mensagem' => 'Atividade excluido'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } elseif ($resultado === 'NAO_ENCONTRADO') {

            http_response_code(404);
            echo json_encode([
                'mensagem' => 'Atividade não encontrado'
            ], JSON_UNESCAPED_UNICODE);
        } elseif ($resultado === 'POSSUI_INSCRICOES') {

            http_response_code(409);
            echo json_encode([
                'mensagem' => 'Atividade já possui inscrições'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'mensagem' => 'Erro ao excluir'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
    }
}
