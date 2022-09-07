<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220714103924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD picture VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(50) NOT NULL, CHANGE pseudo pseudo VARCHAR(50) NOT NULL, CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE actif actif TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP picture, CHANGE prenom prenom VARCHAR(50) DEFAULT NULL, CHANGE pseudo pseudo VARCHAR(50) DEFAULT NULL, CHANGE nom nom VARCHAR(50) DEFAULT NULL, CHANGE actif actif TINYINT(1) DEFAULT NULL');
    }
}
