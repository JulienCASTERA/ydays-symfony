<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

    final public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user
            ->setEmail('admin@test.fr')
            ->setPassword($this->encoder->encodePassword($user,'test'))
            ->setIsVerified(true)
        ;
        $manager->persist($user);

        for($u = 1; $u <= 10; $u++)
        {
            $user = (new User())
                ->setEmail(sprintf('user%s@test.com', $u))
                ->setIsVerified(true)
            ;
            $user
                ->setPassword($this->encoder->encodePassword($user, 'test'));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
