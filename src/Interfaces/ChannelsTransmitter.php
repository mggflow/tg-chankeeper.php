<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Interfaces;

interface ChannelsTransmitter
{
    public function getFreshNames(int $count): array;
}