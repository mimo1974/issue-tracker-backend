<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260723174305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX uniq_auth_identity_provider_provider_user_id ON auth_identity (provider, provider_user_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_auth_identity_provider_email ON auth_identity (provider, email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_auth_identity_provider_provider_user_id');
        $this->addSql('DROP INDEX uniq_auth_identity_provider_email');
    }
}
