<?php
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Conectar ao banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "1234";
    $dbname = "hospital_agape";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // Verificar se o token é válido
    $stmt = $conn->prepare("SELECT cpf, data_expiracao FROM tokens_recuperacao WHERE token = ? AND data_expiracao > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token válido, permitir que o usuário redefina a senha
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nova_senha = $_POST['nova_senha'] ?? '';

            if (empty($nova_senha)) {
                echo "<script>alert('Por favor, insira uma nova senha.');</script>";
            } else {
                // Atualizar a senha no banco de dados
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $user = $result->fetch_assoc();
                $cpf = $user['cpf'];

                $stmt = $conn->prepare("UPDATE paciente SET senha = ? WHERE cpf = ?");
                $stmt->bind_param("ss", $senha_hash, $cpf);
                $stmt->execute();

                echo "<script>alert('Senha alterada com sucesso.'); window.location.href = 'login.php';</script>";
            }
        }
        ?>

        <!-- Formulário para redefinir a senha -->
        <form method="post">
            <label for="nova_senha">Nova Senha:</label>
            <input type="password" id="nova_senha" name="nova_senha" required>
            <input type="submit" value="Alterar Senha">
        </form>

        <?php
    } else {
        echo "<script>alert('Link inválido ou expirado.');</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Token inválido.');</script>";
}
?>
