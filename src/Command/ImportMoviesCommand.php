<?php

namespace App\Command;

use App\Entity\Movie;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:import-movies',
    description: 'Import movies from TMDB',
)]
class ImportMoviesCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption('page', null, InputOption::VALUE_OPTIONAL, 'Page number', 1)
        ;
    }

    protected function getMovies($page) {
        $API_KEY = '9962de3c66111255c5b403573ceab203';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/movie/popular?api_key='.$API_KEY.'&language=fr-FR&page='.$page);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nbPages = $input->getOption('page') ?: 1;

        for ($i = 1; $i <= $nbPages; $i++) {
            $response = $this->getMovies($i);

            $json = json_decode($response, true);
            $movies = $json['results'];

            foreach ($movies as $movie) {
                $output->writeln($movie['title']);

                // If the movie already exists, skip it
                $existingMovie = $this->entityManager->getRepository(Movie::class)->findOneBy(['title' => $movie['title']]);
                if ($existingMovie) {
                    continue;
                }
                $movieEntity = new Movie();
                $movieEntity->setTitle($movie['title']);
                $movieEntity->setOverview($movie['overview']);
                $movieEntity->setReleaseDate($movie['release_date']);
                $movieEntity->setPosterPath($movie['poster_path']);
                $movieEntity->setBackdropPath($movie['backdrop_path']);
                $movieEntity->setVoteAverage($movie['vote_average']);

                $this->entityManager->persist($movieEntity);

                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
