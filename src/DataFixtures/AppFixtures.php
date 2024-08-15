<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Tache;
use App\Entity\Statut;
use \DateTime;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Création des statuts
        $todo = new Statut();
        $todo->setLibelle('To Do');
        $manager->persist($todo);

        $doing = new Statut();
        $doing->setLibelle('Doing');
        $manager->persist($doing);
        
        $done = new Statut();
        $done->setLibelle('Done');
        $manager->persist($done);

        // Création des employés
        $employe1 = new Employe();
        $employe1->setNom('Sheppard')
            ->setPrenom('John')
            ->setEmail('john@me.com')
            ->setStatut('CDI')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($employe1, 'john'))
            ->setDateArrivee(new DateTime('2024-08-15'));
        $manager->persist($employe1);

        $employe2 = new Employe();
        $employe2->setNom('Vakarian')
            ->setPrenom('Garrus')
            ->setEmail('garrus@me.com')
            ->setStatut('CDI')
            ->setRoles([])
            ->setPassword($this->hasher->hashPassword($employe2, 'garrus'))
            ->setDateArrivee(new DateTime('2024-08-15'));
        $manager->persist($employe2);

        $employe3 = new Employe();
        $employe3->setNom('T\'Soni')
            ->setPrenom('Liara')
            ->setEmail('liara@me.com')
            ->setStatut('CDI')
            ->setRoles([])
            ->setPassword($this->hasher->hashPassword($employe3, 'liara'))
            ->setDateArrivee(new DateTime('2024-08-15'));
        $manager->persist($employe3);

        $employe4 = new Employe();
        $employe4->setNom('Alenko')
            ->setPrenom('Kaidan')
            ->setEmail('kaidan@me.com')
            ->setStatut('CDD')
            ->setRoles([])
            ->setPassword($this->hasher->hashPassword($employe4, 'kaidan'))
            ->setDateArrivee(new DateTime('2024-08-15'));
        $manager->persist($employe4);

        $employe5 = new Employe();
        $employe5->setNom('Lawson')
            ->setPrenom('Miranda')
            ->setEmail('miranda@me.com')
            ->setStatut('Freelance')
            ->setRoles([])
            ->setPassword($this->hasher->hashPassword($employe5, 'miranda'))
            ->setDateArrivee(new DateTime('2024-08-15'));
        $manager->persist($employe5);

        // Création des projets
        $projet1 = new Projet();
        $projet1->setNom('TaskLinker')
            ->setArchive(true);
        $manager->persist($projet1);

        $projet2 = new Projet();
        $projet2->setNom('test')
            ->setArchive(true);
        $manager->persist($projet2);

        $projet3 = new Projet();
        $projet3->setNom('Citadelle')
            ->setArchive(false);
        $projet3->addEmploye($employe1)
            ->addEmploye($employe2)
            ->addEmploye($employe3)
            ->addEmploye($employe4);
        $manager->persist($projet3);

        $projet4 = new Projet();
        $projet4->setNom('Cerberus')
            ->setArchive(false);
        $projet4->addEmploye($employe5);
        $manager->persist($projet4);

        // Création des tâches
        $tache1 = new Tache();
        $tache1->setTitre('Test (To Do)')
            ->setDescription('Une tâche à faire')
            ->setStatut($todo)
            ->setEmploye($employe2)
            ->setProjet($projet3)
            ->setDeadline(new DateTime('2024-08-17'));
        $manager->persist($tache1);

        $tache2 = new Tache();
        $tache2->setTitre('Test (Doing)')
            ->setDescription('Une tâche en cours')
            ->setStatut($doing)
            ->setEmploye($employe2)
            ->setProjet($projet3)
            ->setDeadline(new DateTime('2024-08-16'));
        $manager->persist($tache2);

        $tache3 = new Tache();
        $tache3->setTitre('Test (Done)')
            ->setDescription('Une tâche terminée')
            ->setStatut($done)
            ->setEmploye($employe3)
            ->setProjet($projet3)
            ->setDeadline(new DateTime('2024-08-16'));
        $manager->persist($tache3);

        $manager->flush();
    }
}
