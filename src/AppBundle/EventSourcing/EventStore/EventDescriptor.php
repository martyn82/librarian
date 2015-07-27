<?php

namespace AppBundle\EventSourcing\EventStore;

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
     * @var integer
     */
    private $playHead;

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
     * @param integer $playHead
     * @return EventDescriptor
     */
    public static function record($identity, $event, $payload, $playHead)
    {
        return new self(
            $identity,
            $event,
            $payload,
            date('r'),
            $playHead
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
     * @param integer $playHead
     */
    private function __construct($identity, $event, $payload, $recorded, $playHead)
    {
        $this->identity = (string) $identity;
        $this->event = (string) $event;
        $this->payload = (string) $payload;
        $this->recorded = (string) $recorded;
        $this->playHead = (int) $playHead;
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
     * @return integer
     */
    public function getPlayhead()
    {
        return $this->playHead;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'identity' => $this->identity,
            'event' => $this->event,
            'playhead' => $this->playHead,
            'payload' => $this->payload,
            'recorded' => $this->recorded
        ];
    }
}
