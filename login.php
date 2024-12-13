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
        // Validação básica do CPF (apenas para evitar entradas inválidas, não substitui validação completa do CPF)
        $cpf = preg_replace('/[^0-9]/', '', $cpf); // Remove caracteres não numéricos
        if (strlen($cpf) !== 11) {
            echo "<script>alert('CPF inválido.');</script>";
            exit();
        }

        // Conexão com banco de dados
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

        // Prepara a consulta
        $stmt = $conn->prepare("SELECT senha FROM paciente WHERE cpf = ?");
        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }

        // Vincula o parâmetro
        $stmt->bind_param("s", $cpf);

        // Executa a consulta
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Verifica a senha
            $user = $result->fetch_assoc();
            if (password_verify($senha, $user['senha'])) {
                // Login bem-sucedido
                header('Location: paciente/index.html');
                exit();
            } else {
                echo "<script>alert('CPF ou senha inválidos.');</script>";
            }
        } else {
            echo "<script>alert('CPF ou senha inválidos.');</script>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>