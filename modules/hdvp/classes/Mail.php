<?php

class Mail
{


    protected $config = [];


    protected $connection;
    protected $localhost;
    protected $timeout = 30;
    protected $debug_mode = false;


    protected $host;
    protected $port;
    protected $secure;
    protected $auth;
    protected $user;
    protected $pass;


    protected $to = [];
    protected $cc = [];
    protected $bcc = [];
    protected $from;
    protected $reply;
    protected $body;
    protected $text;
    protected $subject;
    protected $attachments = [];
    protected $text_mode = false;


    protected $charset = 'UTF-8';
    protected $newline = "\r\n";
    protected $encoding = '7bit';
    protected $wordwrap = 70;

    public function __construct($config, $connection = null)
    {
        $this->config = $config;
        $connection = $connection ? $this->config('connections.' . $connection) : $this->config('connections.' . $this->config('default'));


        $this->host = $connection['host'];
        $this->port = $connection['port'];
        $this->secure = $connection['secure'];
        $this->auth = $connection['auth'];
        $this->user = $connection['user'];
        $this->pass = $connection['pass'];

        $this->debug_mode = $this->config('debug_mode');
        $this->localhost = $this->config('default');
    }

    public function from($email, $name = null)
    {
        $this->from = array(
            'email' => $email,
            'name' => $name,
        );
    }

    public function reply($email, $name = null)
    {
        $this->reply = array(
            'email' => $email,
            'name' => $name,
        );
    }

    public function replyto($email, $name = null)
    {
        $this->reply($email, $name);
    }

    public function to($email, $name = null)
    {
        $this->to[] = array(
            'email' => $email,
            'name' => $name,
        );
    }

    public function cc($email, $name = null)
    {
        $this->cc[] = array(
            'email' => $email,
            'name' => $name,
        );
    }

    public function bcc($email, $name = null)
    {
        $this->bcc[] = array(
            'email' => $email,
            'name' => $name,
        );
    }

    public function body($html)
    {
        $this->body = $html;
        #$this->text = $this->normalize($html);
    }

    public function text($text)
    {
        $this->text = $this->normalize(wordwrap(strip_tags($text), $this->wordwrap));
    }

    public function subject($subject)
    {
        $this->subject = $subject;
    }

    public function attach($path)
    {
        $this->attachments[] = $path;
    }

    public function send_text()
    {
        $this->text_mode = true;
        return $this->send();
    }

    public function send()
    {
        if ($this->smtp_connect()) {
            if ($this->smtp_deliver()) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }

        $this->smtp_disconnect();

        return $result;
    }

    protected function smtp_connect()
    {
        if ($this->secure === 'ssl') $this->host = 'ssl://' . $this->host;

        $this->connection = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);

        if ($this->code() !== 220) return false;

        $this->request(($this->auth ? 'EHLO' : 'HELO') . ' ' . $this->localhost . $this->newline);

        $this->response();

        if ($this->secure === 'tls') {
            $this->request('STARTTLS' . $this->newline);

            if ($this->code() !== 220) return false;

            stream_socket_enable_crypto($this->connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            $this->request(($this->auth ? 'EHLO' : 'HELO') . ' ' . $this->localhost . $this->newline);

            if ($this->code() !== 250) return false;
        }


        if ($this->auth) {
            $this->request('AUTH LOGIN' . $this->newline);

            if ($this->code() !== 334) return false;

            $this->request(base64_encode($this->user) . $this->newline);

            if ($this->code() !== 334) return false;

            $this->request(base64_encode($this->pass) . $this->newline);

            if ($this->code() !== 235) return false;
        }

        return true;
    }

