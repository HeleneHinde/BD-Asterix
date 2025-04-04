<?php

namespace App\Command;

use App\Entity\Album;
use App\Entity\Author;
use App\Entity\Genre;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:import-albums',
    description: 'Add a short description for your command',
)]
class ImportAlbumsCommand extends Command
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected static $defaultName = 'app:import-albums';

    protected function configure()
    {
        $this->setDescription('Importe les albums et les auteurs dans la base de données');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Créer des genres
        $genres = ['BD', 'Album illustré'];
        foreach ($genres as $genreName) {
            $genre = new Genre();
            $genre->setName($genreName);
            $this->entityManager->persist($genre);
        }

        $this->entityManager->flush();

        // Données des albums
        $albums = [
            [
                "titre" => "Astérix le Gaulois",
                "année" => 1961,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/09/album-asterix-le-gaulois.png"
            ],
            [
                "titre" => "La Serpe d'or",
                "année" => 1962,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/album-La-Serpe-dor.png"
            ],
            [
                "titre" => "Astérix et les Goths",
                "année" => 1963,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/album-Asterix-et-les-Goths.png"
            ],
            [
                "titre" => "Astérix gladiateur",
                "année" => 1964,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-Gladiateur.png"
            ],
            [
                "titre" => "Le Tour de Gaule d'Astérix",
                "année" => 1965,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Tour-de-Gaule-dAsterix.png"
            ],
            [
                "titre" => "Astérix et Cléopâtre",
                "année" => 1965,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-et-Cleopatre.png"
            ],
            [
                "titre" => "Le Combat des chefs",
                "année" => 1966,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Combat-des-chefs.png"
            ],
            [
                "titre" => "Astérix chez les Bretons",
                "année" => 1966,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-chez-les-Bretons.png"
            ],
            [
                "titre" => "Astérix et les Normands",
                "année" => 1966,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-et-les-Normands.png"
            ],
            [
                "titre" => "Astérix légionnaire",
                "année" => 1967,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-legionnaire.png"
            ],
            [
                "titre" => "Le Bouclier arverne",
                "année" => 1968,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Bouclier-arverne.png"
            ],
            [
                "titre" => "Astérix aux Jeux olympiques",
                "année" => 1968,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-aux-jeux-Olympiques.png"
            ],
            [
                "titre" => "Astérix et le chaudron",
                "année" => 1969,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-et-le-chaudron.png"
            ],
            [
                "titre" => "Astérix en Hispanie",
                "année" => 1969,
                "genre" => "BD",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-en-Hispanie.png"
            ],
            [
                "titre" => "La Zizanie",
                "année" => 1970,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/La-Zizanie.png"
            ],
            [
                "titre" => "Astérix chez les Helvètes",
                "année" => 1970,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-chez-les-Helvetes.png"
            ],
            [
                "titre" => "Le Domaine des dieux",
                "année" => 1971,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Domaine-des-dieux.png"
            ],
            [
                "titre" => "Les Lauriers de César",
                "année" => 1972,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Les-Lauriers-de-Cesar.png"
            ],
            [
                "titre" => "Le Devin",
                "année" => 1972,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Devin.png"
            ],
            [
                "titre" => "Astérix en Corse",
                "année" => 1973,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-en-Corse.png"
            ],
            [
                "titre" => "Le Cadeau de César",
                "année" => 1974,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Cadeau-de-Cesar.png"
            ],
            [
                "titre" => "La Grande Traversée",
                "année" => 1975,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/La-Grande-Traversee.png"
            ],
            [
                "titre" => "Obélix et Compagnie",
                "année" => 1976,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Obelix-et-Compagnie.png"
            ],
            [
                "titre" => "Astérix chez les Belges",
                "année" => 1979,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-chez-les-Belges.png"
            ],
            [
                "titre" => "Le Grand Fossé",
                "année" => 1980,
                "genre" => "BD",
                "auteurs" => ["Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Grand-Fosse.png"
            ],
            [
                "titre" => "L'Odyssée d'Astérix",
                "année" => 1981,
                "genre" => "BD",
                "auteurs" => ["Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/LOdyssee-dAsterix.png"
            ],
            [
                "titre" => "Le Fils d'Astérix",
                "année" => 1983,
                "genre" => "BD",
                "auteurs" => ["Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Fils-dAsterix.png"
            ],
            [
                "titre" => "Astérix chez Rahàzade",
                "année" => 1987,
                "genre" => "BD",
                "auteurs" => ["Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-chez-Rahazade.png"
            ],
            [
                "titre" => "La Rose et le Glaive",
                "année" => 1991,
                "genre" => "BD",
                "auteurs" => ["Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/La-Rose-et-le-glaive.png"
            ],
            [
                "titre" => "La Galère d'Obélix",
                "année" => 1996,
                "genre" => "BD",
                "auteurs" => ["Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/La-Galere-dObelix.png"
            ],
            [
                "titre" => "Astérix et Latraviata",
                "année" => 2001,
                "genre" => "BD",
                "auteurs" => ["Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-et-Latraviata.png"
            ],
            [
                "titre" => "Astérix et la Rentrée gauloise",
                "année" => 2003,
                "genre" => "BD",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-et-la-rentree-gauloise.png"
            ],
            [
                "titre" => "Le Ciel lui tombe sur la tête",
                "année" => 2005,
                "genre" => "BD",
                "auteurs" => ["Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Ciel-lui-tombe-sur-la-tete.png"
            ],
            [
                "titre" => "L'Anniversaire d'Astérix et Obélix =>Le Livre d'or",
                "année" => 2009,
                "genre" => "BD",
                "auteurs" => ["Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/LAnniversaire-dAsterix-Obelix-%E2%80%93-Le-Livre-dOr.png"
            ],
            [
                "titre" => "Astérix chez les Pictes",
                "année" => 2013,
                "genre" => "BD",
                "auteurs" => ['Jean-Yves Ferri', 'Didier Conrad'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-chez-les-Pictes.png"
            ],
            [
                "titre" => "Le Papyrus de César",
                "année" => 2015,
                "genre" => "BD",
                "auteurs" => ['Jean-Yves Ferri', 'Didier Conrad'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Papyrus-de-Cesar.png"
            ],
            [
                "titre" => "Astérix et la Transitalique",
                "année" => 2017,
                "genre" => "BD",
                "auteurs" => ['Jean-Yves Ferri', 'Didier Conrad'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-et-la-Transitalique.png"
            ],
            [
                "titre" => "La Fille de Vercingétorix",
                "année" => 2019,
                "genre" => "BD",
                "auteurs" => ['Jean-Yves Ferri', 'Didier Conrad'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/la-fille-de-vercingetorix.png"
            ],
            [
                "titre" => "Astérix et le Griffon",
                "année" => 2021,
                "genre" => "BD",
                "auteurs" => ["Jean-Yves Ferri", "Didier Conrad"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Asterix-et-le-Griffon.png"
            ],
            [
                "titre" => "L'Iris Blanc",
                "année" => 2023,
                "genre" => "BD",
                "auteurs" => ['Fabcaro', 'Didier Conrad'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/LIris-blanc.png"
            ],
            [
                "titre" => "Astérix en Lusitanie",
                "année" => 2025,
                "genre" => "BD",
                "auteurs" => ['Fabcaro', 'Didier Conrad'],
                "image" => "https://asterix.com/wp-content/uploads/2025/03/Key_Visual_41.png"
            ],
            [
                "titre" => "Astérix, Le secret de la potion magique",
                "année" => 2018,
                "genre" => "Album illustré",
                "auteurs" => ['René Goscinny', 'Albert Uderzo'],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Secret-de-la-Potion-Magique.png"
            ],
            [
                "titre" => "Comment Obélix est tombé dans la marmite du druide quand il était petit",
                "année" => 1989,
                "genre" => "Album illustré",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/09/Comment-Obelix-est-tombe-dans-la-marmite-du-druide-quand-il-etait-petit.png"
            ],
            [
                "titre" => "Les 12 Travaux d'Astérix",
                "année" => 2016,
                "genre" => "Album illustré",
                "auteurs" => ["René Goscinny", "Albert Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Les-12-travaux-dAsterix.png"
            ],
            [
                "titre" => "L'empire du Milieu",
                "année" => 2023,
                "genre" => "Album illustré",
                "auteurs" => ["Olivier Gay", "Fabrice Tarrin"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/empire-du-milieu.jpg"
            ],
            [
                "titre" => "Le Menhir d'Or",
                "année" => 2020,
                "genre" => "Album illustré",
                "auteurs" => ["Goscinny", "Uderzo"],
                "image" => "https://asterix.com/wp-content/uploads/2024/11/Le-Menhir-dor-1167x1536.jpg"
            ],
        ];


        foreach ($albums as $data) {
            $album = new Album();
            $album->setTitle($data["titre"]);
            $album->setDate(new \DateTime($data['année'] . '-01-01'));  // L'année au format DateTime (1er janvier)
            $album->setImage($data["image"]);
            // Récupérer le genre BD
            $bdGenre = $this->entityManager->getRepository(Genre::class)->findOneBy(['name' => $data["genre"]]);
            $album->setGenre($bdGenre);
            $album->setOwned(false);
            $album->setPurchaseOption(false);


            $this->entityManager->persist($album);
        };

        // Sauvegarder les données dans la base de données
        $this->entityManager->flush();

        $output->writeln('Les albums et auteurs ont été importés avec succès!');

        return Command::SUCCESS;
    }
}
