<?php
/**
 * Sistema de teste de email usando Gmail SMTP
 * Use este arquivo para testar localmente no XAMPP
 */

// Verificar se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Configurações Gmail SMTP - CONFIGURE AQUI
$smtp_host = "smtp.gmail.com";
$smtp_port = 587;
$smtp_username = "alexanderba09@gmail.com"; // Seu email Gmail
$smtp_password = ""; // Senha de app do Gmail (NÃO a senha normal)
$from_email = "alexanderba09@gmail.com";
$to_email = "alexanderba09@gmail.com";

// IMPORTANTE: Para usar Gmail, você precisa:
// 1. Ativar "Verificação em 2 etapas" na conta Google
// 2. Gerar uma "Senha de app" específica
// 3. Usar essa senha de app aqui, não a senha normal

// Validar dados do formulário
$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$phone = isset($_POST['phone']) ? filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING) : '';
$subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
$message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

if (empty($name) || !$email || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos obrigatórios']);
    exit;
}

if (empty($smtp_password)) {
    echo json_encode(['success' => false, 'message' => '⚠️ Configure a senha de app do Gmail no arquivo test_email_local.php']);
    exit;
}

// Mapear assuntos
$subjects_map = [
    'patrocinio' => 'Patrocínio',
    'expositores' => 'Ser Expositor', 
    'informacoes' => 'Informações Gerais',
    'imprensa' => 'Imprensa',
    'outros' => 'Outros'
];

$subject_text = isset($subjects_map[$subject]) ? $subjects_map[$subject] : 'Contato';
$email_subject = "Empório Pimenta e Cafés - " . $subject_text . " - " . $name;

// Corpo do email
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #8B4513; color: #FFD700; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 20px; }
        .info-row { margin-bottom: 15px; }
        .label { font-weight: bold; color: #8B4513; }
        .footer { background-color: #8B4513; color: white; padding: 10px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🌶️ Empório Pimenta e Cafés ☕</h1>
            <p>Nova mensagem de contato - TESTE LOCAL</p>
        </div>
        
        <div class='content'>
            <div class='info-row'>
                <span class='label'>Nome:</span> " . htmlspecialchars($name) . "
            </div>
            <div class='info-row'>
                <span class='label'>Email:</span> " . htmlspecialchars($email) . "
            </div>
            <div class='info-row'>
                <span class='label'>Telefone:</span> " . htmlspecialchars($phone) . "
            </div>
            <div class='info-row'>
                <span class='label'>Assunto:</span> " . htmlspecialchars($subject_text) . "
            </div>
            <div class='info-row'>
                <span class='label'>Mensagem:</span><br>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
            <div class='info-row'>
                <span class='label'>Data:</span> " . date('d/m/Y H:i:s') . "
            </div>
            <div class='info-row'>
                <span class='label'>Ambiente:</span> XAMPP Local
            </div>
        </div>
        
        <div class='footer'>
            <p>Mensagem enviada através do sistema de teste local</p>
        </div>
    </div>
</body>
</html>";

// Função SMTP simplificada para Gmail
function sendGmailSMTP($host, $port, $username, $password, $from, $to, $subject, $body, $reply_to) {
    $socket = fsockopen($host, $port, $errno, $errstr, 30);
    
    if (!$socket) {
        return "Erro de conexão: $errstr ($errno)";
    }
    
    $responses = [];
    
    // Ler resposta inicial
    $responses[] = fgets($socket, 512);
    if (substr($responses[0], 0, 3) != "220") {
        fclose($socket);
        return "Erro: Servidor não respondeu corretamente";
    }
    
    // EHLO
    fwrite($socket, "EHLO localhost\r\n");
    $responses[] = fgets($socket, 512);
    
    // STARTTLS
    fwrite($socket, "STARTTLS\r\n");
    $responses[] = fgets($socket, 512);
    if (substr($responses[count($responses)-1], 0, 3) != "220") {
        fclose($socket);
        return "Erro: STARTTLS falhou";
    }
    
    // Ativar TLS
    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        fclose($socket);
        return "Erro: Não foi possível ativar TLS";
    }
    
    // EHLO novamente após TLS
    fwrite($socket, "EHLO localhost\r\n");
    $responses[] = fgets($socket, 512);
    
    // AUTH LOGIN
    fwrite($socket, "AUTH LOGIN\r\n");
    $responses[] = fgets($socket, 512);
    
    fwrite($socket, base64_encode($username) . "\r\n");
    $responses[] = fgets($socket, 512);
    
    fwrite($socket, base64_encode($password) . "\r\n");
    $responses[] = fgets($socket, 512);
    if (substr($responses[count($responses)-1], 0, 3) != "235") {
        fclose($socket);
        return "Erro: Autenticação falhou - verifique email e senha de app";
    }
    
    // MAIL FROM
    fwrite($socket, "MAIL FROM: <$from>\r\n");
    $responses[] = fgets($socket, 512);
    
    // RCPT TO
    fwrite($socket, "RCPT TO: <$to>\r\n");
    $responses[] = fgets($socket, 512);
    
    // DATA
    fwrite($socket, "DATA\r\n");
    $responses[] = fgets($socket, 512);
    
    // Cabeçalhos e corpo
    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $reply_to\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "\r\n";
    
    fwrite($socket, $headers . $body . "\r\n.\r\n");
    $responses[] = fgets($socket, 512);
    
    // QUIT
    fwrite($socket, "QUIT\r\n");
    $responses[] = fgets($socket, 512);
    
    fclose($socket);
    
    if (substr($responses[count($responses)-2], 0, 3) == "250") {
        return true;
    } else {
        return "Erro no envio: " . implode(" | ", $responses);
    }
}

// Tentar enviar
$result = sendGmailSMTP($smtp_host, $smtp_port, $smtp_username, $smtp_password, $from_email, $to_email, $email_subject, $email_body, $email);

if ($result === true) {
    $response = [
        'success' => true,
        'message' => '✅ Email enviado com sucesso via Gmail SMTP!'
    ];
} else {
    $response = [
        'success' => false,
        'message' => '❌ Erro: ' . $result
    ];
}

echo json_encode($response);
?>