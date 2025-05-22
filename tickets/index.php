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
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/modal.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

</head>

<body>
    <div class="container">
        <h1>Solicitações da unidade</h1>

        <?php if (empty($tickets)): ?>
            <div class="no-tickets">Nenhum ticket encontrado.</div>
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
            <span class="close-modal" onclick="fecharModal()">&times;</span>
            <div id="ticketDetalhes">
                <div class="loading">Carregando ticket...</div>
            </div>
        </div>
    </div>

    <script src="../assets/js/scripts.js"></script>
</body>

</html>