<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220717120155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE web_department CHANGE status status ENUM(\'Active\', \'Deleted\')');
        $this->addSql('ALTER TABLE web_notice CHANGE status status ENUM(\'Active\', \'Deleted\'), CHANGE type type ENUM(\'General\', \'Student\', \'Faculty\', \'Tender\')');
        $this->addSql('ALTER TABLE web_user ADD department_id INT DEFAULT NULL, CHANGE status status ENUM(\'Active\', \'Deleted\')');
        $this->addSql('ALTER TABLE web_user ADD CONSTRAINT FK_4991DBBCAE80F5DF FOREIGN KEY (department_id) REFERENCES web_department (id)');
        $this->addSql('CREATE INDEX IDX_4991DBBCAE80F5DF ON web_user (department_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE web_department CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE web_notice CHANGE status status VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE web_user DROP FOREIGN KEY FK_4991DBBCAE80F5DF');
        $this->addSql('DROP INDEX IDX_4991DBBCAE80F5DF ON web_user');
        $this->addSql('ALTER TABLE web_user DROP department_id, CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
