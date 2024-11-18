<?php

namespace App\Command;

use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:populate-database',
    description: 'Populate the database with data from an external API.',
)]
class PopulateDatabaseFromApiCommand extends Command
{
    protected static $defaultName = 'app:populate-database';
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Populate the database with data from an external API.')
            ->setHelp('This command fetches data from an API and saves it into the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching data from the API...');

        //Call l'api
        $url = 'https://tyradex.app/api/v1/types';
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'RobotPokemon',
                    'From' => 'adresse@domaine.com',
                    'Content-Type' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                $output->writeln('<error>Failed to fetch data. HTTP status code: ' . $response->getStatusCode() . '</error>');
                return Command::FAILURE;
            }
            //Transforme le JSON en array
            $data = $response->toArray();
            $output->writeln('Data fetched successfully.');

            foreach ($data as $type) {
                $output->writeln('Processing type: ' . $type['name']['fr']);

                // Vérifie si le type existe déjà
                $existingType = $this->entityManager->getRepository(Type::class)->findOneBy(['name' => $type['name']['fr']]);
                if ($existingType) {
                    $output->writeln('Type already exists: ' . $type['name']['fr']);
                    continue;
                }

                // Crée et persiste l'entité
                $typeEntity = new Type();
                $typeEntity->setName($type['name']['fr']);
                $typeEntity->setImage($type['sprites']);

                $this->entityManager->persist($typeEntity);
            }

            // Sauvegarde les changements
            $this->entityManager->flush();
            $output->writeln('<info>Database has been populated successfully!</info>');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        } catch (TransportExceptionInterface $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
