<?php
declare(strict_types=1);

namespace App\Service\Tracking\Statsd;

use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection\InetSocket;
use Domnikl\Statsd\Connection\UdpSocket;

/**
 * Class StatsdClientFactory
 */
class StatsdClientFactory
{
    /**
     * @param string $host
     * @param int    $port
     * @param string $namespace
     * @param string $wrapperClass
     * @param string $socketClass
     *
     * @return Client
     * @throws \RuntimeException
     */
    public static function createClient(
        string $host,
        int $port,
        string $namespace = '',
        string $wrapperClass = Client::class,
        string $socketClass = UdpSocket::class
    ) : Client {
        if (!is_subclass_of($socketClass, InetSocket::class)) {
            throw new \RuntimeException(sprintf('$socketClass must be subclass of %s', InetSocket::class));
        }

        if (!is_subclass_of($wrapperClass, Client::class)) {
            throw new \RuntimeException(sprintf('$wrapperClass must be subclass of %s', Client::class));
        }

        $connection = new $socketClass($host, $port);
        $client = new $wrapperClass($connection, $namespace);

        return $client;
    }
}
