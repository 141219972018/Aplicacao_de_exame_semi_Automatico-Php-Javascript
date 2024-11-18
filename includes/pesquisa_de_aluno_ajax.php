<?php
require_once('funcoes.php');

if (isset($_POST['search_value'])) {
    $search_value = $_POST['search_value'];
    if ($search_value) {
        $search_value = "%{$search_value}%";
        $stmt = $conn->prepare("SELECT * FROM alunos WHERE nome LIKE ? LIMIT 10");
        if ($stmt) {
            $stmt->bind_param('s', $search_value);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado) {
                while ($row = $resultado->fetch_assoc()) {
                    $nome_aluno = $row['nome'];
                    $id_aluno = $row['id'];
                    echo '<a href="perfil_aluno.php?aluno=' . $id_aluno . '"class="dropdown-item pesquisa-item-resultado">' . htmlspecialchars($nome_aluno, ENT_QUOTES, 'UTF-8') . '</a>';
                }
            }
        }
    }
}
