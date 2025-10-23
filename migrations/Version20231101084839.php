<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231101084839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create OCR reference tables and seed static data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS type_page_ocr');
        $this->addSql('DROP TABLE IF EXISTS result_ocr');
        $this->addSql('DROP TABLE IF EXISTS ef_classification');

        $this->addSql(<<<'SQL'
CREATE TABLE `ef_classification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Code` text COLLATE utf8_unicode_ci,
  `CODE_CLASSE` double DEFAULT NULL,
  `label` text COLLATE utf8_unicode_ci,
  `label_recherche` text COLLATE utf8_unicode_ci,
  `INDIC_TRAITEMENT` bigint(20) DEFAULT NULL,
  `type_EF` text COLLATE utf8_unicode_ci,
  `NIV_TYPE` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL);

        $this->addSql(<<<'SQL'
CREATE TABLE `result_ocr` (
  `id` int NOT NULL AUTO_INCREMENT,
  `path` text COLLATE utf8_unicode_ci,
  `matricule` text COLLATE utf8_unicode_ci,
  `annee` text COLLATE utf8_unicode_ci,
  `page` bigint(20) DEFAULT NULL,
  `ligne` double DEFAULT NULL,
  `label` text COLLATE utf8_unicode_ci,
  `notes` text COLLATE utf8_unicode_ci,
  `value_n` text COLLATE utf8_unicode_ci,
  `value_n1` text COLLATE utf8_unicode_ci,
  `code` text COLLATE utf8_unicode_ci,
  `type_page` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL);

        $this->addSql(<<<'SQL'
CREATE TABLE `type_page_ocr` (
  `id` int NOT NULL AUTO_INCREMENT,
  `path` text COLLATE utf8_unicode_ci,
  `matricule` text COLLATE utf8_unicode_ci,
  `annee` text COLLATE utf8_unicode_ci,
  `page` bigint(20) DEFAULT NULL,
  `TYPE` text COLLATE utf8_unicode_ci,
  `Nbr` bigint(20) DEFAULT NULL,
  `Label_type` text COLLATE utf8_unicode_ci,
  `valide` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL);

        $sqlFile = __DIR__ . '/sql/seed_ocr.sql';
        $sql = file_get_contents($sqlFile);
        if ($sql === false) {
            throw new \RuntimeException(sprintf('Unable to read seed file "%s"', $sqlFile));
        }

        $this->executeSqlStatements($sql);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS type_page_ocr');
        $this->addSql('DROP TABLE IF EXISTS result_ocr');
        $this->addSql('DROP TABLE IF EXISTS ef_classification');
    }

    private function executeSqlStatements(string $sql): void
    {
        $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
        foreach ($statements as $statement) {
            if ($statement !== '') {
                $this->addSql($statement);
            }
        }
    }
}