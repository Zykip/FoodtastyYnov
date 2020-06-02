<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200520174533 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cart_dishes (id INT AUTO_INCREMENT NOT NULL, dish_id INT NOT NULL, restaurant_id INT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, user_agent VARCHAR(255) NOT NULL, ip_address VARCHAR(50) NOT NULL, INDEX IDX_56C9B102148EB0CB (dish_id), INDEX IDX_56C9B102B1E7706E (restaurant_id), INDEX IDX_56C9B102A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_dishes ADD CONSTRAINT FK_56C9B102148EB0CB FOREIGN KEY (dish_id) REFERENCES dishes (id)');
        $this->addSql('ALTER TABLE cart_dishes ADD CONSTRAINT FK_56C9B102B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurants (id)');
        $this->addSql('ALTER TABLE cart_dishes ADD CONSTRAINT FK_56C9B102A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE restaurants CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE admins CHANGE address address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE orders CHANGE customer_address customer_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE cart_dishes');
        $this->addSql('ALTER TABLE admins CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE orders CHANGE customer_address customer_address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE restaurants CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE users CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
    }
}
