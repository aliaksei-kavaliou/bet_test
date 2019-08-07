<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190807055338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bet_selection RENAME INDEX idx_837b8d61d871dc26 TO IDX_A762D642D871DC26');
        $this->addSql('ALTER TABLE balance_transaction ADD player_id INT NOT NULL');
        $this->addSql('ALTER TABLE balance_transaction ADD CONSTRAINT FK_A70FE73399E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('CREATE INDEX IDX_A70FE73399E6F5DF ON balance_transaction (player_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE balance_transaction DROP FOREIGN KEY FK_A70FE73399E6F5DF');
        $this->addSql('DROP INDEX IDX_A70FE73399E6F5DF ON balance_transaction');
        $this->addSql('ALTER TABLE balance_transaction DROP player_id');
        $this->addSql('ALTER TABLE bet_selection RENAME INDEX idx_a762d642d871dc26 TO IDX_837B8D61D871DC26');
    }
}
