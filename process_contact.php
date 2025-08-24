<?php
// Configurações de email para Hostgator
header('Content-Type: text/html; charset=UTF-8');

// Verificar se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Configurações do destinatário - ALTERE AQUI SEU EMAIL
// $to = "contato@emporiopimentaecafes.com.br"; // Altere para seu email
$to = "alexanderba09@gmail.com"; // Altere para seu email
$headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
$headers .= "Reply-To: " . $_POST['email'] . "\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "MIME-Version: 1.0\r\n";

// Validar e sanitizar dados do formulário
$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$phone = isset($_POST['phone']) ? filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING) : '';
$subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
$message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

// Validar campos obrigatórios
if (empty($name) || !$email || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos obrigatórios']);
    exit;
}

// Verificar se o email é válido
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
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

// Montar o assunto do email
$email_subject = "Empório Pimenta e Cafés - " . $subject_text . " - " . $name;

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
                <span class='label'>IP:</span> " . $_SERVER['REMOTE_ADDR'] . "
            </div>
        </div>
        
        <div class='footer'>
            <p>Mensagem enviada através do site do Empório Pimenta e Cafés</p>
        </div>
    </div>
</body>
</html>";

// Tentar enviar o email
$email_sent = mail($to, $email_subject, $email_body, $headers);

if ($email_sent) {
    // Email enviado com sucesso
    $response = [
        'success' => true,
        'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.'
    ];
} else {
    // Erro ao enviar email
    $response = [
        'success' => false,
        'message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.'
    ];
}

// Verificar se a requisição espera JSON (AJAX)
if (isset($_SERVER['HTTP_CONTENT_TYPE']) && strpos($_SERVER['HTTP_CONTENT_TYPE'], 'application/json') !== false) {
    echo json_encode($response);
} else {
    // Redirecionar de volta para o site com mensagem
    $message_param = urlencode($response['message']);
    $status_param = $response['success'] ? 'success' : 'error';
    header("Location: index.html?message={$message_param}&status={$status_param}");
    exit;
}
