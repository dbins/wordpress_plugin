<?php
/**
 * Email
 * 
 * Classe para envio de emails
 * @author Orlando Xavier (ox@orlandoxavier.com)
 *
 */
class Email
{
    /**
     * 
     * Endereço de origem
     * @var string
     */
    private $from;
    /**
     * 
     * Endereço de destino
     * @var string
     */
    private $to;
    /**
     * 
     * Assunto
     * @var string
     */
    private $subject;
    /**
     * 
     * Corpo do email
     * @var string
     */
    private $message;
    /**
     * 
     * Cabeçalho
     * @var string
     */
    private $header;
    /**
     * 
     * Construtor 
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $message
     */
    public function __construct ($from = '', $to = '', $subject = '', $message = '')
    {
        if (! empty($from) && ! empty($to) && ! empty($subject) &&
         ! empty($message))
        {
            if ($this->emailValidate($from, $to))
            {
                $this->from    = $from;
                $this->to      = $to;
                $this->subject = $subject;
                $this->message = $message;
                
                $this->header  = 'From: ' . $this->from . "\r\n" .
                 'Reply-To: ' . $this->from . "\r\n" . 'X-Mailer: PHP/' .
                 phpversion();
            }
            else
            {
                exit('Digite os endereços de email corretamente.');
            }
        }
    }
    /**
     * 
     * Envia o email para o destinatário: 'to'
     * Caso o email tenha sido enviado, retorna true
     * Caso tenha ocorrido alguma falha durante o envio, retorna false
     */
    public function send ()
    {
        if (mail($this->to, $this->subject, $this->message, $this->header))
        {
            return true;
        } else
        {
            return false;
        }
    }
    /**
     * 
     * Valida os emails passados como parâmetro no construtor
     * 
     * @param string $from
     * @param string $to
     */
    public function emailValidate ($from, $to)
    {
        if (filter_var($from, FILTER_VALIDATE_EMAIL) &&
         filter_var($to, FILTER_VALIDATE_EMAIL))
        {
            return true;
        } else
        {
            return false;
        }
    }
    /**
     * @return the $from
     */
    public function getFrom ()
    {
        return $this->from;
    }
    /**
     * @return the $to
     */
    public function getTo ()
    {
        return $this->to;
    }
    /**
     * @return the $subject
     */
    public function getSubject ()
    {
        return $this->subject;
    }
    /**
     * @return the $message
     */
    public function getMessage ()
    {
        return $this->message;
    }
    /**
     * @param string $from
     */
    public function setFrom ($from)
    {
        $this->from = $from;
    }
    /**
     * @param string $to
     */
    public function setTo ($to)
    {
        $this->to = $to;
    }
    /**
     * @param string $subject
     */
    public function setSubject ($subject)
    {
        $this->subject = $subject;
    }
    /**
     * @param string $message
     */
    public function setMessage ($message)
    {
        $this->message = $message;
    }
}
?>
