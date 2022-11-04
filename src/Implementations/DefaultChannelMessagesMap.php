<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Implementations;

class DefaultChannelMessagesMap implements \MGGFLOW\Telegram\ChannelKeeper\Interfaces\ChannelMessagesMap
{

    public function getMessage(string $channelName): string
    {
        return 'The channel is still alive and we are preparing content for you! 🙏🏽🖤';
    }
}