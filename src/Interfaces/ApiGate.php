<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Interfaces;

interface ApiGate
{
    public function getChannelLastMessage(string $channelName): ?object;

    public function publishChannelMessage(string $channelName, string $message): ?object;

    public function reactToMessage(object $post): ?object;
}