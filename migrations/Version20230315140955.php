<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315140955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tv (id INT AUTO_INCREMENT NOT NULL, poster_path VARCHAR(255) DEFAULT NULL, backdrop_path VARCHAR(255) DEFAULT NULL, vote_average DOUBLE PRECISION NOT NULL, overview VARCHAR(255) NOT NULL, first_air_date VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tv_genre (tv_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_7A2ADBD51D245270 (tv_id), INDEX IDX_7A2ADBD54296D31F (genre_id), PRIMARY KEY(tv_id, genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tv_genre ADD CONSTRAINT FK_7A2ADBD51D245270 FOREIGN KEY (tv_id) REFERENCES tv (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tv_genre ADD CONSTRAINT FK_7A2ADBD54296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tv_genre DROP FOREIGN KEY FK_7A2ADBD51D245270');
        $this->addSql('ALTER TABLE tv_genre DROP FOREIGN KEY FK_7A2ADBD54296D31F');
        $this->addSql('DROP TABLE tv');
        $this->addSql('DROP TABLE tv_genre');
    }
}
