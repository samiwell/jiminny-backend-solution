<?php

namespace Solution;

class AudioParser {

    /** @var array silence data in useful format */
    private $audioData = [];

    public function __construct(array $rawSilenceData)
    {
        $this->audioData = $this->parseRawSilenceData($rawSilenceData);
    }

    private function parseRawSilenceData(array $rawSilenceData)
    {
        $audioData = [];

        $audioEvent = $this->getEmptyaudioEvent();
        foreach ($rawSilenceData as $row) {
            $rowData       = explode(':', $row);
            $rowDataLength = count($rowData);

            if ( $rowDataLength === 2 ) {
                $audioEvent->end      = $this->parseDigits($rowData[1]);
                $audioEvent->duration = $audioEvent->end - $audioEvent->start;                

                $audioData[] = $audioEvent;
                $audioEvent = $this->getEmptyaudioEvent();

            } elseif( $rowDataLength === 3 ) {
                $audioEvent->start = $this->parseDigits($rowData[1]);

            } else {
                // TODO Log error
            }
        }

        $audioEvent->end = $audioEvent->start;
        $audioData[] = $audioEvent; 

        return $audioData;
    }

    private function parseDigits(string $string){
        return preg_replace('/[^\d\.]+/', '', $string);
    }

    private function getEmptyaudioEvent()
    {
        return (object) [
            'start'    => 0,
            'end'      => null,
            'duration' => 0,
        ];
    }

    public function getAudioData()
    {
        return $this->audioData;
    }

    /**
     * Get the duration of the channel
     *
     * @return float
     */
    public function getTotalLength()
    {
        $lastRecord = end($this->audioData);
        return $lastRecord->end;
    }

    /**
     * Get the total duration of the speech
     *
     * @return float
     */
    public function getTotalAudioDuration()
    {
        $totalAudioLength = 0;
        foreach ($this->audioData as $record) {
            $totalAudioLength += $record->duration;
        }
        return $totalAudioLength;
    }

    /**
     * Get the audio points
     *
     * @return array $audioPoints
     */
    public function getAudioPoints()
    {
        $audioPoints = [];
        foreach ($this->audioData as $record) {
            $audioPoints[] = [$record->start, $record->end];
        }
        return $audioPoints;
    }

}