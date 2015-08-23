<?php

namespace AppBundle\Controller\Resource;

use AppBundle\Domain\Descriptor\UserDescriptor;
use AppBundle\EventSourcing\ReadStore\ReadModel;
use JMS\Serializer\Annotation as Serializer;

class User implements Resource, UserDescriptor
{
    /**
     * @Serializer\SerializedName("_id")
     * @Serializer\Type("string")
     * @var string
     */
    private $id;

    /**
     * @Serializer\SerializedName("user_name")
     * @Serializer\Type("string")
     * @var string
     */
    private $userName;

    /**
     * @Serializer\SerializedName("email_address")
     * @Serializer\Type("string")
     * @var string
     */
    private $emailAddress;

    /**
     * @Serializer\SerializedName("full_name")
     * @Serializer\Type("string")
     * @var string
     */
    private $fullName;

    /**
     * @param ReadModel $user
     */
    public static function createFromReadModel(ReadModel $user)
    {
        return new self($user->getId(), $user->getUserName(), $user->getEmailAddress(), $user->getFullName());
    }

    /**
     * @param string $id
     * @param string $userName
     * @param string $emailAddress
     * @param string $fullName
     */
    private function __construct($id, $userName, $emailAddress, $fullName)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->emailAddress = $emailAddress;
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
}