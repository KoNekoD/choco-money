<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419182453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delayed_transfer (id VARCHAR(255) NOT NULL, exchange_snapshot_id VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, base_asset VARCHAR(255) NOT NULL, quote_asset VARCHAR(255) NOT NULL, mutual_transfer_transaction_key VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, lead_base_wallet VARCHAR(255) NOT NULL, exchanger_quote_wallet VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CDAEBBF97E754A75 ON delayed_transfer (exchange_snapshot_id)');
        $this->addSql('COMMENT ON COLUMN delayed_transfer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE delayed_transfer_history (id VARCHAR(255) NOT NULL, delayed_transfer_id VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, comment TEXT NOT NULL, from_status VARCHAR(255) NOT NULL, to_status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A337D9F9EA0B743E ON delayed_transfer_history (delayed_transfer_id)');
        $this->addSql('COMMENT ON COLUMN delayed_transfer_history.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE delayed_transfer ADD CONSTRAINT FK_CDAEBBF97E754A75 FOREIGN KEY (exchange_snapshot_id) REFERENCES exchange_snapshot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE delayed_transfer_history ADD CONSTRAINT FK_A337D9F9EA0B743E FOREIGN KEY (delayed_transfer_id) REFERENCES delayed_transfer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE delayed_transfer DROP CONSTRAINT FK_CDAEBBF97E754A75');
        $this->addSql('ALTER TABLE delayed_transfer_history DROP CONSTRAINT FK_A337D9F9EA0B743E');
        $this->addSql('DROP TABLE delayed_transfer');
        $this->addSql('DROP TABLE delayed_transfer_history');
    }
}
