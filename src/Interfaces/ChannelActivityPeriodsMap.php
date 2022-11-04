<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Interfaces;

interface ChannelActivityPeriodsMap
{
    public function getChannelActivityPeriod(string $channelName): int;
}