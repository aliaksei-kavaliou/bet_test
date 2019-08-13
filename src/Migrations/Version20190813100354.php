<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190813100354 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bet_selection CHANGE odds odds NUMERIC(10, 3) NOT NULL');
        $this->addSql('ALTER TABLE balance_transaction CHANGE amount amount NUMERIC(10, 2) NOT NULL, CHANGE amount_before amount_before NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE bet CHANGE stake_amount stake_amount NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE balance_transaction CHANGE amount amount DOUBLE PRECISION NOT NULL, CHANGE amount_before amount_before DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE bet CHANGE stake_amount stake_amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE bet_selection CHANGE odds odds DOUBLE PRECISION NOT NULL');
    }
}
