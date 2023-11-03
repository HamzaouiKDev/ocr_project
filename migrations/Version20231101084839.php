<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231101084839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE ef_classification');
        $this->addSql('DROP TABLE result_ocr');
        $this->addSql('DROP TABLE type_page_ocr');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ef_classification (Code TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, CODE_CLASSE DOUBLE PRECISION DEFAULT NULL, label TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, label_recherche TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, INDIC_TRAITEMENT BIGINT DEFAULT NULL, type_EF TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, NIV_TYPE BIGINT DEFAULT NULL) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE result_ocr (path TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, matricule TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, annee TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, page BIGINT DEFAULT NULL, ligne DOUBLE PRECISION DEFAULT NULL, label TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, notes TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, value_n TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, value_n1 TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, code TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, type_page TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE type_page_ocr (path TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, matricule TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, annee TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, page BIGINT DEFAULT NULL, TYPE TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, Nbr BIGINT DEFAULT NULL, Label_type TEXT CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
