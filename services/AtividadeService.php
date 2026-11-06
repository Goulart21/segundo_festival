
<?php


require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Atividade.php' ;
class AtividadeService
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function cadastarAtividade(Atividade $atividade): string
    {

        $capacidade = $atividade->getCapacidade();

        if ($capacidade <= 0) {

            return 'CAPACIDADE_INVALIDA';
        }

        $sql = "INSERT INTO atividades (nome_atividade, descricao, data_atividade, hora_inicio, hora_fim, local_atividade, capacidade) VALUES
        (:nome_atividade, :descricao, :data_atividade, :hora_inicio, :hora_fim, :local_atividade, :capacidade)";

        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute([
            ':nome_atividade' => $atividade->getNomeAtividade(),
            ':descricao' => $atividade->getDescricao(),
            ':data_atividade' => $atividade->getDataAtividade(),
            ':hora_inicio' => $atividade->getHoraInicio(),
            ':hora_fim' => $atividade->getHoraFim(),
            ':local_atividade' => $atividade->getLocalAtividade(),
            ':capacidade' => $atividade->getCapacidade()
        ])) {
            return 'CADASTRADO';
        }

        return 'ERRO_CADASTRO';
    }

    public function listaratividades(): array
    {

        $sql = "SELECT * FROM atividades";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarAtividadePorId(int $id): ?array
    {

        $sql = "SELECT * FROM atividades WHERE id_atividade = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $atividadeId = $stmt->fetch(PDO::FETCH_ASSOC);

        return $atividadeId ?: null;
    }

    public function atualizarAtividade(int $id, Atividade $atividade): string
    {
        try {

            $capacidade = $atividade->getCapacidade();

            if ($capacidade <= 0) {
                return 'CAPACIDADE_INVALIDA';
            }

            $sql = "SELECT id_atividade FROM atividades WHERE id_atividade = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id
            ]);

            if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
                return 'NAO_ENCONTRADO';
            }

            $sql = "SELECT COUNT(*) FROM inscricoes WHERE id_atividade = :id AND status = 'ATIVA'";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id
            ]);

            $quantidadeInscritos = (int) $stmt->fetchColumn();

            if ($capacidade < $quantidadeInscritos) {
                return 'CAPACIDADE_MENOR_QUE_INSCRITOS';
            }

            $sql = "UPDATE atividades SET nome_atividade = :nome_atividade, descricao = :descricao, data_atividade = :data_atividade, hora_inicio = :hora_inicio, hora_fim = :hora_fim,
            local_atividade = :local_atividade, capacidade = :capacidade WHERE id_atividade = :id";

            $stmt = $this->pdo->prepare($sql);

            if ($stmt->execute([
                ':nome_atividade' => $atividade->getNomeAtividade(),
                ':descricao' => $atividade->getDescricao(),
                ':data_atividade' => $atividade->getDataAtividade(),
                ':hora_inicio' => $atividade->getHoraInicio(),
                ':hora_fim' => $atividade->getHoraFim(),
                ':local_atividade' => $atividade->getLocalAtividade(),
                ':capacidade' => $atividade->getCapacidade(),
                ':id' => $id
            ])) {
                return 'ATUALIZADO';
            }

            return 'ERRO_ATUALIZAR';
        } catch (PDOException $e) {
            return 'ERRO_ATUALIZAR';
        }
    }

    public function excluirAtividade(int $id_atividade): string
    {
        try {

            $sql = "SELECT COUNT(*)
                FROM atividades
                WHERE id_atividade = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id_atividade
            ]);

            if ((int) $stmt->fetchColumn() === 0) {
                return 'NAO_ENCONTRADO';
            }


            $sql = "SELECT COUNT(*)
                FROM inscricoes
                WHERE id_atividade = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id_atividade
            ]);

            if ((int) $stmt->fetchColumn() > 0) {
                return 'POSSUI_INSCRICOES';
            }

            $sql = "DELETE FROM atividades
                WHERE id_atividade = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id_atividade
            ]);

            if ($stmt->rowCount() > 0) {
                return 'EXCLUIDO';
            }

            return 'NAO_ENCONTRADO';
        } catch (PDOException $e) {
            return 'ERRO_EXCLUIR';
        }
    }
}
