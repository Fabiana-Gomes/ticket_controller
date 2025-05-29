<?php
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';
require_once '../includes/TicketService.php';

header('Content-Type: text/html; charset=utf-8');

try {

    if (empty($_SESSION['cliente_id'])) {
        throw new Exception('Acesso não autorizado');
    }

    $ticketId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    if (!$ticketId) {
        throw new Exception('ID do ticket inválido');
    }

    $clienteId = (int) $_SESSION['cliente_id'];
    $ticketService = new TicketService($pdo);
    $ticketData = $ticketService->getTicketDetails($ticketId, $clienteId);

    if (!$ticketData) {
        throw new Exception('Ticket não encontrado ou você não tem permissão para visualizá-lo.');
    }

    echo $ticketService->renderTicket($ticketData);
} catch (Exception $e) {
    http_response_code(400);

    echo '<div class="error">' . htmlspecialchars($e->getMessage()) . '</div>';

    error_log('Erro em get_ticket.php: ' . $e->getMessage());
}