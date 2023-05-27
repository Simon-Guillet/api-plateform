<?php

namespace App\Command;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:import-relation-genres-movies',
    description: 'Import relation from genres and movies from TMDB',
)]
class ImportRelationsGenresMoviesCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function getGenres($movie) {
        $API_KEY = '9962de3c66111255c5b403573ceab203';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/movie/'.$movie->getTmdbId().'?api_key='.$API_KEY.'&language=fr-FR');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response, true);
        if (!isset($json['genres'])) {
            return [[], $response];
        }
        $genres = $json['genres'];
        return [$genres, $response];
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $movies = $this->entityManager->getRepository(Movie::class)->findAll();
        foreach ($movies as $movie) {
            [$genres, $response] = $this->getGenres($movie); // [$genres, $response] = [0 => $genres, 1 => $response]

            $output->writeln('movie : '. $movie->getTitle());
            
            foreach ($genres as $genre) {
                $genreEntity = $this->entityManager->getRepository(Genre::class)->findOneBy(['name' => $genre['name']]);
                if (!$genreEntity) {
                    continue;
                }
                $output->writeln('genre : '. $genreEntity->getName());
                $movie->addGenre($genreEntity);
                $this->entityManager->persist($movie);

                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
