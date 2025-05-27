<?php
class TicketService
{
    private $pdo;

    // Construtor da classe recebe uma instância do PDO (conexão com banco)
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Recupera os detalhes de um ticket específico pertencente a um cliente específico
    public function getTicketDetails(int $ticketId, int $clienteId)
    {
        // Consulta SQL para buscar os dados do ticket + situação (somente os campos necessários)
        $sql = "SELECT 
                    st.protocolo,
                    st.cliente,
                    st.assunto,
                    st.mensagem,
                    DATE_FORMAT(st.data_cadastro, '%d/%m/%Y %H:%i') AS data_cadastro,
                    DATE_FORMAT(st.data_atualizacao, '%d/%m/%Y %H:%i') AS data_atualizacao,
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

        // Busca respostas do ticket (resposta e data formatada)
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

        return $ticketData;
    }

    // Gera HTML para exibir o conteúdo do ticket
    public function renderTicket(array $ticketData): string
    {
        if (empty($ticketData)) {
            return '<div class="error">Ticket não encontrado</div>';
        }

        // Classe CSS com base na situação do ticket (ex: aberto -> .aberto)
        $statusClass = strtolower(str_replace(' ', '-', $ticketData['situacao']));

        // Começa a capturar o conteúdo HTML
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

            <!-- Metadados do ticket -->
            <div class="ticket-meta-grid">
                <div><strong>Cliente ID:</strong> <?= htmlspecialchars($ticketData['cliente']) ?></div>
                <div><strong>Data Cadastro:</strong> <?= htmlspecialchars($ticketData['data_cadastro']) ?></div>
                <div><strong>Última Atualização:</strong> <?= htmlspecialchars($ticketData['data_atualizacao']) ?></div>
            </div>

            <div class="ticket-content">
                <h3>Mensagem</h3>
                <div class="ticket-message">
                    <?= nl2br(htmlspecialchars($ticketData['mensagem'])) ?>
                </div>
            </div>

            <!-- Respostas -->
            <?php if (!empty($ticketData['respostas'])): ?>
                <div class="ticket-responses">
                    <h3>Respostas</h3>
                    <?php foreach ($ticketData['respostas'] as $resposta): ?>
                        <div class="response-item">
                            <div class="response-header">
                                <span class="response-author">Atendente</span>
                                <span class="response-date">
                                    <?= htmlspecialchars($resposta['data_resposta']) ?>
                                </span>
                            </div>
                            <div class="response-content">
                                <?= nl2br(htmlspecialchars($resposta['resposta'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill"></i> Este ticket ainda não possui respostas.
                </div>
            <?php endif; ?>
        </div>
        <?php
        // Retorna o conteúdo HTML gerado
        return ob_get_clean();
    }
}
