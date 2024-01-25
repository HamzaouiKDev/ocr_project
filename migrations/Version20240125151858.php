<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240125151858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE result_ocr_sauv (id INT AUTO_INCREMENT NOT NULL, path TEXT DEFAULT NULL, matricule TEXT DEFAULT NULL, annee TEXT DEFAULT NULL, page BIGINT DEFAULT NULL, ligne DOUBLE PRECISION DEFAULT NULL, label TEXT DEFAULT NULL, notes TEXT DEFAULT NULL, value_n TEXT DEFAULT NULL, value_n1 TEXT DEFAULT NULL, code TEXT DEFAULT NULL, type_page TEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE result_ocr_sauv');
    }
}
