<?php

namespace Mukharem\Deploy\Ssh;

use RuntimeException;

final class ConnectFactory
{
    /**
     * @param string $user
     * @param string $pass
     */
    public static function create(string $host, int $port = 22, string $user, string $pass)
    {
        $connection = ssh2_auth_password(self::createConnect($host, $port), $user, $pass);

        if ($connection !== true) {
            throw new RuntimeException(
                "Failed to establish connection with user:$user and pass:$pass"
            );
        }

        return $connection;
    }

    /**
     * @param string $host
     * @param int $port
     * @throws RuntimeException
     * @return resource
     */
    private static function createConnect(string $host, int $port = 22)
    {
        $connection = ssh2_connect($host, $port);

        if ($connection === false) {
            throw new RuntimeException("Failed to establish connection to {$host}:{$port}");
        }

        return $connection;
    }
}
