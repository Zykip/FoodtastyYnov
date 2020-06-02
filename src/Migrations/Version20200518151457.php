<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200518151457 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customers (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, postal_address VARCHAR(255) NOT NULL, balance NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dishes (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT NOT NULL, media_id INT NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_584DD35DB1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, restaurant_id INT NOT NULL, created_at DATETIME NOT NULL, deliver_at DATETIME NOT NULL, order_number VARCHAR(255) NOT NULL, customer_address VARCHAR(255) DEFAULT NULL, customer_phone VARCHAR(255) NOT NULL, delivery_cost NUMERIC(10, 2) NOT NULL, INDEX IDX_E52FFDEE9395C3F3 (customer_id), INDEX IDX_E52FFDEEB1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders_dishes (id INT AUTO_INCREMENT NOT NULL, dish_id INT NOT NULL, order_id INT NOT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_F9DDEA8C148EB0CB (dish_id), INDEX IDX_F9DDEA8C8D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurants (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, media_id INT NOT NULL, address VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurants_users (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6738E56CB1E7706E (restaurant_id), INDEX IDX_6738E56CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, verification_token LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dishes ADD CONSTRAINT FK_584DD35DB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurants (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurants (id)');
        $this->addSql('ALTER TABLE orders_dishes ADD CONSTRAINT FK_F9DDEA8C148EB0CB FOREIGN KEY (dish_id) REFERENCES dishes (id)');
        $this->addSql('ALTER TABLE orders_dishes ADD CONSTRAINT FK_F9DDEA8C8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE restaurants_users ADD CONSTRAINT FK_6738E56CB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurants (id)');
        $this->addSql('ALTER TABLE restaurants_users ADD CONSTRAINT FK_6738E56CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9395C3F3');
        $this->addSql('ALTER TABLE orders_dishes DROP FOREIGN KEY FK_F9DDEA8C148EB0CB');
        $this->addSql('ALTER TABLE orders_dishes DROP FOREIGN KEY FK_F9DDEA8C8D9F6D38');
        $this->addSql('ALTER TABLE dishes DROP FOREIGN KEY FK_584DD35DB1E7706E');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEB1E7706E');
        $this->addSql('ALTER TABLE restaurants_users DROP FOREIGN KEY FK_6738E56CB1E7706E');
        $this->addSql('ALTER TABLE restaurants_users DROP FOREIGN KEY FK_6738E56CA76ED395');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE dishes');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE orders_dishes');
        $this->addSql('DROP TABLE restaurants');
        $this->addSql('DROP TABLE restaurants_users');
        $this->addSql('DROP TABLE users');
    }
}
