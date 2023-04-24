<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230421212354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create fun_fact table and add fun facts';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fun_fact (id INT AUTO_INCREMENT NOT NULL, content VARCHAR(255) NOT NULL, friend_type VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql("
			INSERT INTO fun_fact (`content`, `friend_type`) VALUES
			('Clam becomes sexually insatiable when ingesting antidepressants.', 'clam'),
			('Sea slugs are colorblind: they have eyes that are primitive and only see the light or dark. Because of this, they navigate by scent using their rhinophores.', 'sea-slug'),
			('Lobsters can swim forward and backward. When they\'re alarmed, they scoot away in reverse by rapidly curling and uncurling their tails.', 'lobster'),
			('Schools of clownfish have a strict hierarchy, with the most aggressive female at the top.', 'clownfish');
		");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE fun_fact');
    }
}
