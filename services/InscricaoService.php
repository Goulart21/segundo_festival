
<?php

require_once __DIR__ . '/../models/Atividade.php';
require_once __DIR__ . '/../models/Inscricao.php';
require_once __DIR__ . '/../models/Participante.php';
class InscricaoService
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function cadastrarInscricao(Inscricao $inscricao): string
    {

        try {

            $this->pdo->beginTransaction();

            $sql = "SELECT id_participante FROM participantes WHERE id_participante = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $inscricao->getIdParticipante()
            ]);

            if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
                $this->pdo->rollBack();
                return 'PARTICIPANTE_NAO_ENCONTRADO';
            }

            $sql = "SELECT capacidade WHERE id_atividade = :id FOR update";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $inscricao->getIdAtividade()
            ]);

            $atividade = $stmt->fetch(PDo::FETCH_ASSOC);

            if ($atividade === false) {
                $this->pdo->rollBack();
                return 'ATIVIDADE_NAO_ENCONTRADA';
            }

            $capacidade = (int) $atividade['capacidade'];

            $sql = "SELECT id_inscricao, status FROM inscricoes WHERE id_participante = :id_participante AND id_atividade = :id_atividade";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id_participante' => $inscricao->getIdParticipante(),
                ':id_atividade' => $inscricao->getIdAtividade()
            ]);

            $inscricaoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($inscricaoExistente !== false && $inscricaoExistente['status'] === 'ATIVA') {

                $this->pdo->rollBack();
                return 'INSCRICAO_DUPLICADA';
            }

            $sql = "SELECT COUNT(*) FROM inscricoes WHERE id_atividade = :id AND status = 'ATIVA'";

            $stmt = $this->pdo->prepare($sql);

            $quantidadeInscritos = (int) $stmt->fetchColumn();

            if ($quantidadeInscritos >= $capacidade) {
                $this->pdo->rollBack();
                return 'ATIVIDADE_LOTADA';
            }

            if ($inscricaoExistente !== false) {

                $sql = "UPDATE inscricoes SET status = 'ATIVA' WHERE id_inscricao = :id_inscricao";

                $stmt = $this->pdo->prepare($sql);

                $stmt->execute([
                    ':id_inscricao' => $inscricaoExistente['id_inscricao']
                ]);

                $this->pdo->commit();

                return 'REATIVADA';
            }

            $sql = "INSERT INTO inscricoes (id_participante, id_atividade) VALUES (:id_participante, :id_atividade)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id_participante' => $inscricao->getIdParticipante(),
                ':id_atividade' => $inscricao->getIdAtividade()
            ]);

            $this->pdo->commit();
            return 'CADASTRADO';
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return 'ERRO_CADASTRO';
        }
    }

    public function listarInscricoes(): array
    {

        $sql = " SELECT i.id_inscricao, i.id_participante, p.nome_participante, i.id_atividade, a.nome_atividade, i.status
        FROM inscricoes AS i
        INNER JOIN participantes AS p
        ON i.id_participante = p.id_participante
        INNER JOIN atividades AS a
        ON i.id_atividade = a.id_atividade
        ORDER BY i.id_inscricao DESC";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarInscricaoPorId(int $id): ?array
    {

        $sql = "SELECT i.id_inscricao, i.id_participante, p.nome_participante, i.id_atividade, a.nome_atividade, i.status
        FROM inscricoes AS i
        INNER JOIN participantes AS p
        ON i.id_participante = p.id_participante
        INNER JOIN atividades AS a
        ON i.id_atividade = a.id_atividade
        WHERE id_inscricao = :id";

        $stmt = $this->pdo->prepare($sql);

        $inscricaoId = $stmt->fetch(PDO::FETCH_ASSOC);

        return $inscricaoId ?: null;
    }

    public function cancelarInscricao(int $id): string
    {

        try {
            $sql = "SELECT * FROM inscricoes WHERE id_inscricao = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id
            ]);

            $inscricao = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($inscricao === false) {
                return 'NAO_ENCONTRADO';
            }

            if ($inscricao['status'] === 'CANCELADA') {
                return 'JA_CANCELADA';
            }

            $sql = "UPDATE inscricoes SET status = 'CANCELADA' WHERE id_inscricao = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id
            ]);

            return 'CANCELADA';
        } catch (PDOException $e) {
            return 'ERRO_CANCELAR';
        }
    }
}
