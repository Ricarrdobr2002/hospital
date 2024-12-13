<?php
// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os valores do formulário
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $csenha = $_POST['csenha'] ?? '';

    // Valida se os campos estão preenchidos
    if (empty($nome) || empty($email) || empty($cpf) || empty($senha) || empty($csenha)) {
        echo "<script>alert('Por favor, preencha todos os campos.');</script>";
    } elseif ($senha !== $csenha) {
        echo "<script>alert('As senhas não coincidem.');</script>";
    } else {
        // Exemplo de conexão com banco de dados MySQL
        $servername = "localhost";
        $username = "root";
        $password = "1234";
        $dbname = "hospital_agape";

        // Cria a conexão
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica a conexão
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        // Criptografa a senha antes de salvar no banco de dados
        $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

        // Prepara e executa a inserção
        $stmt = $conn->prepare("INSERT INTO paciente (nome, email, cpf, senha) VALUES (?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ssss", $nome, $email, $cpf, $senha_criptografada);

            if ($stmt->execute()) {
                header('Location: ../index.html');
                exit();
            } else {
                echo "<script>alert('Erro ao realizar cadastro. Tente novamente.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Erro ao preparar consulta: " . $conn->error . "');</script>";
        }

        $conn->close();
    }
}
?>
