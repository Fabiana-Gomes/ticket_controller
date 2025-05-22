<?php
require_once '../includes/db_connection.php';
require_once '../includes/auth.php';

$cliente_id = $_SESSION['cliente_id'];

$sql = "SELECT 
            st.*,
            sts.situacao AS situacao
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <h1>Meus Tickets</h1>
    <table class="tickets-table">
        <thead>
            <tr>
                <th>Protocolo</th>
                <th>Assunto</th>
                <th>Data</th>
                <th>Situação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><a href="ver.php?id=<?= $ticket['id_ticket'] ?>"><?= htmlspecialchars($ticket['protocolo']) ?></a></td>
                    <td><?= htmlspecialchars($ticket['assunto']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($ticket['data_cadastro'])) ?></td>
                    <td><?= htmlspecialchars($ticket['situacao'] ?? 'N/A') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>