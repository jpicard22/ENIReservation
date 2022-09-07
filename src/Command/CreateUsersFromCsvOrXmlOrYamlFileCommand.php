<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CreateUsersFromCsvOrXmlOrYamlFileCommand extends Command
{
    private SymfonyStyle $io;
    private EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        UserRepository $userRepository
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->dataDirectory = $dataDirectory;
        $this->userRepository = $userRepository;
    }

    // nom de la commande
    protected static $defaultName = 'app:create-users-from-file';

    protected function configure(): void
    {
        $this->setDescription('Importer des données en provenance d\'un fichier CSV ou XML ou YAML ou JSON');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
       $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createUsers();

        return Command::SUCCESS;
    }

    private function getDataFromFile(): array
    {
        // dataDirectory est initialisé à partir du service yaml pour aller chercher le fichier
        $file = $this->dataDirectory . 'user.yaml';

        // pour importer le fichier suivant n'importe quelle extension
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizers = [new ObjectNormalizer()];

        $encoders = [
            new CsvEncoder(),
            new XmlEncoder(),
            new YamlEncoder(),
            new JsonEncoder()
        ];

        // 2  arguments à passer : tab et encoder
        $serializer = new Serializer($normalizers, $encoders);

        // retourne une string à partir d'un fichier
        /** @var string $fileString  */
        $fileString = file_get_contents($file); // retourne soit string ou false

        $data = $serializer->decode($fileString, $fileExtension);

        // dd($data);
        // si la clef data existe ( results généralement dans le format xml) on la retourne (celle qui nous interesse)
        if(array_key_exists('data', $data)){
            return $data['data'];
        }

        return $data;
    }

    private function createUsers(): void
    {
        // $this->getDataFromFile();

        // affichage dans la console
        $this->io->section('CREATION DES UTILISATEURS A PARTIR DU FICHIER');

        // pour avoir le nombre des users créés
        $usersCreated = 0;

        // on reccupère le tableau data
        foreach($this->getDataFromFile() as $row){
            // vérification s'il y a bien une clé email et si elle n'est pas vide
            if((array_key_exists('email', $row) && !empty($row['email']))){
                $user = $this->userRepository->findOneBy(
                    [
                        'email' => $row['email']
                    ]);

                    // si il n'y a pas de user de retourné, on peut créer un nouvel utilisateur
                    if (!$user){
                        $user = new User();

                        $user
                            ->setEmail($row['email'])
                            ->setPassword($row['password'])
                            ->setPrenom($row['prenom'])
                            ->setPseudo($row['pseudo'])
                            ->setNom($row['nom'])
                            ->setTel($row['tel'])
                            ->setActif($row['actif']);

                        $this->entityManager->persist($user);

                        $usersCreated++;
                    }
            }
        }
        $this->entityManager->flush();  

        if($usersCreated > 1){
            $string = "{$usersCreated} UTILISATEURS CREES EN BASE DE DONNEES.";
        } elseif($usersCreated === 1){
            $string = '1 UTILISATEUR A ETE CREE EN BASE DE DONNEES.';
        }else{
            $string = 'AUCUN UTILISATEUR N\'A ETE CREE EN BASE DE DONNEES.';
        }

        $this->io->success($string);
    }
}
