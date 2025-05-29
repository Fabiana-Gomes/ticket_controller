<?php
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

$cliente_id = $_SESSION['cliente_id'];

$sql = "SELECT 
            st.id_ticket as id,
            st.protocolo,
            st.assunto,
            st.mensagem,
            st.data_cadastro,
            sts.situacao
        FROM 
            suporte_ticket st
            JOIN suporte_ticket_situacao sts ON st.id_situacao = sts.id_situacao
        WHERE 
            st.cliente = :cliente_id 
        ORDER BY 
            st.data_cadastro DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':cliente_id' => $cliente_id]);
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Meus Tickets</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=close" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/modal.css">
</head>

<body>
    <div class="title-container">
        <h1>
            <i class="bi bi-card-checklist title-icon"></i>
            <span class="title-text">Tickets solicitados</span>
            <div class="title-underline"></div>
        </h1>
    </div>
    <div class="filter-wrapper">
        <div class="filter-container">
            <button class="btn filter-toggle-btn" id="filterToggleBtn">
                <i class="bi bi-funnel me-2"></i>
                <span class="filter-text">Filtrar Tickets</span>
                <i class="bi bi-chevron-down ms-2" id="filterIcon"></i>
            </button>
        </div>
    </div>
    <div class="card filter-card" id="filterCard">
        <div class="card-body">
            <form class="row g-2 align-items-end" id="filterForm">
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option selected value="">Todos</option>
                        <option value="concluido">Concluídos</option>
                        <option value="pedagogico">Pedagógico</option>
                        <option value="ead">EAD</option>
                        <option value="gestor">Gestor</option>
                        <option value="financeiro">Financeiro</option>
                        <option value="presencial">Presencial</option>
                        <option value="comercial">Comercial</option>
                        <option value="treinamentos">Treinamentos</option>
                        <option value="cancelado">Cancelados</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="startDate" class="form-label">Data Inicial</label>
                    <input type="date" class="form-control" id="startDate">
                </div>
                <div class="col-md-3">
                    <label for="endDate" class="form-label">Data Final</label>
                    <input type="date" class="form-control" id="endDate">
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-sm me-1 reset-btn" id="resetFilters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                    <button type="submit" class="btn btn-sm filter-btn">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="no-tickets-container">
            <div class="no-tickets-card">
                <i class="bi bi-inbox no-tickets-icon"></i>
                <h3 class="no-tickets-title">Nenhum ticket encontrado</h3>
            </div>
        </div>
    <?php else: ?>
        <div class="post-it-container">
            <?php foreach ($tickets as $ticket):
                $statusMapping = [
                    'Resolvido' => 'resolvido',
                    'Respondido' => 'resolvido',
                    'Corrigido' => 'resolvido',
                    'Instalação' => 'instalacao',
                    'Livros Lucrativos' => 'livros-lucrativos',
                    'Instrutor Comunicativo' => 'instrutor-comunicativo',
                    'Contratação OM DIGITAL' => 'om-digital',
                    'ClassStudio' => 'classstudio',
                    'CRM' => 'crm',
                    'Carteira de Estudante AERGS' => 'carteira-estudante',
                    'Treinamento' => 'treinamento',
                    'Em acompanhamento' => 'acompanhamento',
                    'Correção de cursos' => 'correcao-cursos',
                    'Melhoria e ajuste método' => 'melhoria-metodo',
                    'Treinamento ferramentas' => 'treinamento-ferramentas',
                    'Gestor Melhorias' => 'gestor-melhorias',
                    'Sugestão' => 'sugestao',
                    'Cancelado' => 'cancelado',
                    'Desinstalação' => 'desinstalacao',
                    'Loja Virtual' => 'loja-virtual',
                    'Ajustes Loja' => 'ajustes-loja',
                    'Instalação EAD' => 'instalacao-ead',
                    'Site' => 'site',
                    'Ajustes Site' => 'ajustes-site',
                    'LP' => 'lp',
                    'B.E.L.' => 'bel',
                    'Edpay' => 'edpay',
                    'Gestor' => 'gestor',
                    'Sender' => 'sender',
                    'Meu App de Cursos' => 'app-cursos',
                    'Pagar.me' => 'pagarme',
                    'Liberação de Migração' => 'migracao',
                    'EAD' => 'ead'
                ];

                $statusClass = $statusMapping[$ticket['situacao']] ?? 'aberto';
            ?>
                <div class="post-it <?= $statusClass ?>" data-id="<?= $ticket['id'] ?>">
                    <div class="post-it-header">
                        <div class="post-it-protocolo">#<?= htmlspecialchars($ticket['protocolo']) ?></div>
                        <div class="post-it-date"><?= date('d/m/Y', strtotime($ticket['data_cadastro'])) ?></div>
                    </div>
                    <div class="post-it-title"><?= htmlspecialchars($ticket['assunto']) ?></div>
                    <div class="post-it-content">
                        <?= substr(htmlspecialchars($ticket['mensagem']), 0, 100) ?>...
                    </div>
                    <div class="post-it-footer">
                        <span class="post-it-status"><?= htmlspecialchars($ticket['situacao']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div id="ticketModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="fecharModal()">
                <span class="material-symbols-outlined">close</span>
            </span>
            <div id="ticketDetalhes">
                <div class="loading">Carregando ticket...</div>
            </div>
        </div>
    </div>

    <script src="../assets/js/scripts.js"></script>
</body>

</html>