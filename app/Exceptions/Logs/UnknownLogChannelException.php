<?php

namespace App\Exceptions\Logs;

use App\Enums\Logs\LogChannel;
use RuntimeException;

final class UnknownLogChannelException extends RuntimeException
{
    /**
     * @param LogChannel $channel
     * @param array<int, string> $configured
     * @return self
     */
    public static function forChannel(LogChannel $channel, array $configured): self
    {
        return self::forName($channel->value, $configured);
    }

    /**
     * @param string $name
     * @param array<int, string> $configured
     * @return self
     */
    public static function forName(string $name, array $configured): self
    {
        return new self(sprintf(
            'Log channel "%s" has no driver configured. Configured channels: %s.',
            $name,
            $configured === [] ? '<none>' : implode(', ', $configured),
        ));
    }
}
