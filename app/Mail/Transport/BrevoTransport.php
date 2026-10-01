<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

/**
 * Sends mail through Brevo's HTTP API (https://api.brevo.com/v3/smtp/email).
 * Used because Render's free plan blocks SMTP ports; HTTPS is allowed.
 * Set MAIL_MAILER=brevo and BREVO_API_KEY in the environment.
 */
class BrevoTransport extends AbstractTransport
{
    public function __construct(private string $apiKey, private string $endpoint = 'https://api.brevo.com/v3/smtp/email')
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        if ($this->apiKey === '') {
            throw new TransportException('BREVO_API_KEY is not set.');
        }

        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $from = $email->getFrom()[0] ?? null;
        $map = fn (array $list) => array_values(array_map(
            fn (Address $a) => array_filter(['email' => $a->getAddress(), 'name' => $a->getName() ?: null]),
            $list
        ));

        $payload = array_filter([
            'sender' => $from ? array_filter(['email' => $from->getAddress(), 'name' => $from->getName() ?: null]) : null,
            'to' => $map($email->getTo()),
            'cc' => $map($email->getCc()) ?: null,
            'bcc' => $map($email->getBcc()) ?: null,
            'replyTo' => ($r = $email->getReplyTo()[0] ?? null) ? ['email' => $r->getAddress()] : null,
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ]);

        $response = Http::withHeaders(['api-key' => $this->apiKey, 'accept' => 'application/json'])
            ->timeout(15)
            ->post($this->endpoint, $payload);

        if ($response->failed()) {
            throw new TransportException('Brevo rejected the email (HTTP ' . $response->status() . '): ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
