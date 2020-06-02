<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519203227 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admins CHANGE address address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE orders CHANGE customer_address customer_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE restaurants_users DROP INDEX IDX_6738E56CA76ED395, ADD UNIQUE INDEX UNIQ_6738E56CA76ED395 (user_id)');
        $this->addSql('ALTER TABLE restaurants_users DROP INDEX IDX_6738E56CB1E7706E, ADD UNIQUE INDEX UNIQ_6738E56CB1E7706E (restaurant_id)');
        $this->addSql('ALTER TABLE users CHANGE verification_token verification_token LONGTEXT DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admins CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE orders CHANGE customer_address customer_address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE restaurants_users DROP INDEX UNIQ_6738E56CB1E7706E, ADD INDEX IDX_6738E56CB1E7706E (restaurant_id)');
        $this->addSql('ALTER TABLE restaurants_users DROP INDEX UNIQ_6738E56CA76ED395, ADD INDEX IDX_6738E56CA76ED395 (user_id)');
        $this->addSql('ALTER TABLE users CHANGE verification_token verification_token LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
