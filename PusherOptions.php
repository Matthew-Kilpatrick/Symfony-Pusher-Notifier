<?php


namespace Symfony\Component\Notifier\Bridge\Pusher;


use Symfony\Component\Notifier\Message\MessageOptionsInterface;

class PusherOptions implements MessageOptionsInterface
{

    private $options = [];
    private $recipientId = null;


    /**
     * Set options for Apple Push Notification Service (APNS)
     * @see https://developer.apple.com/library/archive/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/PayloadKeyReference.html#//apple_ref/doc/uid/TP40008194-CH17-SW1
     * @param array $options
     * @return $this
     */
    public function apns(array $options): self
    {
        $this->options['apns'] = $options;
        return $this;
    }

    /**
     * Set options for Firebase Cloud Messaging (FCM)
     * @see https://firebase.google.com/docs/cloud-messaging/http-server-ref#downstream
     * @param array $options
     * @return $this
     */
    public function fcm(array $options): self
    {
        $this->options['fcm'] = $options;
        return $this;
    }


    /**
     * Set options for Pusher Beam web (browser) notifications
     * @see https://pusher.com/docs/beams/reference/publish-payloads#web-format
     * @param array $options
     * @return $this
     */
    public function web(array $options): self
    {
        $this->options['web'] = $options;
        return $this;
    }

    public function userId(string $userId): self
    {
        $this->options['users'] = [$userId];
        return $this;
    }

    public function userIds(array $userIds): self
    {
        $this->options['users'] = $userIds;
        return $this;
    }

    public function interest(string $interest): self
    {
        $this->options['interests'] = [$interest];
        return $this;
    }

    public function interests(array $interests): self
    {
        $this->options['interests'] = $interests;
        return $this;
    }

    public function getRecipientId(): ?string
    {
        return null;
    }

    public function toArray(): array
    {
        return $this->options;
    }
}
