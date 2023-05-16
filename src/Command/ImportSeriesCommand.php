<?php

namespace App\Command;

use App\Entity\Tv;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:import-series',
    description: 'Import series from TMDB',
)]
class ImportSeriesCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function getSeries($page) {
        $API_KEY = '9962de3c66111255c5b403573ceab203';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/tv/popular?api_key='.$API_KEY.'&language=fr-FR&page='.$page);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    protected function getNbPages() {
        $API_KEY = '9962de3c66111255c5b403573ceab203';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.themoviedb.org/3/tv/popular?api_key='.$API_KEY.'&language=fr-FR&page=1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response, true);
        return $json['total_pages'];
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nbPages = $this->getNbPages();

        for ($i = 1; $i <= $nbPages; $i++) {
            $response = $this->getSeries($i);

            $json = json_decode($response, true);
            $series = $json['results'];

            foreach ($series as $serie) {
                $output->writeln($serie['name']);

                // If the serie already exists, skip it
                $existingSerie = $this->entityManager->getRepository(Tv::class)->findOneBy(['name' => $serie['name']]);
                if ($existingSerie) {
                    continue;
                }
                $tvEntity = new Tv();
                $tvEntity->setPosterPath($serie['poster_path']);
                $tvEntity->setBackdropPath($serie['backdrop_path']);
                $tvEntity->setVoteAverage($serie['vote_average']);
                $tvEntity->setOverview($serie['overview']);
                if (isset($serie['first_air_date'])) {
                    $tvEntity->setFirstAirDate($serie['first_air_date']);
                } else {
                    $tvEntity->setFirstAirDate("");
                }
                $tvEntity->setName($serie['name']);
                $tvEntity->setOriginalName($serie['original_name']);

                $this->entityManager->persist($tvEntity);

                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
