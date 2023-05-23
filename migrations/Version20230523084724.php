<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230523084724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notation (id INT AUTO_INCREMENT NOT NULL, spot_id INT NOT NULL, user_id INT DEFAULT NULL, note INT NOT NULL, INDEX IDX_268BC952DF1D37C (spot_id), INDEX IDX_268BC95A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC952DF1D37C FOREIGN KEY (spot_id) REFERENCES spot (id)');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC95A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notation DROP FOREIGN KEY FK_268BC952DF1D37C');
        $this->addSql('ALTER TABLE notation DROP FOREIGN KEY FK_268BC95A76ED395');
        $this->addSql('DROP TABLE notation');
    }
}
