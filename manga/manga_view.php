<?php
// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "manga");

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Buscar mensagens para exibição
$result = $conn->query("SELECT falas.id, falas.personagem_id, personagens.nome, personagens.cor, falas.fala 
                        FROM falas 
                        LEFT JOIN personagens ON falas.personagem_id = personagens.id 
                        ORDER BY falas.id ASC");

// Função para destacar o nome do personagem nas falas
function highlightCharacterName($text, $characters) {
    foreach ($characters as $character) {
        // Escapa os nomes para evitar problemas de regex
        $name = preg_quote($character['nome'], '/');
        $color = $character['cor'];

        // Substitui o nome do personagem pela versão destacada
        $text = preg_replace_callback("/\b$name\b/i", function($matches) use ($color) {
            return '<span style="color:' . $color . ';">' . $matches[0] . '</span>';
        }, $text);
    }
    return $text;
}

// Buscar personagens para a busca de nomes
$personagens = $conn->query("SELECT * FROM personagens");
$characters = [];
while ($row = $personagens->fetch_assoc()) {
    $characters[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualização do Manga</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="chat-container">
        <div class="chat-messages">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="message">
                    <?php if ($row['personagem_id'] != 0): // Verifica se não é o narrador ?>
                        <span class="character" style="color: <?= $row['cor'] ?? '#000'; ?>">
                            [<?= htmlspecialchars($row['nome']) ?>]:
                        </span>
                    <?php endif; ?>
                    
                    <span class="text">
                        <?php 
                            // Se for o narrador (personagem_id 0), não exibe o nome ou "[Narrador]:"
                            if ($row['personagem_id'] === 0) {
                                echo htmlspecialchars($row['fala']);
                            } else {
                                // Caso contrário, destaca os nomes dos personagens nas falas
                                echo highlightCharacterName(htmlspecialchars($row['fala']), $characters);
                            }
                        ?>
                    </span>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
