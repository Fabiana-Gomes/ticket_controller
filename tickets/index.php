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

// Verifica se há tickets antes de exibir
if (empty($tickets)) {
    $noTicketsMessage = "Nenhum ticket encontrado.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Meus Tickets</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
    <div class="container">
        <h1>Meus Tickets</h1>

        <?php if (isset($noTicketsMessage)): ?>
            <div class="no-tickets">
                <?= $noTicketsMessage ?>
            </div>
        <?php else: ?>
            <div class="post-it-container">
                <?php foreach ($tickets as $ticket):
                    $statusClass = strtolower(str_replace(' ', '-', $ticket['situacao']));
                ?>
                    <div class="post-it <?= $statusClass ?>">
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
                            <a href="ver.php?id=<?= $ticket['id'] ?>" class="view-button">
                                <span class="material-symbols-outlined">open_in_full</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="../assets/js/scripts.js"></script>
</body>
</html>