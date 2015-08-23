<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\Domain\Descriptor\UserDescriptor;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\ReadStore\Document;
use AppBundle\EventSourcing\ReadStore\ReadModel;

class User extends Document implements ReadModel, UserDescriptor
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var integer
     */
    private $version;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @param Uuid $id
     * @param string $userName
     * @param string $emailAddress
     * @param string $fullName
     * @param integer $version
     */
    public function __construct(Uuid $id, $userName, $emailAddress, $fullName, $version)
    {
        $this->id = $id;
        $this->userName = (string) $userName;
        $this->emailAddress = (string) $emailAddress;
        $this->fullName = (string) $fullName;
        $this->version = (int) $version;
    }

    /**
     * @return Uuid
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    final public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'id' => $this->getId()->serialize(),
            'version' => $this->getVersion(),
            'userName' => $this->getUserName(),
            'emailAddress' => $this->getEmailAddress(),
            'fullName' => $this->getFullName()
        ];
    }

    /**
     * @param array $data
     * @return User
     */
    public static function deserialize(array $data)
    {
        assert(array_key_exists('id', $data));
        assert(array_key_exists('userName', $data));
        assert(array_key_exists('emailAddress', $data));
        assert(array_key_exists('fullName', $data));
        assert(array_key_exists('version', $data));

        return new self(
            Uuid::deserialize($data['id']),
            $data['userName'],
            $data['emailAddress'],
            $data['fullName'],
            $data['version']
        );
    }
}