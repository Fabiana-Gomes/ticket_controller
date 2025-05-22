<?php
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

$ticket_id = $_GET['id'] ?? 0;
$cliente_id = $_SESSION['cliente_id'];

// Consulta detalhada (segunda query que você forneceu)
$sql = "SELECT 
            st.id_ticket AS id, 
            st.protocolo AS protocolo, 
            st.assunto AS assunto, 
            st.mensagem AS mensagem, 
            st.data_cadastro AS cadastro, 
            st.responsavel AS responsavel,  
            sts.situacao AS situacao, 
            stp.prioridade AS prioridade,
            c.fantasia AS fantasia, 
            c.fone as fone, 
            c.fone2 as fone2, 
            c.celular as celular, 
            c.contato as contato, 
            c.email as email
        FROM 
            clientes c, 
            suporte_ticket st, 
            suporte_ticket_prioridade stp, 
            suporte_ticket_situacao sts
        WHERE 
            st.cliente = c.id AND 
            st.prioridade = stp.id_prioridade AND 
            st.id_situacao = sts.id_situacao AND
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket <?= htmlspecialchars($ticket['protocolo']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="ticket-container">
        <h1>Ticket #<?= htmlspecialchars($ticket['protocolo']) ?></h1>
        
        <div class="ticket-header">
            <p><strong>Cliente:</strong> <?= htmlspecialchars($ticket['fantasia']) ?></p>
            <p><strong>Contato:</strong> <?= htmlspecialchars($ticket['contato']) ?> (<?= htmlspecialchars($ticket['email']) ?>)</p>
            <p><strong>Telefones:</strong> <?= htmlspecialchars($ticket['fone']) ?> / <?= htmlspecialchars($ticket['celular']) ?></p>
        </div>
        
        <div class="ticket-details">
            <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($ticket['cadastro'])) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($ticket['situacao']) ?></p>
            <p><strong>Prioridade:</strong> <?= htmlspecialchars($ticket['prioridade']) ?></p>
            <p><strong>Responsável:</strong> <?= htmlspecialchars($ticket['responsavel'] ?? 'Não atribuído') ?></p>
        </div>
        
        <div class="ticket-content">
            <h2><?= htmlspecialchars($ticket['assunto']) ?></h2>
            <div class="message"><?= nl2br(htmlspecialchars($ticket['mensagem'])) ?></div>
        </div>
        
        <a href="index.php" class="back-button">← Voltar para a lista</a>
    </div>
</body>
</html>