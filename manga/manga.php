<?php
// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "manga");

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Lógica para excluir uma fala
if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM falas WHERE id = $id");
    header("Location: manga.php");
    exit();
}

// Lógica para editar uma fala
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $novaFala = $conn->real_escape_string($_POST['novaFala']);
    $conn->query("UPDATE falas SET fala = '$novaFala' WHERE id = $id");
    header("Location: manga.php");
    exit();
}

// Lógica para adicionar uma nova fala
if (isset($_POST['addFala'])) {
    $personagemId = intval($_POST['personagem']);
    $fala = $conn->real_escape_string($_POST['fala']);
    $conn->query("INSERT INTO falas (personagem_id, fala) VALUES ($personagemId, '$fala')");
    header("Location: manga.php");
    exit();
}

// Buscar mensagens para exibição
$result = $conn->query("SELECT falas.id, personagens.id AS personagem_id, personagens.nome, personagens.cor, falas.fala 
                        FROM falas 
                        LEFT JOIN personagens ON falas.personagem_id = personagens.id 
                        ORDER BY falas.id ASC");

// Buscar personagens para o seletor
$personagens = $conn->query("SELECT * FROM personagens");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mangá - Chat</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Adicionar estilo para ocultar/mostrar os botões */
        .message-actions {
            display: none; /* Ocultar por padrão */
            flex-direction: row; /* Alinhar os botões na mesma linha */
            gap: 10px; /* Espaço entre os botões */
            margin-left: 10px;
        }

        .message:hover .message-actions {
            display: flex; /* Mostrar as ações quando o mouse estiver sobre a mensagem */
        }

        .message-actions button {
            background-color: #333;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .message-actions button:hover {
            background-color: #007bff;
        }

        .message-actions input {
            border-radius: 5px;
            padding: 5px;
            border: 1px solid #ccc;
        }

        /* Remover o ícone dos três pontos */
        .menu-button {
            display: none; /* Não exibir o ícone dos três pontos */
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-messages">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="message">
                    <?php
                        // Se a fala for do narrador (ID 0), não mostrar o nome
                        if ($row['personagem_id'] != 0) {
                            echo '<span class="character" style="color: ' . $row['cor'] . ';">[' . htmlspecialchars($row['nome']) . ']:</span>';
                        }

                        // Destacar personagens na fala
                        $fala = htmlspecialchars($row['fala']);
                        $personagens_para_destacar = [];

                        // Buscar todos os personagens novamente (certificando-se que o ponteiro não está avançado)
                        $personagens->data_seek(0); // Resetar o ponteiro do banco de dados para que possamos usá-lo novamente
                        while ($personagem = $personagens->fetch_assoc()) {
                            $personagens_para_destacar[$personagem['id']] = [
                                'nome' => $personagem['nome'],
                                'cor' => $personagem['cor']
                            ];
                        }

                        // Destacar o nome dos personagens nas falas
                        foreach ($personagens_para_destacar as $id => $personagem) {
                            if ($id != 0) {
                                $fala = preg_replace('/\b(' . preg_quote($personagem['nome'], '/') . ')\b/', 
                                                    '<span style="color: ' . $personagem['cor'] . ';">$1</span>', $fala);
                            }
                        }
                    ?>

                    <span class="text"><?= $fala ?></span>

                    <!-- Ações de editar e excluir, escondidas até passar o mouse -->
                    <div class="message-actions">
                        <!-- Formulário para editar a fala -->
                        <form method="post" class="inline-form">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="text" name="novaFala" placeholder="Editar fala" required>
                            <button type="submit" name="edit">Editar</button>
                        </form>

                        <!-- Formulário para excluir a fala -->
                        <form method="post" class="inline-form">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete" class="delete-button">Excluir</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="chat-input">
            <form method="post">
                <select name="personagem" required>
                    <option value="0" style="color: #aaa;">Narrador</option>
                    <?php 
                        // Reexibir os personagens na caixa de seleção
                        $personagens->data_seek(0); // Resetar o ponteiro para a caixa de seleção
                        while ($row = $personagens->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" style="color: <?= $row['cor'] ?? '#000' ?>">
                            <?= htmlspecialchars($row['nome']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="fala" placeholder="Digite uma fala..." required>
                <button type="submit" name="addFala">Enviar</button>
            </form>
        </div>
    </div>

</body>
</html>