    protected function smtp_construct()
    {
        $boundary = md5(uniqid(time()));

        $headers[] = 'From: ' . $this->format($this->from);
        $headers[] = 'Reply-To: ' . $this->format($this->reply ? $this->reply : $this->from);
        $headers[] = 'Subject: ' . $this->subject;
        $headers[] = 'Date: ' . date('r');

        if (!empty($this->to)) {
            $string = '';
            foreach ($this->to as $r) $string .= $this->format($r) . ', ';
            $string = substr($string, 0, -2);
            $headers[] = 'To: ' . $string;
        }

        if (!empty($this->cc)) {
            $string = '';
            foreach ($this->cc as $r) $string .= $this->format($r) . ', ';
            $string = substr($string, 0, -2);
            $headers[] = 'CC: ' . $string;
        }

        if (empty($this->attachments)) {
            if ($this->text_mode) {
                $headers[] = 'Content-Type: text/plain; charset="' . $this->charset . '"';
                $headers[] = 'Content-Transfer-Encoding: ' . $this->encoding;
                $headers[] = '';
                $headers[] = $this->text;
            } else {
                $headers[] = 'MIME-Version: 1.0';
                $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
                $headers[] = '';
                $headers[] = 'This is a multi-part message in MIME format.';
                $headers[] = '--' . $boundary;

                $headers[] = 'Content-Type: text/plain; charset="' . $this->charset . '"';
                $headers[] = 'Content-Transfer-Encoding: ' . $this->encoding;
                $headers[] = '';
                $headers[] = $this->text;
                $headers[] = '--' . $boundary;

                $headers[] = 'Content-Type: text/html; charset="' . $this->charset . '"';
                $headers[] = 'Content-Transfer-Encoding: ' . $this->encoding;
                $headers[] = '';
                $headers[] = $this->body;
                $headers[] = '--' . $boundary . '--';
            }
        } else {
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
            $headers[] = '';
            $headers[] = 'This is a multi-part message in MIME format.';
            $headers[] = '--' . $boundary;

            $headers[] = 'Content-Type: text/plain; charset="' . $this->charset . '"';
            $headers[] = 'Content-Transfer-Encoding: ' . $this->encoding;
            $headers[] = '';
            $headers[] = $this->text;
            $headers[] = '--' . $boundary;

            if (!$this->text_mode) {
                $headers[] = 'Content-Type: text/html; charset="' . $this->charset . '"';
                $headers[] = 'Content-Transfer-Encoding: ' . $this->encoding;
                $headers[] = '';
                $headers[] = $this->body;
                $headers[] = '--' . $boundary;
            }

            foreach ($this->attachments as $path) {
                if (file_exists($path)) {
                    $contents = @file_get_contents($path);

                    if ($contents) {
                        $contents = chunk_split(base64_encode($contents));

                        $headers[] = 'Content-Type: application/octet-stream; name="' . basename($path) . '"';
                        $headers[] = 'Content-Transfer-Encoding: base64';
                        $headers[] = 'Content-Disposition: attachment';
                        $headers[] = '';
                        $headers[] = $contents;
                        $headers[] = '--' . $boundary;
                    }
                }
            }

            $headers[sizeof($headers) - 1] .= '--';
        }

        $headers[] = '.';

        $email = '';
        foreach ($headers as $header) {
            $email .= $header . $this->newline;
        }

        return $email;
    }

    protected function smtp_deliver()
    {
        $this->request('MAIL FROM: <' . $this->from['email'] . '>' . $this->newline);

        $this->response();

        $recipients = array_merge($this->to, $this->cc, $this->bcc);
        foreach ($recipients as $r) {
            $this->request('RCPT TO: <' . $r['email'] . '>' . $this->newline);
            $this->response();
        }

        $this->request('DATA' . $this->newline);
        $this->response();
        $this->request($this->smtp_construct());

        if ($this->code() === 250) {
            return true;
        } else {
            return false;
        }
    }

    protected function smtp_disconnect()
    {
        $this->request('QUIT' . $this->newline);
        $this->response();
        fclose($this->connection);
    }

    protected function code()
    {

        return (int)substr($this->response(), 0, 3);
    }

    protected function request($string)
    {
        if ($this->debug_mode) echo '<code><strong>' . $string . '</strong></code><br/>';
        fputs($this->connection, $string);
    }

    protected function response()
    {
        $response = '';
        while ($str = fgets($this->connection, 4096)) {
            $response .= $str;
            if (substr($str, 3, 1) === ' ') break;
        }
        if ($this->debug_mode) echo '<code>' . $response . '</code><br/>';

        return $response;
    }

    protected function format($recipient)
    {
        if ($recipient['name']) {
            return $recipient['name'] . ' <' . $recipient['email'] . '>';
        } else {
            return '<' . $recipient['email'] . '>';
        }
    }

    private function normalize($lines)
    {
        $lines = str_replace("\r", "\n", $lines);

        $content = '';
        foreach (explode("\n", $lines) as $line) {
            foreach (str_split($line, 998) as $result) {
                $content .= $result . $this->newline;
            }
        }

        return $content;
    }

    private function config($coords, $default = null)
    {
        return $this->ex($this->config, $coords, $default);
    }

    private function ex($a, $path, $default = null)
    {
        $current = $a;
        $p = strtok($path, '.');

        while ($p !== false) {
            if (!isset($current[$p])) {
                return $default;
            }
            $current = $current[$p];
            $p = strtok('.');
        }

        return $current;
    }
}
