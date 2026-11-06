
<?php

require_once __DIR__  . '/../config/config.php';
require_once __DIR__ . '/../models/Participante.php';
class ParticipanteService
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function cadastrarParticipante(Participante $participante): string
    {

        $sql = "SELECT COUNT(*) FROM participantes WHERE email = :email";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':email' => $participante->getEmail()
        ]);

        if ($stmt->fetchColumn() > 0) {
            return 'EMAIL_DUPLICADO';
        }

        $sql = "INSERT INTO participantes (nome_participante, email, telefone) VALUES (:nome_participante, :email, :telefone)";

        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute([
            ':nome_participante' => $participante->getNomeParticipante(),
            ':email' => $participante->getEmail(),
            ':telefone' => $participante->getNomeParticipante(),
        ])) {
            return 'CADASTRADO';
        }

        return 'ERRO_CADASTRO';
    }

    public function listarParticipantes(): array
    {

        $sql = "SELECT * FROM participantes";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarParticipantePorId(int $id): ?array
    {

        $sql = "SELECT * FROM participantes WHERE id_participante = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $participanteId = $stmt->fetch(PDO::FETCH_ASSOC);

        return $participanteId ?: null;
    }

    public function atualizarParticipante(int $id, Participante $participante): string
    {

        $sql = "SELECT * FROM participantes WHERE id_participante = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        if ($stmt->fetch(PDO::FETCH_ASSOC) === null) {
            return 'NAO_ENCONTRADO';
        }

        $sql = "SELECT COUNT(*) FROM participantes WHERE email = :email AND id_participante <> :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':email' => $participante->getEmail(),
            ':id' => $id
        ]);

        if ($stmt->fetchColumn() > 0) {
            return 'EMAIL_DUPLICADO';
        }

        $sql = "UPDATE participantes SET nome_participante = :nome_participante, email = :email, telefone = :telefone  WHERE id_participante = :id";

        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute([
            ':nome_participante' => $participante->getNomeParticipante(),
            ':email' => $participante->getEmail(),
            ':telefone' => $participante->getTelefone(),
            ':id' => $id
        ])) {
            return 'ATUALIZADO';
        }

        return 'ERRO_CADASTRO';
    }

    public function  excluirParticipante(int $id): string
    {
        $sql = "DELETE FROM participantes WHERE id_participante = :id";

        $stmt = $this->pdo->prepare($sql);


        if ($stmt->execute([
            ':id' => $id
        ])) {
            if ($stmt->rowCount() > 0) {
                return 'EXCLUIDO';
            }

            return 'NAO_ENCONTRADO';
        }
        return 'ERRO_EXCLUIR';
    }
}
