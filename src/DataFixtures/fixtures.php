<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTime;
use DateInterval;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class fixtures extends Fixture implements UserPasswordHasherInterface
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {

        //Ville
        $ville = new Ville();
        $ville->setNom("Paris");
        $ville->setCodePostal(75000);
        $manager->persist($ville);

        $ville2 = new Ville();
        $ville2->setNom("Bordeaux");
        $ville2->setCodePostal(33300);
        $manager->persist($ville2);

        $ville3 = new Ville();
        $ville3->setNom("La Rochelle");
        $ville3->setCodePostal(17000);
        $manager->persist($ville3);

        $ville4 = new Ville();
        $ville4->setNom("Rennes");
        $ville4->setCodePostal(35000);
        $manager->persist($ville4);

        $ville5 = new Ville();
        $ville5->setNom("Lyon");
        $ville5->setCodePostal(69000);
        $manager->persist($ville5);

        $ville6 = new Ville();
        $ville6->setNom("Quimper");
        $ville6->setCodePostal(29000);
        $manager->persist($ville6);

        $ville7 = new Ville();
        $ville7->setNom("Nantes");
        $ville7->setCodePostal(44200);
        $manager->persist($ville7);

        $ville8 = new Ville();
        $ville8->setNom("Les Epesses");
        $ville8->setCodePostal(85590);
        $manager->persist($ville8);

        //Lieu
        $lieu = new Lieu();
        $lieu->setNom("les machines de l'ile");
        $lieu->setRue("Boulevard Léon Bureau");
        $lieu->setLatitude(47.206158);
        $lieu->setLongitude(-1.564443);
        $lieu->setVille($ville7);
        $manager->persist($lieu);


        $lieu2 = new Lieu();
        $lieu2->setNom("le Puy du Fou");
        $lieu2->setRue("85590 les Epesses");
        $lieu2->setLatitude(46.8920755);
        $lieu2->setLongitude(-0.9490605);
        $lieu2->setVille($ville8);
        $manager->persist($lieu2);

        $lieu3 = new Lieu();
        $lieu3->setNom("DisneyLand Paris");
        $lieu3->setRue("boulevard de Parc 77700 Coupvray");
        $lieu3->setLatitude(48.8673893);
        $lieu3->setLongitude(2.781399);
        $lieu3->setVille($ville);
        $manager->persist($lieu3);

        $lieu4 = new Lieu();
        $lieu4->setNom("Futuroscope");
        $lieu4->setRue("Avenue René Monory 86360 Chasseneuil-du-Poitou");
        $lieu4->setLatitude(46.6666138);
        $lieu4->setLongitude(0.3509703);
        $lieu4->setVille($ville4);
        $manager->persist($lieu4);

        //ETAT
        $etat = new Etat();
        $etat->setLibelle("créée");
        $manager->persist($etat);

        $etat2 = new Etat();
        $etat2->setLibelle("ouverte");
        $manager->persist($etat2);

        $etat3 = new Etat();
        $etat3->setLibelle("cloturée");
        $manager->persist($etat3);

        $etat4 = new Etat();
        $etat4->setLibelle("en cours");
        $manager->persist($etat4);

        $etat5 = new Etat();
        $etat5->setLibelle("passée");
        $manager->persist($etat5);

        $etat6 = new Etat();
        $etat6->setLibelle("annulée");
        $manager->persist($etat6);

        //CAMPUS
        $campus = new Campus();
        $campus->setNom("Nantes");
        $manager->persist($campus);

        $campus2 = new Campus();
        $campus2->setNom("Rennes");
        $manager->persist($campus2);

        $campus3 = new Campus();
        $campus3->setNom("Quimper");
        $manager->persist($campus3);

        $campus4 = new Campus();
        $campus4->setNom("Niort");
        $manager->persist($campus4);



        //User
        $user = new User();
        $user->setPrenom("admin");
        $user->setNom("Admin");
        $user->setEmail("admin@gmail.com");
        $user->setPassword($this->encoder->encodePassword($user,'password'));
        $user->setPseudo("Admin");
        $user->setTel("0102030405");
        $user->setActif(1);
        $user->setRoles(['ROLE_ADMIN','ROLE_ORGANISATEUR']);
        $user->setCampus($campus4);
        $manager->persist($user);

        $user1 = new User();
        $user1->setPrenom("Stephanie");
        $user1->setNom("Pouliquen");
        $user1->setEmail("s@gmail.com");
        $user1->setPassword($this->encoder->encodePassword($user,'password'));
        $user1->setPseudo("steph");
        $user1->setTel("0102030405");
        $user1->setActif(1);
        $user1->setCampus($campus4);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setPrenom("Nathan");
        $user2->setNom("Baudine");
        $user2->setEmail("n@gmail.com");
        $user2->setPassword($this->encoder->encodePassword($user,'password'));
        $user2->setPseudo("Nathan");
        $user2->setTel("0102030405");
        $user2->setActif(1);
        $user->setRoles(['ROLE_ADMIN','ROLE_ORGANISTAEUR']);
        $user->setCampus($campus4);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setPrenom("Martin");
        $user3->setNom("Klauer");
        $user3->setEmail("m@gmail.com");
        $user3->setPassword($this->encoder->encodePassword($user,'password'));
        $user3->setPseudo("Mart");
        $user3->setTel("0102030405");
        $user3->setActif(1);
        $user3->setCampus($campus4);
        $manager->persist($user3);

        $user4 = new User();
        $user4->setPrenom("Julien");
        $user4->setNom("Le Morvan");
        $user4->setEmail("ju@gmail.com");
        $user4->setPassword($this->encoder->encodePassword($user,'password'));
        $user4->setPseudo("Juju");
        $user4->setTel("0102030405");
        $user4->setActif(1);
        $user4->setCampus($campus4);
        $manager->persist($user1);

        $user5 = new User();
        $user5->setPrenom("Jérôme");
        $user5->setNom("PICARD");
        $user5->setEmail("jeje@gmail.com");
        $user5->setPassword($this->encoder->encodePassword($user,'password'));
        $user5->setPseudo("Jeje");
        $user5->setTel("0102030405");
        $user5->setActif(1);
        $user5->setCampus($campus4);
        $manager->persist($user5);


        //Sortie
        $format =  'Y-m-d H:i:s';
        $dateFuturoscope = DateTime::createFromFormat($format, '2022-09-20 10:00:00');
        $dateParcDisney = DateTime::createFromFormat($format, '2022-10-20 10:00:00');
        $datePuyDuFou = DateTime::createFromFormat($format, '2022-08-20 10:00:00');
        $dateLimiteFutur = DateTime::createFromFormat($format, '2022-09-17 00:00:00');
        $dateLimiteDisney = DateTime::createFromFormat($format, '2022-07-18 00:00:00');
        $dateLimitePuyDu = DateTime::createFromFormat($format, '2022-07-18 00:00:00');

        $sortie = new Sortie();
        $sortie->setNom("visite du Futur");
        $sortie->setDateHeureDebut($dateFuturoscope);
        $sortie->setDuree(480);
        $sortie->setDateLimiteInscription($dateLimiteFutur);
        $sortie->setNbInscriptionsMax(10);
        $sortie->setInfosSortie("Le départ est à 7h et le trajet se fera en bus.");
        $sortie->setEtat($etat2);
        $sortie->setSiteOrganisateur($campus);
        $sortie->setOrganisateur($user);
        $sortie->setNbUserCurrent(0);
        $manager->persist($sortie);

        $sortie2 = new Sortie();
        $sortie2->setNom("visite de Disney");
        $sortie2->setDateHeureDebut($dateParcDisney);
        $sortie2->setDuree(480);
        $sortie2->setDateLimiteInscription($dateLimiteDisney);
        $sortie2->setNbInscriptionsMax(15);
        $sortie2->setInfosSortie("Le départ est à 6h et le trajet se fera en voiture.");
        $sortie2->setEtat($etat2);
        $sortie2->setSiteOrganisateur($campus2);
        $sortie2->setOrganisateur($user);
        $sortie2->setNbUserCurrent(0);
        $manager->persist($sortie2);

        $sortie3 = new Sortie();
        $sortie3->setNom("visite du Puy");
        $sortie3->setDateHeureDebut($datePuyDuFou);
        $sortie3->setDuree(480);
        $sortie3->setDateLimiteInscription($dateLimitePuyDu);
        $sortie3->setNbInscriptionsMax(15);
        $sortie3->setInfosSortie("Le départ est à 7h et le trajet se fera en voiture.");
        $sortie3->setEtat($etat4);
        $sortie3->setSiteOrganisateur($campus);
        $sortie3->setNbUserCurrent(0);
        $sortie3->setOrganisateur($user2);
        $manager->persist($sortie3);

        $manager->flush();
    }
}
