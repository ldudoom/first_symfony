<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{

    protected UserPasswordHasherInterface $_hasher;
    protected User $_user;
    protected array $_roles;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->_user = new User();
        $this->_hasher = $hasher;
        $this->_roles[] = 'ROLE_ADMIN';
    }

    public function load(ObjectManager $manager): void
    {
        $this->_user->setRoles($this->_roles);
        $this->_user->setPassword($this->_hasher->hashPassword($this->_user, '12345678'));
        $this->_user->setEmail('raullduch@gmail.com');
        $manager->persist($this->_user);
        $manager->flush();
    }
}
