<?php

namespace App\DataFixtures;


use App\Entity\LovManagement\Gender;
use App\Entity\LovManagement\Civility;
use App\Entity\UserManagement\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Faker\Factory
     */
    private $faker;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Sets the Faker\Factory.
     *
     * @param null
     */
    public function setFaker()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $this->setFaker();
        $this->loadUser($manager); 
        $this->loadGender($manager); 
        $this->loadCivility($manager);   
    }

    public function loadUser(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$firstName, $lastName, $username, $password, $phone, $email, $roles]) {
            $user = new User();
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $password
            ));
            $user->setTel($phone);
            $user->setFax($phone);
            $user->setEmail($email);
            $user->setRoles($roles);
            $user->setIsEnable(true);
            $user->setContractFile(null);
            $manager->persist($user);
            $this->addReference($username, $user);
        }
        $manager->flush();
    }   


    public function loadGender(ObjectManager $manager)
    {
        $sort = 1;
        foreach ($this->getGenderData() as [$title, $sort]) {
            $gender = new Gender();
            $gender->setTitle($title);
            $gender->setSort($sort);
            $manager->persist($gender);
            if ($sort == 1) {
                $this->addReference('gender', $gender);
            }
            $sort++;
        }
        $manager->flush();
    }

    public function loadCivility(ObjectManager $manager)
    {
        $sort = 1;
        foreach ($this->getCivilityData() as [$title, $sort]) {
            $civility = new Civility();
            $civility->setTitle($title);
            $civility->setSort($sort);
            $manager->persist($civility);
            if ($sort == 1) {
                $this->addReference('civility', $civility);
            }
            $sort++;
        }
        $manager->flush();
    }

    

    private function getUserData(): array
    {
        return [
            ['root', 'root', 'root', 'admin_20201!', '0(0)99766899', 'info@relooke.com', ['ROLE_SUPER_ADMIN']], 
            ['pds', 'pds', 'pds', 'azerty', '0(0)99766899', 'pds@relooke.com', ['ROLE_GERANT']]
        ];
    }

    private function getGenderData(): array
    {
        return [
            ['Masculin', '1'],
            ['Féminin', '2']
        ];
    }

    private function getCivilityData(): array
    {
        return [
            ['Mme', '3'],
            ['M.', '4']
        ];
    }
    

}
