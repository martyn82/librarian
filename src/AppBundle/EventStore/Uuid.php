<?php

namespace AppBundle\EventStore;

use JMS\Serializer\Annotation as Serializer;

class Uuid
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
        return new self(self::generate());
    }

    /**
     * @codeCoverageIgnore
     * @return string
     * @throws \RuntimeException
     */
    private static function generate()
    {
        if (\function_exists('random_bytes')) {
            $bytes = \random_bytes(16);
        } else if (\function_exists('openssl_random_pseudo_bytes')) {
            $bytes = \openssl_random_pseudo_bytes(16);
        } else {
            throw new \RuntimeException("Unable to generate a random byte sequence.");
        }

        return self::guid4($bytes);
    }

    /**
     * @param string $data
     * @return string
     */
    private static function guid4($data)
    {
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

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
}
