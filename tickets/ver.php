<?php
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

$ticket_id = $_GET['id'] ?? 0;
$cliente_id = $_SESSION['cliente_id'];

$sql = "SELECT 
            st.id_ticket AS id, 
            st.protocolo, 
            st.assunto, 
            st.mensagem, 
            st.data_cadastro, 
            st.responsavel,  
            sts.situacao, 
            stp.prioridade,
            c.fantasia, 
            c.fone, 
            c.celular, 
            c.contato, 
            c.email
        FROM 
            clientes c
            JOIN suporte_ticket st ON st.cliente = c.id
            JOIN suporte_ticket_prioridade stp ON st.prioridade = stp.id_prioridade
            JOIN suporte_ticket_situacao sts ON st.id_situacao = sts.id_situacao
        WHERE 
            st.id_ticket = :ticket_id AND
            st.cliente = :cliente_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':ticket_id' => $ticket_id,
    ':cliente_id' => $cliente_id
]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die('Ticket não encontrado ou você não tem permissão para visualizá-lo.');
}

$statusClass = strtolower(str_replace(' ', '-', $ticket['situacao']));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?= htmlspecialchars($ticket['protocolo']) ?></title>
    <link rel="stylesheet" href="../assets/css/ver.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="ticket-view-container">
        <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        
        <div class="ticket-detail <?= $statusClass ?>">
            <div class="ticket-header">
                <div>
                    <h1 class="ticket-protocolo">Ticket #<?= htmlspecialchars($ticket['protocolo']) ?></h1>
                    <h2 class="ticket-title"><?= htmlspecialchars($ticket['assunto']) ?></h2>
                </div>
                <div class="ticket-status <?= $statusClass ?>">
                    <?= htmlspecialchars($ticket['situacao']) ?>
                </div>
            </div>
            
            <div class="ticket-meta-grid">
                <div class="meta-item">
                    <span class="meta-label">Cliente</span>
                    <span class="meta-value"><?= htmlspecialchars($ticket['fantasia']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Contato</span>
                    <span class="meta-value"><?= htmlspecialchars($ticket['contato']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Telefone</span>
                    <span class="meta-value"><?= htmlspecialchars($ticket['fone']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Celular</span>
                    <span class="meta-value"><?= htmlspecialchars($ticket['celular']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">E-mail</span>
                    <span class="meta-value"><?= htmlspecialchars($ticket['email']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Data</span>
                    <span class="meta-value"><?= date('d/m/Y H:i', strtotime($ticket['data_cadastro'])) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Prioridade</span>
                    <span class="meta-value"><?= htmlspecialchars($ticket['prioridade']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Responsável</span>
                    <span class="meta-value"><?= htmlspecialchars($ticket['responsavel'] ?? 'Não atribuído') ?></span>
                </div>
            </div>
            
            <div class="ticket-content">
                <h3>Mensagem</h3>
                <div class="ticket-message">
                    <?= nl2br(htmlspecialchars($ticket['mensagem'])) ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>