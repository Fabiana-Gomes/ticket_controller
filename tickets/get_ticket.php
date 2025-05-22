<?php
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

header('Content-Type: text/html; charset=utf-8');

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
    die('<div class="error">Ticket não encontrado ou você não tem permissão para visualizá-lo.</div>');
}

$statusClass = strtolower(str_replace(' ', '-', $ticket['situacao']));
?>

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

<style>
    .ticket-detail {
        position: relative;
        min-height: auto;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        margin: 0;
        max-width: 100%;
    }
    
    .ticket-detail::after {
        content: "";
        position: absolute;
        bottom: 15px;
        right: 15px;
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, transparent 0%, transparent 50%, rgba(0,0,0,0.05) 50%, rgba(0,0,0,0.05) 100%);
    }
</style>