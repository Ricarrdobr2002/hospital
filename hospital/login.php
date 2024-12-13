<?php
// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os valores do formulário
    $cpf = $_POST['cpf'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Valida se os campos estão preenchidos
    if (empty($cpf) || empty($senha)) {
        echo "<script>alert('Por favor, preencha todos os campos.');</script>";
    } else {
        // Aqui você pode adicionar a lógica para validar o CPF e a senha no banco de dados

        // Exemplo de conexão com banco de dados MySQL
        $servername = "localhost";
        $username = "root";
        $password = "1234";
        $dbname = "agape";

        // Cria a conexão
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica a conexão
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        // Prepara e executa a consulta
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE cpf = ? AND senha = ?");
        $stmt->bind_param("ss", $cpf, $senha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Login bem-sucedido
            echo "<script>alert('Login realizado com sucesso!');</script>";
            // Redirecionar para a página inicial ou painel do usuário
            // header('Location: dashboard.php');
        } else {
            // Falha no login
            echo "<script>alert('CPF ou senha inválidos.');</script>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>