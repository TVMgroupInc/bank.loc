<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200711094213 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE deposit_commission_log (id INT AUTO_INCREMENT NOT NULL, deposit_id INT NOT NULL, date DATETIME NOT NULL, sum DOUBLE PRECISION NOT NULL, percent SMALLINT UNSIGNED NOT NULL, INDEX IDX_4F66EFC59815E4B1 (deposit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deposit_interest_charge_log (id INT AUTO_INCREMENT NOT NULL, deposit_id INT NOT NULL, date DATETIME NOT NULL, sum DOUBLE PRECISION NOT NULL, INDEX IDX_EB987BF59815E4B1 (deposit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_account_log (id INT AUTO_INCREMENT NOT NULL, bank_account_id INT NOT NULL, balance_change DOUBLE PRECISION NOT NULL, date_ops DATETIME NOT NULL, type_ops VARCHAR(25) NOT NULL, INDEX IDX_ED4F87E412CB990C (bank_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE deposit_commission_log ADD CONSTRAINT FK_4F66EFC59815E4B1 FOREIGN KEY (deposit_id) REFERENCES deposit (id)');
        $this->addSql('ALTER TABLE deposit_interest_charge_log ADD CONSTRAINT FK_EB987BF59815E4B1 FOREIGN KEY (deposit_id) REFERENCES deposit (id)');
        $this->addSql('ALTER TABLE bank_account_log ADD CONSTRAINT FK_ED4F87E412CB990C FOREIGN KEY (bank_account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE deposit_history CHANGE deposit_id deposit_id INT NOT NULL');
        $this->addSql('ALTER TABLE deposit_history ADD CONSTRAINT FK_26708A109815E4B1 FOREIGN KEY (deposit_id) REFERENCES deposit (id)');
        $this->addSql('CREATE INDEX IDX_26708A109815E4B1 ON deposit_history (deposit_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE deposit_commission_log');
        $this->addSql('DROP TABLE deposit_interest_charge_log');
        $this->addSql('DROP TABLE bank_account_log');
        $this->addSql('ALTER TABLE deposit_history DROP FOREIGN KEY FK_26708A109815E4B1');
        $this->addSql('DROP INDEX IDX_26708A109815E4B1 ON deposit_history');
        $this->addSql('ALTER TABLE deposit_history CHANGE deposit_id deposit_id INT UNSIGNED NOT NULL');
    }
}
