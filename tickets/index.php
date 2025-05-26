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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=close" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/modal.css">
</head>

<body>
    <div class="title-container">
        <h1>
            <i class="bi bi-card-checklist title-icon"></i>
            <span class="title-text">Solicitações da unidade</span>
            <div class="title-underline"></div>
        </h1>
    </div>
    <div class="container-fluid mb-3">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Alinhar botão à direita -->
                <div class="text-end">
                    <button class="btn filter-toggle-btn" id="filterToggleBtn">
                        <i class="bi bi-funnel me-2"></i>
                        Filtrar Tickets
                        <span class="ms-2">
                            <i class="bi bi-chevron-down float-end" id="filterIcon"></i>
                        </span>

                    </button>
                </div>

                <!-- Card do filtro (inicialmente oculto) -->
                <div class="card filter-card" id="filterCard">

                    <div class="card-body">
                        <form class="row g-2 align-items-end" id="filterForm">
                            <!-- Status Filter -->
                            <div class="col-md-3">
                                <label for="statusFilter" class="form-label">Status</label>
                                <select class="form-select" id="statusFilter">
                                    <option selected value="">Todos</option>
                                    <option value="aberto">Aberto</option>
                                    <option value="em-andamento">Em Andamento</option>
                                    <option value="resolvido">Resolvido</option>
                                    <option value="fechado">Fechado</option>
                                </select>
                            </div>


                            <!-- Start Date -->
                            <div class="col-md-3">
                                <label for="startDate" class="form-label">Data Inicial</label>
                                <input type="date" class="form-control" id="startDate">
                            </div>

                            <!-- End Date -->
                            <div class="col-md-3">
                                <label for="endDate" class="form-label">Data Final</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>

                            <!-- Buttons -->
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
            </div>
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
                $statusClass = strtolower(str_replace(' ', '-', $ticket['situacao']));
            ?>
                <div class="post-it <?= $statusClass ?>" onclick="abrirModalTicket(<?= $ticket['id'] ?>)">
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
    </div>

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