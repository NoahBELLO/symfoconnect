<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Mise à jour entités User (bio, avatarUrl, createdAt, username unique) et Post (content, createdAt, author)';
    }

    public function up(Schema $schema): void
    {
        // Modifications de la table user
        // On ajoute created_at nullable d'abord pour gérer les lignes existantes
        $this->addSql('ALTER TABLE user ADD bio LONGTEXT DEFAULT NULL, ADD avatar_url VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE username username VARCHAR(180) DEFAULT NULL');
        $this->addSql('UPDATE user SET created_at = NOW() WHERE created_at IS NULL');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');

        // Modifications de la table post
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395 ON post');
        // On ajoute les nouvelles colonnes nullable d'abord pour gérer les lignes existantes
        $this->addSql('ALTER TABLE post ADD content LONGTEXT DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD author_id INT DEFAULT NULL');
        $this->addSql('UPDATE post SET content = COALESCE(text, \'\'), created_at = NOW() WHERE content IS NULL');
        $this->addSql('ALTER TABLE post DROP text, DROP image, DROP location, DROP user_id');
        $this->addSql('ALTER TABLE post CHANGE content content LONGTEXT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
    }

    public function down(Schema $schema): void
    {
        // Annulation des modifications de la table post
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF675F31B ON post');
        $this->addSql('ALTER TABLE post ADD text LONGTEXT DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD location VARCHAR(255) DEFAULT NULL, ADD user_id INT NOT NULL, DROP content, DROP created_at, DROP author_id');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');

        // Annulation des modifications de la table user
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user DROP bio, DROP avatar_url, DROP created_at, CHANGE username username VARCHAR(255) DEFAULT NULL');
    }
}
