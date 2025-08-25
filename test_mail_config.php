<?php
/**
 * Teste de configuração de email no XAMPP
 */

echo "<h2>Teste de Configuração de Email</h2>";

// Verificar se a função mail existe
echo "<h3>1. Função mail()</h3>";
if (function_exists('mail')) {
    echo "✅ Função mail() está disponível<br>";
} else {
    echo "❌ Função mail() NÃO está disponível<br>";
}

// Verificar configurações PHP
echo "<h3>2. Configurações PHP.ini</h3>";
echo "SMTP: " . (ini_get('SMTP') ? ini_get('SMTP') : 'NÃO CONFIGURADO') . "<br>";
echo "smtp_port: " . (ini_get('smtp_port') ? ini_get('smtp_port') : 'NÃO CONFIGURADO') . "<br>";
echo "sendmail_from: " . (ini_get('sendmail_from') ? ini_get('sendmail_from') : 'NÃO CONFIGURADO') . "<br>";
echo "sendmail_path: " . (ini_get('sendmail_path') ? ini_get('sendmail_path') : 'NÃO CONFIGURADO') . "<br>";

// Verificar se está rodando no XAMPP
echo "<h3>3. Ambiente</h3>";
echo "Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Sistema: " . php_uname() . "<br>";

// Teste de envio simples
echo "<h3>4. Teste de Envio</h3>";
$to = "alexanderba09@gmail.com";
$subject = "Teste XAMPP - " . date('H:i:s');
$message = "Teste de email enviado do XAMPP às " . date('d/m/Y H:i:s');
$headers = "From: noreply@localhost\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

echo "Tentando enviar email para: $to<br>";
$result = mail($to, $subject, $message, $headers);

if ($result) {
    echo "✅ mail() retornou TRUE (mas pode não ter chegado)<br>";
    echo "<strong>IMPORTANTE:</strong> XAMPP local geralmente não consegue enviar emails reais.<br>";
    echo "Para envio real, use um dos arquivos SMTP (send_email_smtp.php ou test_email_local.php)<br>";
} else {
    echo "❌ mail() retornou FALSE - erro no envio<br>";
}

// Dicas para configuração
echo "<h3>5. Como configurar email no XAMPP</h3>";
echo "<ol>";
echo "<li><strong>Opção 1 (Gmail SMTP):</strong> Use o arquivo test_email_local.php com suas credenciais Gmail</li>";
echo "<li><strong>Opção 2 (Servidor externo):</strong> Use send_email_smtp.php com SMTP do seu provedor</li>";
echo "<li><strong>Opção 3 (Config local):</strong> Configure sendmail no php.ini (complexo)</li>";
echo "</ol>";

echo "<h3>6. Recomendação</h3>";
echo "<p><strong>Para testes locais:</strong> Configure o test_email_local.php com uma conta Gmail</p>";
echo "<p><strong>Para produção:</strong> Use send_email_smtp.php com o SMTP do seu servidor</p>";
?>