# 📧 Sistema de Email - Empório Pimenta e Cafés

## 🚀 Instalação na Hostgator

### 1. Upload dos Arquivos
Faça upload dos seguintes arquivos para sua pasta `public_html` da Hostgator:

```
public_html/
├── index.html
├── process_contact.php          (★ PRINCIPAL)
├── send_email_smtp.php          (★ ALTERNATIVO)
├── src/
│   ├── css/
│   ├── imgs/
│   ├── fonts/
│   └── scrips/
```

### 2. Configurar Email Principal (process_contact.php)

Abra o arquivo `process_contact.php` e altere apenas esta linha:

```php
// ALTERE AQUI SEU EMAIL
$to = "contato@emporiopimentaecafes.com.br"; // ← Coloque seu email aqui
```

**Exemplo:**
```php
$to = "seu-email@seudominio.com.br";
```

### 3. Teste o Sistema

1. Acesse seu site: `https://seudominio.com.br`
2. Role até a seção "CONTATO" 
3. Preencha e envie o formulário
4. Verifique se recebeu o email

---

## 🔧 Se Não Funcionar (Método SMTP)

Caso o método simples não funcione, use o arquivo `send_email_smtp.php`:

### 1. Configure o SMTP
Abra `send_email_smtp.php` e configure:

```php
// CONFIGURAÇÕES SMTP HOSTGATOR
$smtp_host = "mail.seudominio.com.br";     // Seu domínio
$smtp_port = 587;                          // Ou 465 para SSL
$smtp_username = "contato@seudominio.com.br"; // Email que vai enviar
$smtp_password = "suasenha";               // Senha do email
$from_email = "contato@seudominio.com.br"; // Mesmo email de cima
$to_email = "contato@seudominio.com.br";   // Email que vai receber
```

### 2. Altere o Formulário
No arquivo `index.html`, linha 377, altere:

```html
<!-- DE: -->
<form class="contact-form" action="process_contact.php" method="POST" id="contactForm">

<!-- PARA: -->
<form class="contact-form" action="send_email_smtp.php" method="POST" id="contactForm">
```

---

## 📝 Configurações da Hostgator

### Email Corporativo
1. Entre no **cPanel** da Hostgator
2. Vá em **"Contas de Email"**
3. Crie um email: `contato@seudominio.com.br`
4. Anote a senha criada

### Configurações SMTP Hostgator
```
Servidor SMTP: mail.seudominio.com.br
Porta: 587 (STARTTLS) ou 465 (SSL)
Usuário: seu-email@seudominio.com.br
Senha: [senha do email]
```

---

## ✅ Checklist de Instalação

- [ ] Upload de todos os arquivos
- [ ] Configurar email no `process_contact.php`
- [ ] Testar o formulário
- [ ] Se não funcionar, configurar SMTP
- [ ] Criar email corporativo no cPanel
- [ ] Testar novamente

---

## 🎯 Funcionalidades

✅ **Formulário de Contato Completo**
- Nome, email, telefone, assunto, mensagem
- Validação de campos obrigatórios
- Formatação automática de telefone
- Feedback visual para o usuário

✅ **Email HTML Responsivo**
- Design profissional com cores do site
- Todas as informações organizadas
- Data, hora e IP do remetente

✅ **Compatibilidade**
- Funciona na Hostgator
- Responsive design
- Cross-browser compatibility

---

## 🆘 Problemas Comuns

### "Erro ao enviar mensagem"
1. Verifique se o email está correto no PHP
2. Teste com email diferente
3. Use o método SMTP alternativo

### "Método não permitido"
- Certifique-se que está acessando via `https://`
- Não funciona abrindo arquivo local

### Email não chega
1. Verifique pasta de spam
2. Teste com email do Gmail/Outlook
3. Configure SMTP corretamente

---

## 📞 Suporte

Para suporte técnico da Hostgator:
- **Chat:** Disponível 24/7 no painel
- **Telefone:** 0800 host gator
- **Tickets:** Via painel de controle

---

*Sistema criado especificamente para funcionar na Hostgator* 🚀