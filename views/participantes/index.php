<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/tema.css">
    <title>Participantes</title>
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: var(--festival-verde);">
            <div class="container-fluid">
                <a class="navbar-brand fw-semibold" href="#">Experiência Viva</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="?pagina=principal">Principal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?pagina=dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="?pagina=participantes">Participantes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?pagina=atividades">Atividades</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?pagina=inscricoes">Inscrições</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container-fluid p-4">

        <?php $modoEdicao = $participanteEdicao !== null; ?>
        <section class="mt-4">
            <p class="fw-semibold small-text" style="color: var(--festival-terracota)">
                Sistema de Gerenciamento
            </p>

            <h1 class="fw-semibold" style="color:var(--festival-folhagem)">
                Participantes
            </h1>
        </section>

        <?php $statusCadastrado = $_GET['status'] ?? ''; ?>

        <?php if ($statusCadastrado === 'cadastrado'): ?>

            <div class="alert alert-success">
                Participante cadastrado com sucesso!
            </div>

        <?php elseif ($statusCadastrado === 'email_duplicado'): ?>

            <div class="alert alert-warning">
                Este e-mail já está cadastrado.
            </div>

        <?php elseif ($statusCadastrado === 'erro'): ?>

            <div class="alert alert-danger">
                Não foi possível cadastrar o participante.
            </div>

        <?php elseif ($statusCadastrado === 'atualizado'): ?>

            <div class="alert alert-success">
                Participante atualizado com sucesso!
            </div>

        <?php endif; ?>

        <?php $modoEdicao = $participanteEdicao !== null; ?>
        <section class="card shadow-sm mt-4">

            <div class="card-body p-4">
                <h2 class="h5 fw-semibold">Cadastrar Participantes</h2>

                <form method="POST" action="<?= $modoEdicao ? '?pagina=participantes&editar=' . (int) ($participanteEdicao['id_participante'] ?? '')
                                                : '?pagina=participantes' ?>">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nome_participante" class="form-label">Nome:</label>
                            <input type="text" id="nome_participante" name="nome_participante" class="form-control"
                                value="<?= htmlspecialchars(
                                            (string) ($participanteEdicao['nome_participante'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="<?= htmlspecialchars(
                                            (string) ($participanteEdicao['email'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label for="telefone" class="form-label">Telefone:</label>
                            <input type="tel" id="telefone" name="telefone" class="form-control"
                                value="<?= htmlspecialchars(
                                            (string) ($participanteEdicao['telefone'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>" required>
                        </div>
                    </div>
                    <div class="m-4">
                        <button type="submit" class="btn btn-festival">
                            Cadastrar
                        </button>

                        <?php if ($modoEdicao): ?>
                            <a href="?pagina=participantes" class="btn btn-outline-secondary ms-2">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

        </section>

        <section aria-labelledby="Tabela de Participantes">

            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">

                            <thead class="table-ligth">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Telefone</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>

                            <tbody id="tabelaParticipantes">

                                <?php if (empty($participantes)): ?>
                                    <tr>
                                        <td>
                                            Nenhum participante cadastrado
                                        </td>
                                    </tr>
                                <?php else: ?>

                                    <?php foreach ($participantes as $participante): ?>

                                        <tr>
                                            <td>
                                                <?= htmlspecialchars(
                                                    (string) $participante['id_participante'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    (string) $participante['nome_participante'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    (string) $participante['email'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    (string) $participante['telefone'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                <a href="?pagina=participantes&editar=<?= (int) $participante['id_participante'] ?>"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    Editar
                                                </a>

                                                <form method="POST" action="?pagina=participantes&acao=excluir&id=<?= (int) $participante['id_participante'] ?>"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir esse participante ?')">
                                                    <button class="btn btn-danger btn-sm" type="submit">Excluir</button>
                                                </form>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-top mt-5 pt-4 text-center">
        <p class="small text-secondary">
            Experiência Viva - Sistema de Gerenciamento
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
</body>

</html>