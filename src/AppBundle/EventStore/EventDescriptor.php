<?php

namespace AppBundle\EventStore;

final class EventDescriptor
{
    /**
     * @var string
     */
    private $identity;

    /**
     * @var string
     */
    private $event;

    /**
     * @var int
     */
    private $playhead;

    /**
     * @var string
     */
    private $payload;

    /**
     * @var string
     */
    private $recorded;

    /**
     * @param string $identity
     * @param string $event
     * @param string $payload
     * @param int $playhead
     * @return EventDescriptor
     */
    public static function record($identity, $event, $payload, $playhead)
    {
        return new self(
            $identity,
            $event,
            $payload,
            date('r'),
            $playhead
        );
    }

    /**
     * @param array $data
     * @return EventDescriptor
     */
    public static function reconstructFromArray(array $data)
    {
        return new self(
            $data['identity'],
            $data['event'],
            $data['payload'],
            $data['recorded'],
            $data['playhead']
        );
    }

    /**
     * @param string $identity
     * @param string $event
     * @param string $payload
     * @param string $recorded
     * @param int $playhead
     */
    private function __construct($identity, $event, $payload, $recorded, $playhead)
    {
        $this->identity = (string) $identity;
        $this->event = (string) $event;
        $this->payload = (string) $payload;
        $this->recorded = (string) $recorded;
        $this->playhead = (int) $playhead;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return int
     */
    public function getPlayhead()
    {
        return $this->playhead;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'identity' => $this->identity,
            'event' => $this->event,
            'playhead' => $this->playhead,
            'payload' => $this->payload,
            'recorded' => $this->recorded
        ];
    }
}
