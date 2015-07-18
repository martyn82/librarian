<?php

namespace AppBundle\EventStore;

use AppBundle\Serializing\Serializable;
use JMS\Serializer\Annotation as Serializer;

class Uuid implements Serializable
{
    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $value;

    /**
     * @return Uuid
     */
    public static function createNew()
    {
        return new self(static::generate());
    }

    /**
     * @codeCoverageIgnore
     * @return string
     * @throws UuidException
     */
    private static function generate()
    {
        if (\function_exists('random_bytes')) {
            $bytes = \random_bytes(16);
        } else if (\function_exists('openssl_random_pseudo_bytes')) {
            $bytes = \openssl_random_pseudo_bytes(16);
        } else {
            throw new UuidException("Unable to generate a random byte sequence.");
        }

        return static::guid4($bytes);
    }

    /**
     * @param string $data
     * @return string
     */
    private static function guid4($data)
    {
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * @param string $value
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * @param array $data
     * @return Uuid
     */
    public static function deserialize(array $data)
    {
        assert(array_key_exists('value', $data));
        return new self($data['value']);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'value' => $this->value
        ];
    }
}
