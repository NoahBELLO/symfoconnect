<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260416100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des tables user_follows, post_likes et notification (Jour 2)';
    }

    public function up(Schema $schema): void
    {
        // Table des follows (ManyToMany auto-référencée sur User)
        $this->addSql('CREATE TABLE user_follows (
            follower_id INT NOT NULL,
            following_id INT NOT NULL,
            INDEX IDX_user_follows_follower (follower_id),
            INDEX IDX_user_follows_following (following_id),
            PRIMARY KEY (follower_id, following_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE user_follows
            ADD CONSTRAINT FK_user_follows_follower FOREIGN KEY (follower_id) REFERENCES user (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_user_follows_following FOREIGN KEY (following_id) REFERENCES user (id) ON DELETE CASCADE');

        // Table des likes (ManyToMany entre Post et User)
        $this->addSql('CREATE TABLE post_likes (
            post_id INT NOT NULL,
            user_id INT NOT NULL,
            INDEX IDX_post_likes_post (post_id),
            INDEX IDX_post_likes_user (user_id),
            PRIMARY KEY (post_id, user_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE post_likes
            ADD CONSTRAINT FK_post_likes_post FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_post_likes_user FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');

        // Table des notifications
        $this->addSql('CREATE TABLE notification (
            id INT AUTO_INCREMENT NOT NULL,
            recipient_id INT NOT NULL,
            type VARCHAR(100) NOT NULL,
            content LONGTEXT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_notification_recipient (recipient_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE notification
            ADD CONSTRAINT FK_notification_recipient FOREIGN KEY (recipient_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_follows DROP FOREIGN KEY FK_user_follows_follower');
        $this->addSql('ALTER TABLE user_follows DROP FOREIGN KEY FK_user_follows_following');
        $this->addSql('DROP TABLE user_follows');

        $this->addSql('ALTER TABLE post_likes DROP FOREIGN KEY FK_post_likes_post');
        $this->addSql('ALTER TABLE post_likes DROP FOREIGN KEY FK_post_likes_user');
        $this->addSql('DROP TABLE post_likes');

        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_notification_recipient');
        $this->addSql('DROP TABLE notification');
    }
}
