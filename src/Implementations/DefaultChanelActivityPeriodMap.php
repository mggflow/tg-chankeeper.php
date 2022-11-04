<?php

namespace MGGFLOW\Telegram\ChannelKeeper\Implementations;

use MGGFLOW\Telegram\ChannelKeeper\Interfaces\ChannelActivityPeriodsMap;

class DefaultChanelActivityPeriodMap implements ChannelActivityPeriodsMap
{

    public function getChannelActivityPeriod(string $channelName): int
    {
        return 7 * 24 * 60 * 60;
    }
}