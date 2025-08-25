<?php
/**
 * Sistema de envio de email usando SMTP para Hostgator
 * Use este arquivo se a função mail() padrão não funcionar
 */

// Verificar se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// CONFIGURAÇÕES SMTP HOSTGATOR - ALTERE AQUI
$smtp_host = "mail.seudominio.com.br"; // Altere para seu domínio
$smtp_port = 587; // ou 465 para SSL
$smtp_username = "contato@seudominio.com.br"; // Altere para seu email
$smtp_password = "suasenha"; // Altere para sua senha
$from_email = "contato@seudominio.com.br"; // Altere para seu email
$to_email = "contato@seudominio.com.br"; // Email que receberá as mensagens

// Validar e sanitizar dados do formulário
$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$phone = isset($_POST['phone']) ? filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING) : '';
$message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

// Validar campos obrigatórios
if (empty($name) || !$email || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos obrigatórios']);
    exit;
}


// Função para enviar email via SMTP
function sendSMTPEmail($smtp_host, $smtp_port, $smtp_username, $smtp_password, $from_email, $to_email, $subject, $body, $reply_to) {
    // Criar conexão socket
    $socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 30);
    
    if (!$socket) {
        return false;
    }
    
    // Função auxiliar para enviar comando SMTP
    function smtp_command($socket, $command, $expected_code = null) {
        fwrite($socket, $command . "\r\n");
        $response = fgets($socket, 512);
        
        if ($expected_code && substr($response, 0, 3) != $expected_code) {
            return false;
        }
        
        return $response;
    }
    
    // Iniciar comunicação SMTP
    $response = fgets($socket, 512);
    if (substr($response, 0, 3) != "220") {
        fclose($socket);
        return false;
    }
    
    // EHLO
    if (!smtp_command($socket, "EHLO " . $_SERVER['HTTP_HOST'], "250")) {
        fclose($socket);
        return false;
    }
    
    // STARTTLS (se porta 587)
    if ($smtp_port == 587) {
        if (!smtp_command($socket, "STARTTLS", "220")) {
            fclose($socket);
            return false;
        }
        
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return false;
        }
        
        if (!smtp_command($socket, "EHLO " . $_SERVER['HTTP_HOST'], "250")) {
            fclose($socket);
            return false;
        }
    }
    
    // Autenticação
    if (!smtp_command($socket, "AUTH LOGIN", "334")) {
        fclose($socket);
        return false;
    }
    
    if (!smtp_command($socket, base64_encode($smtp_username), "334")) {
        fclose($socket);
        return false;
    }
    
    if (!smtp_command($socket, base64_encode($smtp_password), "235")) {
        fclose($socket);
        return false;
    }
    
    // Enviar email
    if (!smtp_command($socket, "MAIL FROM: <$from_email>", "250")) {
        fclose($socket);
        return false;
    }
    
    if (!smtp_command($socket, "RCPT TO: <$to_email>", "250")) {
        fclose($socket);
        return false;
    }
    
    if (!smtp_command($socket, "DATA", "354")) {
        fclose($socket);
        return false;
    }
    
    // Cabeçalhos
    $headers = "From: $from_email\r\n";
    $headers .= "Reply-To: $reply_to\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "\r\n";
    
    // Enviar cabeçalhos e corpo
    fwrite($socket, $headers . $body . "\r\n.\r\n");
    
    $response = fgets($socket, 512);
    if (substr($response, 0, 3) != "250") {
        fclose($socket);
        return false;
    }
    
    // Encerrar
    smtp_command($socket, "QUIT");
    fclose($socket);
    
    return true;
}

// Montar o assunto do email
$email_subject = "Empório Pimenta e Cafés - Contato - " . $name;

// Montar o corpo do email em HTML
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
            <h1>Empório Pimenta e Cafés</h1>
            <p>Nova mensagem de contato</p>
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
                <span class='label'>Mensagem:</span><br>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
            
            <div class='info-row'>
                <span class='label'>Data:</span> " . date('d/m/Y H:i:s') . "
            </div>
            
            <div class='info-row'>
                <span class='label'>IP:</span> " . $_SERVER['REMOTE_ADDR'] . "
            </div>
        </div>
        
        <div class='footer'>
            <p>Mensagem enviada através do site do Empório Pimenta e Cafés</p>
        </div>
    </div>
</body>
</html>";

// Tentar enviar o email via SMTP
$email_sent = sendSMTPEmail($smtp_host, $smtp_port, $smtp_username, $smtp_password, $from_email, $to_email, $email_subject, $email_body, $email);

if ($email_sent) {
    $response = [
        'success' => true,
        'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.'
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.'
    ];
}

// Sempre retornar JSON para requisições AJAX
header('Content-Type: application/json');
echo json_encode($response);
?>