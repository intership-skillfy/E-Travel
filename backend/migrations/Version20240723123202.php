<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240723123202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agency CHANGE website website VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE preferences preferences LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE history CHANGE search_history search_history JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE offre CHANGE destination destination VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE price_list CHANGE hotels hotels VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE review CHANGE images images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agency CHANGE website website VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE client CHANGE preferences preferences LONGTEXT DEFAULT \'NULL\' COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE history CHANGE search_history search_history LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE offre CHANGE destination destination VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE price_list CHANGE hotels hotels VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE review CHANGE images images LONGTEXT DEFAULT \'NULL\' COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
