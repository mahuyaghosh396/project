<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220607132304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE library_book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, available_book VARCHAR(255) NOT NULL, edition VARCHAR(255) NOT NULL, publisher VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE web_notice CHANGE status status ENUM(\'Active\', \'Deleted\')');
        $this->addSql('ALTER TABLE web_user CHANGE status status ENUM(\'Active\', \'Deleted\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE library_book');
        $this->addSql('ALTER TABLE web_notice CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE web_user CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
