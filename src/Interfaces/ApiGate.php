<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Interfaces;

interface ApiGate
{
    public function getLastMessage(string $peer): ?object;

    public function sendMessage(string $peer, string $message): ?object;

    public function reactToMessage(string $peer, int $messId, string $emoticon): ?object;
}