<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Interfaces;

interface ChannelMessagesMap
{
    public function getMessage(string $channelName): string;
}