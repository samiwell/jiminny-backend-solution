<?php

namespace Solution;

class ConversationParser {

    private $audioChannels = [];

    /**
     * Add an audio chanel parsed data
     *
     * @param string $audioChannelId
     * @param AudioParser $audioParser
     * @return void
     */
    public function addAudioChannel(string $audioChannelId, AudioParser $audioParser)
    {
        $this->audioChannels[$audioChannelId] = $audioParser;
    }

    /**
     * Get the duration of the longest channel
     *
     * @return float
     */
    public function getTotalCallDuration()
    {
        $conversationLength = 0;
        foreach ($this->audioChannels as $channel) {
            $channelTotalLength = $channel->getTotalLength();
            if ($channelTotalLength > $conversationLength) {
                $conversationLength = $channelTotalLength;
            }
        }
        return $conversationLength;
    }

    /**
     * Get the percentage of talking time for a given channel
     *
     * @param string $audioChannelId
     * @return float
     */
    public function getChannelTalkPercentage(string $audioChannelId)
    {
        $channel = $this->audioChannels[$audioChannelId];
        $channelTotalAudioDuration = $channel->getTotalAudioDuration();
        $conversationDuration = $this->getTotalCallDuration();
        return $channelTotalAudioDuration * 100 / $conversationDuration;
    }

    /**
     * Get the duration of longest uninterrupted monologue for a given channel
     *
     * @param string $audioChannelId
     * @return int
     */
    public function getChannelLongestUninterruptedMonologue(string $audioChannelId)
    {
        $channel = $this->audioChannels[$audioChannelId];
        $audioData = $channel->getAudioData();

        $longestMonologue = $audioData[0];
        foreach ($audioData as $record) {
            if ($longestMonologue->duration < $record->duration && $this->isUninterrupted($audioChannelId, $record)) {
                $longestMonologue = $record;
            }
        }
        return $longestMonologue->duration;
    }

    /**
     * Check if the monologue was interrupted
     *
     * @param string $audioChannelId
     * @param object $monologue
     * @return boolean
     */
    public function isUninterrupted(string $audioChannelId, object $monologue)
    {
        foreach ($this->audioChannels as $channelId => $channel)
        {
            if($channelId == $audioChannelId) {
                continue;
            }
            foreach ($channel->getAudioData() as $event) {
                if ($event->start > $monologue->start && $event->start < $monologue->end) {
                    return false;
                }
                if ($event->end > $monologue->start && $event->end < $monologue->end) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the audio points for a given channel
     *
     * @param string $audioChannelId
     * @return array
     */
    public function getChannelAudioPoints(string $audioChannelId)
    {
        return $this->audioChannels[$audioChannelId]->getAudioPoints();
    }

}