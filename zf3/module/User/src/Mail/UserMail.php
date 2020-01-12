<?php

namespace User\Mail;

use Zend\Mail;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class UserMail
{
    private $mail;
    private $transport;

    public function __construct($transport, $fromEmail, $fromName)
    {
        $this->mail = new Mail\Message();
        $this->mail->setFrom($fromEmail, $fromName);

        $this->transport = $transport;
    }

    /**
     * Cria o corpo do email no formato HTML
     */
    private function setBody($text)
    {
        // Cria o corpo do e-mail
        $html = new MimePart($text);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);

        $this->mail->setBody($body);
    }

    public function sendVerificationMail($user)
    {
        // Cria um código de validação
        $bcrypt = new Bcrypt();
        $code = $bcrypt->create($user->email . $user->password);
        
        // Cria a url de validação a ser enviada no e-mail
        $url = 'http://localhost:8000/verifyEmail/' . $user->email . '/?code=' . $code;

        // Cria o corpo do e-mail
        $this->setBody('
            <p>Você se cadastrou com sucesso. 
            Clique no link abaixo para validar o 
            seu e-mail e ativar sua conta:</p>
            <a href="' . $url . '">Ativar minha conta</a>
        ');

        // Define os dados de destino
        $this->mail->setSubject('PHP Frameworks ZF3 - Verificação de e-mail');
        $this->mail->addTo($user->email, $user->name);

        // Envia o e-mail
        $this->transport->send($this->mail);
    }

    public function sendResetPasswordMail($user, $newPassword)
    {
        // Cria um código de confirmação
        $bcrypt = new Bcrypt();
        $code = $bcrypt->create($user->email . $user->password);
        $code .= $bcrypt->create($newPassword);

        // Cria a url de confirmação a ser enviada no e-mail
        $url = 'http://localhost:8000/confirmResetPassword/' . $user->email . '/?code=' . $code;

        // Cria o corpo do e-mail
        $this->setBody('
            <p>Você solicitou uma alteração de senha. 
            Ignore este e-mail caso não tenha feito esta solicitação, 
            ou clique no link abaixo para confirmar:</p>
            <a href="' . $url . '">Alterar minha senha</a>
        ');

        // Define os dados de destino
        $this->mail->setSubject('PHP Frameworks ZF3 - Redefinição de senha');
        $this->mail->addTo($user->email, $user->name);

        // Envia o e-mail
        $this->transport->send($this->mail);
    }

}