<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdminDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $userEmail = $_ENV['ADMIN_USER_EMAIL'];
        $userPass = $_ENV['ADMIN_USER_PASSWORD'];

        $user = new User();
        $user->setEmail($userEmail);
        $user->setRoles(['ROLE_ADMIN']);

        $hashedPassword = password_hash($userPass, PASSWORD_DEFAULT);
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $manager->flush();
    }
}
