<?php
class TicketService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getTicketDetails(int $ticketId, int $clienteId)
    {
        $sql = "SELECT 
                    st.id_ticket AS id, 
                    st.protocolo, 
                    st.assunto, 
                    st.mensagem, 
                    DATE_FORMAT(st.data_cadastro, '%d/%m/%Y %H:%i') AS data_formatada,
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ticket_id' => $ticketId,
            ':cliente_id' => $clienteId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function renderTicket(array $ticketData): string
    {
        if (empty($ticketData)) {
            return '<div class="error">Ticket não encontrado</div>';
        }

        $statusClass = strtolower(str_replace(' ', '-', $ticketData['situacao']));

        ob_start(); ?>
        <div class="ticket-detail <?= htmlspecialchars($statusClass) ?>">
            <div class="ticket-header">
                <div>
                    <h1 class="ticket-protocolo">Ticket #<?= htmlspecialchars($ticketData['protocolo']) ?></h1>
                    <h2 class="ticket-title"><?= htmlspecialchars($ticketData['assunto']) ?></h2>
                </div>
                <div class="ticket-status <?= htmlspecialchars($statusClass) ?>">
                    <?= htmlspecialchars($ticketData['situacao']) ?>
                </div>
            </div>

            <div class="ticket-meta-grid">
                <div class="meta-item">
                    <span class="meta-label">Cliente</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['fantasia']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Contato</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['contato']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Telefone</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['fone']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Celular</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['celular']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">E-mail</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['email']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Data</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['data_formatada']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Prioridade</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['prioridade']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Responsável</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['responsavel'] ?? 'Não atribuído') ?></span>
                </div>
            </div>

            <div class="ticket-content">
                <h3>Mensagem</h3>
                <div class="ticket-message">
                    <?= nl2br(htmlspecialchars($ticketData['mensagem'])) ?>
                </div>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}