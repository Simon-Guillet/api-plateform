<?php

namespace App\Command;

use App\Entity\Genre;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:import-genres',
    description: 'Import movie genres from TMDB',
)]
class ImportGenresCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $API_KEY = '9962de3c66111255c5b403573ceab203';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/genre/movie/list?api_key='.$API_KEY.'&language=fr-FR');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $output->writeln($response);

        $json = json_decode($response, true);
        $genres = $json['genres'];

        foreach ($genres as $genre) {
            $output->writeln($genre['name']);

            // If the genre already exists, skip it
            $existingGenre = $this->entityManager->getRepository(Genre::class)->findOneBy(['name' => $genre['name']]);
            if ($existingGenre) {
                continue;
            }
            $genreEntity = new Genre();
            $genreEntity->setName($genre['name']);

            $this->entityManager->persist($genreEntity);

            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
