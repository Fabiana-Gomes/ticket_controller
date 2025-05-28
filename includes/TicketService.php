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
                    st.protocolo,
                    st.cliente,
                    st.assunto,
                    st.mensagem,
                    DATE_FORMAT(st.data_cadastro, '%d/%m/%Y %H:%i') AS data_cadastro,
                    st.interno,
                    sts.situacao
                FROM 
                    suporte_ticket st
                    JOIN suporte_ticket_situacao sts ON st.id_situacao = sts.id_situacao
                WHERE 
                    st.id_ticket = :ticket_id AND
                    st.cliente = :cliente_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ticket_id' => $ticketId,
            ':cliente_id' => $clienteId
        ]);

        $ticketData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticketData) {
            return null;
        }

        if ((int)$ticketData['interno'] === 0) {
            $sqlRespostas = "SELECT 
                                resposta,
                                DATE_FORMAT(data_resposta, '%d/%m/%Y %H:%i') as data_resposta
                            FROM 
                                suporte_ticket_resposta
                            WHERE 
                                id_ticket = :ticket_id
                            ORDER BY 
                                data_resposta ASC";

            $stmtRespostas = $this->pdo->prepare($sqlRespostas);
            if ($stmtRespostas->execute([':ticket_id' => $ticketId])) {
                $ticketData['respostas'] = $stmtRespostas->fetchAll(PDO::FETCH_ASSOC);
            } else {
                error_log("Erro ao buscar respostas: " . print_r($stmtRespostas->errorInfo(), true));
                $ticketData['respostas'] = [];
            }
        } else {
            $ticketData['respostas'] = [];
        }

        return $ticketData;
    }

    public function renderTicket(array $ticketData): string
    {
        if (empty($ticketData)) {
            return '<div class="error-message">Ticket não encontrado</div>';
        }

        $statusClass = strtolower(str_replace(' ', '-', $ticketData['situacao']));
        $borderColorClass = $statusClass;
        ob_start(); ?>

        <div class="ticket-detail-container">
            <div class="ticket-header">
                <div class="ticket-title-group">
                    <h1 class="ticket-protocolo">Ticket #<?= htmlspecialchars($ticketData['protocolo']) ?></h1>
                    <h2 class="ticket-assunto"><?= htmlspecialchars($ticketData['assunto']) ?></h2>
                </div>
                <span class="ticket-status <?= htmlspecialchars($statusClass) ?>">
                    <?= htmlspecialchars($ticketData['situacao']) ?>
                </span>
            </div>

            <div class="ticket-meta-grid">
                <div class="meta-item">
                    <span class="meta-label">Cliente ID</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['cliente']) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Data Cadastro</span>
                    <span class="meta-value"><?= htmlspecialchars($ticketData['data_cadastro']) ?></span>
                </div>
            </div>
            <div class="ticket-message-container">
                <h3 class="section-title">Solicitação</h3>
                <div class="ticket-message-content">
                    <?= nl2br(htmlspecialchars($ticketData['mensagem'])) ?>
                </div>
            </div>
            <?php if (!empty($ticketData['respostas'])): ?>
                <div class="ticket-responses-section">
                    <h3 class="section-title">Resolução</h3>
                    <div class="responses-list">
                        <?php foreach ($ticketData['respostas'] as $resposta): ?>
                            <div class="response-item <?= htmlspecialchars($borderColorClass) ?>">
                                <div class="response-header">
                                    <span class="response-date"><?= htmlspecialchars($resposta['data_resposta']) ?></span>
                                </div>
                                <div class="response-content">
                                    <?= nl2br(htmlspecialchars($resposta['resposta'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ((int)$ticketData['interno'] === 0): ?>
                <div class="ticket-notification">
                    <i class="notification-icon"></i>
                    <span>Este ticket ainda não possui respostas.</span>
                </div>
            <?php endif; ?>
        </div>
        </div>
<?php
        return ob_get_clean();
    }
}