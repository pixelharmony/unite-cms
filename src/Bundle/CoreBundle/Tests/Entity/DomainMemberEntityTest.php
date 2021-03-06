<?php

namespace UniteCMS\CoreBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use UniteCMS\CoreBundle\Entity\Domain;
use UniteCMS\CoreBundle\Entity\DomainMember;

class DomainMemberEntityTest extends TestCase
{
    public function testBasicOperations()
    {
        $domain_member = new DomainMember();
        $domain_member->setId(1);
        $this->assertEquals(1, $domain_member->getId());
    }
}