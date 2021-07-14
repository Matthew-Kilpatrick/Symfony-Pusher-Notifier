<?php

namespace Symfony\Component\Notifier\Bridge\Pusher;


use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Symfony\Component\Notifier\Exception\IncompleteDsnException;
use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\Dsn;

class PusherTransportFactory extends AbstractTransportFactory
{

    /**
     * @return PusherTransport
     */
    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();

        if (!\in_array($scheme, $this->getSupportedSchemes())) {
            throw new UnsupportedSchemeException($dsn, 'pusher', $this->getSupportedSchemes());
        }

        $instanceId = $dsn->getUser();
        $secret = $dsn->getPassword();

        if ($instanceId === null) {
            throw new IncompleteDsnException('Instance ID (username) is required');
        }

        if ($secret === null) {
            throw new IncompleteDsnException('Secret (password) is required');
        }

        return new PusherTransport($secret, $instanceId, $this->client);
    }

    protected function getSupportedSchemes(): array
    {
        return ['pusher'];
    }

}
