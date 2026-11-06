
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

$pagina = $_GET['pagina'] ?? 'principal';

$paginasPermitidas = [
    'principal' => __DIR__ . '/../views/index.html',
    'participantes' => __DIR__ . '/../views/participantes/index.php',
    'atividades' => __DIR__ . '/../views/atividades/index.php',
    'inscricoes' => __DIR__ . '/../views/inscricoes/index.php'
];

if (isset($paginasPermitidas[$pagina]) && is_file($paginasPermitidas[$pagina])) {

    if ($pagina === 'participantes') {

        require_once __DIR__ . '/../services/ParticipanteService.php';

        $participanteService = new ParticipanteService($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['acao'] ?? '') === 'excluir') {

            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            if ($id <= 0 || $id === false || $id === null) {

                header('Location: ?pagina=participantes&status=dados_invalidos');
                exit;
            }

            try {

                $resultado = $participanteService->excluirParticipante($id);

                if ($resultado === 'EXCLUIDO') {
                    $status = 'excluido';
                } elseif ($resultado === 'NAO_ENCONTRADO') {
                    $status = 'nao_encontrado';
                } else {
                    $status = 'erro_excluir';
                }
            } catch (PDOException $e) {
                $status = 'erro_excluir';
            }

            header('Location: ?pagina=participantes&status=' . $status);
            exit;
        }

        $participanteEdicao = null;

        if (isset($_GET['editar'])) {

            $id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

            if ($id === false || $id <= 0  || $id === null) {

                header('Location: ?pagina=participantes&status=dados_invalido');
                exit;
            }

            $participanteEdicao = $participanteService->buscarParticipantePorId($id);

            if ($participanteEdicao === null) {
                header('Location: ?pagina=participantes&status=dados_invalidos');
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nome_participante = $_POST['nome_participante'] ?? null;
            $email = $_POST['email'] ?? null;
            $telefone = $_POST['telefone'] ?? null;

            $estaEditando = isset($_GET['editar']);

            if (
                !is_string($nome_participante) ||
                !is_string($email) ||
                !is_string($telefone) ||
                trim($nome_participante) === '' ||
                trim($email) === '' ||
                trim($telefone) === '' ||
                !filter_var($email, FILTER_VALIDATE_EMAIL)
            ) {

                $destino = '?pagina=participantes';

                if ($estaEditando) {
                    $destino .= '&editar=' . (int) $_GET['editar'];
                }

                header('Location: ' . $destino . '&status=dados_invalidos');
                exit;
            }

            $participante = new Participante(
                trim($nome_participante),
                trim($email),
                trim($telefone)
            );

            try {

                if ($estaEditando) {

                    $id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

                    if ($id === false || $id <= 0 || $id === null) {

                        header('Location:  ?pagina=participantes&status=dados_invalidos');
                        exit;
                    }

                    $resultado = $participanteService->atualizarParticipante($id, $participante);

                    if ($resultado === 'ATUALIZADO') {

                        header('Location: ?pagina=participantes&status=atualizado');
                        exit;
                    } elseif ($resultado === 'EMAIL_DUPLICADO') {
                        header('Location: ?pagina=participantes&editar=' . $id . '&status=email_duplicado');
                        exit;
                    } else {
                        header('Location: ?pagina=participantes&editar=' . $id . '&status=erro');
                        exit;
                    }
                } else {

                    $resultado = $participanteService->cadastrarParticipante($participante);

                    if ($resultado === 'CADASTRADO') {
                        $status = 'cadastrado';
                    } elseif ($resultado === 'EMAIL_DUPLICADO') {
                        $status = 'email_duplicado';
                    } else {
                        $status = 'erro';
                    }

                    header('Location: ?pagina=participantes&status=' . $status);
                    exit;
                }
            } catch (PDOException $e) {
                header('Location: ?pagina=participantes&status=erro');
                exit;
            }
        }

        $participantes = $participanteService->listarParticipantes();
    }

    if ($pagina === 'atividades') {

        require_once __DIR__ . '/../services/AtividadeService.php';

        $atividadeService = new AtividadeService($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['acao'] ?? '') === 'excluir') {

            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            if ($id === null || $id <= 0 || $id === false) {

                header('Location: ?pagina=atividades&status=dados_invalidos');
                exit;
            }

            try {

                $resultado = $atividadeService->excluirAtividade($id);

                if ($resultado === 'EXCLUIDO') {
                    $status = 'excluido';
                } elseif ($resultado === 'NAO_ENCONTRADO') {
                    $status = 'nao_encontrado';
                } else {
                    $status = 'erro_excluir';
                }
            } catch (PDOException $e) {
                $status = 'erro_excluir';
            }

            header('Location: ?pagina=atividades&status=' . $status);
            exit;
        }

        $atividadeEdicao = null;

        if (isset($_GET['editar'])) {

            $id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

            if ($id <= 0 || $id === null || $id === false) {
                header('Location: ?pagina=atividades&status=dados_invalidos');
                exit;
            }

            $atividadeEdicao = $atividadeService->buscarAtividadePorId($id);

            if ($atividadeEdicao === null) {
                header('Location: ?pagina=atividades&status=dados_invalidos');
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nome_atividade = $_GET['nome_atividade'] ?? null;
            $descricao = $_GET['descricao'] ?? null;
            $data_atividade = $_GET['data_atividade'] ?? null;
            $hora_inicio = $_GET['hora_inicio'] ?? null;
            $hora_fim = $_GET['hora_fim'] ?? null;
            $local_atividade = $_GET['local_atividade'] ?? null;
            $capacidade = $_GET['capacidade'] ?? null;

            $estaEditando = isset($_GET['editar']);

            if (
                !is_string($nome_atividade) ||
                !is_string($descricao) ||
                !is_string($data_atividade) ||
                !is_string($hora_inicio) ||
                !is_string($hora_fim) ||
                !is_string($local_atividade) ||
                !filter_var($capacidade, FILTER_VALIDATE_INT) ||
                (int) $capacidade <= 0
            ) {

                $destino = '?pagina=atividades';

                if ($estaEditando) {

                    $destino .= '&editar' . (int) $_GET['editar'];
                }

                header('Location: ' . $destino . '&status=' . '&dados_invalidos');
                exit;
            }

            $atividade = new Atividade(
                trim($nome_atividade),
                trim($descricao),
                trim($data_atividade),
                trim($hora_inicio),
                trim($hora_fim),
                trim($local_atividade),
                (int) $capacidade
            );

            try {

                if ($estaEditando) {

                    $id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

                    if ($id <= 0 || $id === null || $id === false) {
                        header('Location: ?pagina=atividades&status=dados_invalidos');
                        exit;
                    }

                    $resultado = $atividadeService->atualizarAtividade($id, $atividade);

                    if ($resultado === 'ATUALIZADO') {
                        header('Location: ?pagina=atividades&status=atualizado');
                        exit;
                    } elseif ($resultado === 'CAPACIDADE_MENOR_QUE_INSCRITOS') {
                        header('Location: ?pagina=atividades&editar=' . $id . '&status=capacidade_menor_que_inscritos');
                        exit;
                    } elseif ($resultado === 'CAPACIDADE_INVALIDA') {
                        header('Location: ?pagina=atividades&editar=' . $id . '&status=capacidade_invalida');
                        exit;
                    } else {
                        header('Location: ?pagina=atividades&editar=' . $id . '$status=erro');
                    }
                } else {

                    $resultado = $atividadeService->cadastarAtividade($atividade);

                    if ($resultado === 'CADASTRADO') {
                        $status = 'cadastrado';
                    } elseif ($resultado === 'CAPACIDADE_INVALIDA') {
                        $status = 'capacidade_invalida';
                    } else {
                        $status = 'erro';
                    }

                    header('Location: ?pagina=atividades&status=' . $status);
                    exit;
                }
            } catch (PDOException $e) {

                header('Location: ?pagina=atividades&status=erro');
                exit;
            }
        }

        $atividades = $atividadeService->listaratividades();
    }

    if ($pagina === 'inscricoes') {

        require_once __DIR__ . '/../services/AtividadeService.php';
        require_once __DIR__ . '/../services/ParticipanteService.php';
        require_once __DIR__ . '/../services/InscricaoService.php';

        $inscricaoService = new InscricaoService($pdo);
        $participanteService = new ParticipanteService($pdo);
        $atividadeService = new AtividadeService($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['acao'] ?? null) === 'cancelar') {

            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            if ($id <= 0 || $id === null || $id == false) {
                header('Location: ?pagina=inscricoes&status=dados_invalidos');
                exit;
            }

            $resultado = $inscricaoService->cancelarInscricao($id);

            if ($resultado === 'CANCELADA') {
                $status = 'cancelada';
            } else {
                $status = 'erro_cancelar';
            }

            header('Location: ?pagina=inscricoes&status=' . $status);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id_participante = filter_input(INPUT_POST, 'nome_participante', FILTER_VALIDATE_INT);
            $id_atividade = filter_input(INPUT_POST, 'nome_atividade', FILTER_VALIDATE_INT);

            if (
                $id_participante === false || $id_participante === null || $id_participante <= 0 ||
                $id_atividade === false || $id_atividade === null || $id_atividade <= 0
            ) {
                header('Location: ?pagina=inscricoes&status=dados_invalidos');
                exit;
            }

            $inscricao = new Inscricao($id_participante, $id_atividade);

            $resultado = $inscricaoService->cadastrarInscricao($inscricao);

            if ($resultado === 'CADASTRADO') {
                $status = 'cadastrado';
            } elseif ($resultado === 'REATIVADA') {
                $status = 'reativada';
            } elseif ($resultado === 'INSCRICAO_DUPLICADA') {
                $status = 'inscricao_duplicada';
            } else {
                $status = 'erro_cadastro';
            }

            header('Location: ?pagina=inscricoes&status=' . $status);
            exit;
        }

        $inscricoes = $inscricaoService->listarInscricoes();
        $participantes = $participanteService->listarParticipantes();
        $atividades = $atividadeService->listarAtividades();
    }

    require $paginasPermitidas[$pagina];
}
