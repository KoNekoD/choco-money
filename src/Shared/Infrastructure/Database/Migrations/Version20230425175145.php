<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425175145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delayed_transfer ADD exchanger_base_wallet_address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE delayed_transfer ADD exchanger_base_wallet_balance_amount_before_receive_money DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE delayed_transfer ADD exchanger_base_wallet_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE delayed_transfer ADD lead_quote_wallet_address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE delayed_transfer DROP lead_base_wallet');
        $this->addSql('ALTER TABLE delayed_transfer DROP exchanger_quote_wallet');
        $this->addSql('ALTER TABLE delayed_transfer DROP base_asset_wallet_name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE delayed_transfer ADD lead_base_wallet VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE delayed_transfer ADD exchanger_quote_wallet VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE delayed_transfer ADD base_asset_wallet_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE delayed_transfer DROP exchanger_base_wallet_address');
        $this->addSql('ALTER TABLE delayed_transfer DROP exchanger_base_wallet_balance_amount_before_receive_money');
        $this->addSql('ALTER TABLE delayed_transfer DROP exchanger_base_wallet_name');
        $this->addSql('ALTER TABLE delayed_transfer DROP lead_quote_wallet_address');
    }
}
