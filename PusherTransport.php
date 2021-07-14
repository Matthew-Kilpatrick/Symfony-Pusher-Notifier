<?php

namespace Symfony\Component\Notifier\Bridge\Pusher;


use Symfony\Component\Notifier\Exception\LogicException;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PusherTransport extends AbstractTransport
{

    private $secret;
    private $instanceId;

    public function __construct(string $secret, string $instanceId, HttpClientInterface $httpClient)
    {
        $this->secret = $secret;
        $this->instanceId = $instanceId;
        $this->client = $httpClient;
    }


    public function __toString(): string
    {
        return "pusher://{$this->instanceId}:{$this->secret}@default";
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof ChatMessage && ($message->getOptions() === null || $message->getOptions() instanceof PusherOptions);
    }

    protected function doSend(MessageInterface $message): SentMessage
    {

        if (!$message instanceof ChatMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, ChatMessage::class, $message);
        }

        $options = $message->getOptions()->toArray();

        if (!array_key_exists('users', $options) && !array_key_exists('interests', $options)) {
          throw new LogicException('Recipient users or interests required');
        }

        if (!array_key_exists('web', $options) && !array_key_exists('fcm', $options) && !array_key_exists('apns', $options)) {
            // Assume message subject is to be sent via all channels as no explicit data provided
            $options['web'] = [
                'notification' => [
                  'body' => $message->getSubject()
                ]
            ];
            $options['fcm'] = [
                'notification' => [
                    'body' => $message->getSubject()
                ]
            ];
            $options['apns'] = [
                'alert' => [
                    'body' => $message->getSubject()
                ]
            ];
        }

        $response = $this->client->request(
            'POST', "https://{$this->instanceId}.pushnotifications.pusher.com/publish_api/v1/instances/{$this->instanceId}/publishes/users", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->secret}"
            ],
            'json' => $options
        ]);

        $sentMessage = new SentMessage($message, (string) $this);
        $sentMessage->setMessageId($response->toArray()['publishId']);

        return $sentMessage;
    }
}
