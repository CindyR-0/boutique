<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210812145725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_detail CHANGE commande_id commande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande_detail ADD CONSTRAINT FK_2C52844682EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commande_detail ADD CONSTRAINT FK_2C52844616A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('CREATE INDEX IDX_2C52844682EA2E54 ON commande_detail (commande_id)');
        $this->addSql('CREATE INDEX IDX_2C52844616A2B381 ON commande_detail (book_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_detail DROP FOREIGN KEY FK_2C52844682EA2E54');
        $this->addSql('ALTER TABLE commande_detail DROP FOREIGN KEY FK_2C52844616A2B381');
        $this->addSql('DROP INDEX IDX_2C52844682EA2E54 ON commande_detail');
        $this->addSql('DROP INDEX IDX_2C52844616A2B381 ON commande_detail');
        $this->addSql('ALTER TABLE commande_detail CHANGE commande_id commande_id INT NOT NULL');
    }
}
