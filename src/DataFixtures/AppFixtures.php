<?php

namespace App\DataFixtures;

use App\Entity\CaseStudy;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\de_DE\Internet as InternetDE;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    private const CREATE_USERS = 20;
    private const CREATE_CUSTOMER = 50;
    private const CREATE_CASE_STUDIES = 100;

    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $customers = [];

        // user
        for ($i = 0; $i < self::CREATE_USERS; $i++) {
            $user = new User();
            $password = $this->userPasswordHasher->hashPassword($user, $this->faker->password());
            $user
                ->setEmail(
                    sprintf(
                        '%s@%s',
                        $this->faker->unique()->userName,
                        InternetDE::freeEmailDomain(),
                    )
                )
                ->setPassword($password)
                ->setVerified(true);
            $manager->persist($user);
        }

        // customer
        for ($i = 0; $i < self::CREATE_CUSTOMER; $i++) {
            $customer = new Customer();
            $customer
                ->setActive($this->faker->boolean(80))
                ->setName($this->faker->company);
            $customers[] = $customer;
            $manager->persist($customer);
        }

        // case study
        $customerMaxIdx = self::CREATE_CUSTOMER - 1;
        for ($i = 0; $i < self::CREATE_CASE_STUDIES; $i++) {
            $caseStudy = new CaseStudy();
            $caseStudy
                ->setTitle(ucfirst($this->faker->words($this->faker->numberBetween(1, 4), true)))
                ->setDescription($this->faker->sentences($this->faker->numberBetween(4, 12), true))
                ->setCustomer($customers[$this->faker->numberBetween(0, $customerMaxIdx)]);
            $manager->persist($caseStudy);
        }

        $manager->flush();
    }
}
