<?php
require 'api.php';

$leagueId = isset($_GET['league']) ? $_GET['league'] : 71;
$teamName = isset($_GET['team']) ? $_GET['team'] : '';
$season = 2023; // Temporada 2023

// Busca os próximos jogos
$nextFixtures = callAPI('fixtures', ['league' => $leagueId, 'season' => $season, 'next' => 5]);
if (isset($nextFixtures['errors']) && !empty($nextFixtures['errors'])) {
    $nextFixturesError = "Erro ao buscar próximos jogos: " . $nextFixtures['errors'][0];
} else {
    $nextFixturesError = null;
}

// Busca os últimos resultados
$lastResults = callAPI('fixtures', ['league' => $leagueId, 'season' => $season, 'last' => 5]);
if (isset($lastResults['errors']) && !empty($lastResults['errors'])) {
    $lastResultsError = "Erro ao buscar últimos resultados: " . $lastResults['errors'][0];
} else {
    $lastResultsError = null;
}

// Busca jogos de um time específico
if ($teamName) {
    $teams = callAPI('teams', ['search' => $teamName]);
    if (isset($teams['response']) && !empty($teams['response'])) {
        $teamId = $teams['response'][0]['team']['id'];
    } else {
        $teamId = null;
    }

    if ($teamId) {
        // Busca os próximos jogos do time
        $teamFixtures = callAPI('fixtures', ['team' => $teamId, 'season' => $season, 'next' => 5]);
        if (isset($teamFixtures['errors']) && !empty($teamFixtures['errors'])) {
            $teamFixturesError = "Erro ao buscar jogos do time: " . $teamFixtures['errors'][0];
        } else {
            $teamFixturesError = null;
        }

        // Busca os últimos resultados do time
        $teamResults = callAPI('fixtures', ['team' => $teamId, 'season' => $season, 'last' => 5]);
        if (isset($teamResults['errors']) && !empty($teamResults['errors'])) {
            $teamResultsError = "Erro ao buscar últimos resultados do time: " . $teamResults['errors'][0];
        } else {
            $teamResultsError = null;
        }

    } else {
        $teamFixturesError = "Time não encontrado.";
        $teamResultsError = "Time não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Jogos de Futebol Temporada 2023</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="text-center mb-4">
            <i class="fas fa-futbol me-2"></i>Sistema de Jogos de Futebol Temporada 2023
        </h1>

        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="" class="mb-3">
                            <div class="mb-3">
                                <label for="league" class="form-label">Escolha o Campeonato:</label>
                                <select name="league" id="league" class="form-select">
                                    <option value="71">Campeonato Brasileiro</option>
                                    <option value="39">Premier League</option>
                                    <option value="140">La Liga</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Carregar
                            </button>
                        </form>

                        <form method="GET" action="">
                            <div class="mb-3">
                                <label for="team" class="form-label">Pesquisar por Time:</label>
                                <input type="text" name="team" id="team" class="form-control" placeholder="Nome do Time">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0"><i class="fas fa-calendar me-2"></i>Próximos Jogos</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($nextFixturesError): ?>
                            <div class="alert alert-danger"><?= $nextFixturesError ?></div>
                        <?php elseif (isset($nextFixtures['response']) && !empty($nextFixtures['response'])): ?>
                            <div class="list-group">
                                <?php foreach ($nextFixtures['response'] as $fixture): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-center" style="width: 40%">
                                                <img src="<?= $fixture['teams']['home']['logo'] ?>" alt="Home" style="height: 30px" class="mb-1">
                                                <div class="small"><?= $fixture['teams']['home']['name'] ?></div>
                                            </div>
                                            <div class="text-center">
                                                <div class="badge bg-secondary mb-1">
                                                    <?= date('d/m/Y', strtotime($fixture['fixture']['date'])) ?>
                                                </div>
                                                <div class="small">
                                                    <?= date('H:i', strtotime($fixture['fixture']['date'])) ?>
                                                </div>
                                            </div>
                                            <div class="text-center" style="width: 40%">
                                                <img src="<?= $fixture['teams']['away']['logo'] ?>" alt="Away" style="height: 30px" class="mb-1">
                                                <div class="small"><?= $fixture['teams']['away']['name'] ?></div>
                                            </div>
                                        </div>
                                        <div class="text-center small mt-2 text-muted">
                                            <?= $fixture['fixture']['venue']['name'] ?? 'Estádio não disponível' ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Próximos jogos não disponíveis devido ao plano da API ser free e disponibiizar somente os resutados da temporada 2023 que não há mais jogos programados.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0"><i class="fas fa-history me-2"></i>Últimos Resultados</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($lastResultsError): ?>
                            <div class="alert alert-danger"><?= $lastResultsError ?></div>
                        <?php elseif (isset($lastResults['response']) && !empty($lastResults['response'])): ?>
                            <div class="list-group">
                                <?php foreach ($lastResults['response'] as $fixture): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-center" style="width: 40%">
                                                <img src="<?= $fixture['teams']['home']['logo'] ?>" alt="Home" style="height: 30px" class="mb-1">
                                                <div class="small"><?= $fixture['teams']['home']['name'] ?></div>
                                            </div>
                                            <div class="h4 mb-0">
                                                <?= $fixture['goals']['home'] ?> - <?= $fixture['goals']['away'] ?>
                                            </div>
                                            <div class="text-center" style="width: 40%">
                                                <img src="<?= $fixture['teams']['away']['logo'] ?>" alt="Away" style="height: 30px" class="mb-1">
                                                <div class="small"><?= $fixture['teams']['away']['name'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Nenhum resultado recente encontrado.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($teamName): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h5 mb-0">
                            <i class="fas fa-shield-alt me-2"></i>Jogos do Time: <?= $teamName ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <h3 class="h6 mb-3">Próximos Jogos</h3>
                        <?php if ($teamFixturesError): ?>
                            <div class="alert alert-danger"><?= $teamFixturesError ?></div>
                        <?php elseif (isset($teamFixtures['response']) && !empty($teamFixtures['response'])): ?>
                            <div class="list-group mb-4">
                                <?php foreach ($teamFixtures['response'] as $fixture): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-center" style="width: 40%">
                                                <img src="<?= $fixture['teams']['home']['logo'] ?>" alt="Home" style="height: 30px" class="mb-1">
                                                <div class="small"><?= $fixture['teams']['home']['name'] ?></div>
                                            </div>
                                            <div class="text-center">
                                                <div class="badge bg-secondary mb-1">
                                                    <?= date('d/m/Y', strtotime($fixture['fixture']['date'])) ?>
                                                </div>
                                                <div class="small">
                                                    <?= date('H:i', strtotime($fixture['fixture']['date'])) ?>
                                                </div>
                                            </div>
                                            <div class="text-center" style="width: 40%">
                                                <img src="<?= $fixture['teams']['away']['logo'] ?>" alt="Away" style="height: 30px" class="mb-1">
                                                <div class="small"><?= $fixture['teams']['away']['name'] ?></div>
                                            </div>
                                        </div>
                                        <div class="text-center small mt-2 text-muted">
                                            <?= $fixture['fixture']['venue']['name'] ?? 'Estádio não disponível' ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Próximos jogos não disponíveis devido ao plano da API ser free e disponibiizar somente os resutados da temporada 2023 que não há mais jogos programados.
                            </div>
                        <?php endif; ?>

                        <h3 class="h6 mb-3">Últimos Resultados</h3>
                        <?php if ($teamResultsError): ?>
                            <div class="alert alert-danger"><?= $teamResultsError ?></div>
                        <?php elseif (isset($teamResults['response']) && !empty($teamResults['response'])): ?>
                            <div class="list-group">
                                <?php foreach ($teamResults['response'] as $fixture): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-center" style="width: 40%">
                                                <img src="<?= $fixture['teams']['home']['logo'] ?>" alt="Home" style="height: 30px" class="mb-1">
                                                <div class="small"><?= $fixture['teams']['home']['name'] ?></div>
                                            </div>
                                            <div class="h4 mb-0">
                                                <?= $fixture['goals']['home'] ?> - <?= $fixture['goals']['away'] ?>
                                            </div>
                                            <div class="text-center" style="width: 40%">
                                                <img src="<?= $fixture['teams']['away']['logo'] ?>" alt="Away" style="height: 30px" class="mb-1">
                                                <div class="small"><?= $fixture['teams']['away']['name'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Nenhum resultado recente encontrado para este time.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>