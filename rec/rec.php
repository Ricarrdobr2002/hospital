<?php
// Verifica se o formulário foi enviado para recuperação de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    // Captura o e-mail
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        echo "<script>alert('Por favor, informe seu e-mail.');</script>";
    } else {
        // Conexão com o banco de dados
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
        $stmt = $conn->prepare("SELECT cpf, email FROM paciente WHERE email = ?");
        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }

        // Vincula o parâmetro
        $stmt->bind_param("s", $email);

        // Executa a consulta
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Recupera o CPF e e-mail
            $user = $result->fetch_assoc();
            $cpf = $user['cpf'];
            $email = $user['email'];

            // Gerar um token único
            $token = bin2hex(random_bytes(16));

            // Armazenar o token em um banco de dados (tabela de tokens, por exemplo) ou em sessão
            $stmt = $conn->prepare("INSERT INTO tokens_recuperacao (cpf, token, data_expiracao) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $stmt->bind_param("ss", $cpf, $token);
            $stmt->execute();

            // Enviar o e-mail com o link para recuperação de senha
            $link_recuperacao = "http://seusite.com/recuperacao.php?token=$token"; // ajuste o link conforme necessário
            $assunto = "Recuperação de Senha";
            $mensagem = "Olá,\n\nClique no link abaixo para redefinir sua senha:\n$link_recuperacao\n\nEste link expira em 1 hora.";

            // Função para enviar e-mail
            if (mail($email, $assunto, $mensagem)) {
                echo "<script>alert('Instruções para recuperação de senha foram enviadas para seu e-mail.');</script>";
            } else {
                echo "<script>alert('Erro ao enviar o e-mail.');</script>";
            }
        } else {
            echo "<script>alert('E-mail não encontrado.');</script>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
