<?php

namespace AppBundle\Command\ReloadReadStore;

use AppBundle\Domain\Aggregate\User as UserAggregate;
use AppBundle\Domain\Descriptor\UserDescriptor;
use AppBundle\Domain\Message\Event\UserCreated;

class User extends UserAggregate implements UserDescriptor
{
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
     * @param UserCreated $event
     */
    protected function applyUserCreated(UserCreated $event)
    {
        parent::applyUserCreated($event);
        $this->userName = $event->getUserName();
        $this->emailAddress = $event->getEmailAddress();
        $this->fullName = $event->getFullName();
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
