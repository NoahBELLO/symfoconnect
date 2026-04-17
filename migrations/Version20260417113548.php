<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260417113548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, is_read TINYINT NOT NULL, created_at DATETIME NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY `FK_notification_recipient`');
        $this->addSql('ALTER TABLE notification CHANGE is_read is_read TINYINT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification RENAME INDEX idx_notification_recipient TO IDX_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE post CHANGE created_at created_at DATETIME NOT NULL, CHANGE author_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE post_likes RENAME INDEX idx_post_likes_post TO IDX_DED1C2924B89032C');
        $this->addSql('ALTER TABLE post_likes RENAME INDEX idx_post_likes_user TO IDX_DED1C292A76ED395');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_follows DROP FOREIGN KEY `FK_user_follows_follower`');
        $this->addSql('ALTER TABLE user_follows DROP FOREIGN KEY `FK_user_follows_following`');
        $this->addSql('ALTER TABLE user_follows ADD CONSTRAINT FK_136E9479AC24F853 FOREIGN KEY (follower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_follows ADD CONSTRAINT FK_136E94791816E3A3 FOREIGN KEY (following_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_follows RENAME INDEX idx_user_follows_follower TO IDX_136E9479AC24F853');
        $this->addSql('ALTER TABLE user_follows RENAME INDEX idx_user_follows_following TO IDX_136E94791816E3A3');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE notification CHANGE is_read is_read TINYINT DEFAULT 0 NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT `FK_notification_recipient` FOREIGN KEY (recipient_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification RENAME INDEX idx_bf5476cae92f8f78 TO IDX_notification_recipient');
        $this->addSql('ALTER TABLE post CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE author_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post_likes RENAME INDEX idx_ded1c2924b89032c TO IDX_post_likes_post');
        $this->addSql('ALTER TABLE post_likes RENAME INDEX idx_ded1c292a76ed395 TO IDX_post_likes_user');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_follows DROP FOREIGN KEY FK_136E9479AC24F853');
        $this->addSql('ALTER TABLE user_follows DROP FOREIGN KEY FK_136E94791816E3A3');
        $this->addSql('ALTER TABLE user_follows ADD CONSTRAINT `FK_user_follows_follower` FOREIGN KEY (follower_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_follows ADD CONSTRAINT `FK_user_follows_following` FOREIGN KEY (following_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_follows RENAME INDEX idx_136e9479ac24f853 TO IDX_user_follows_follower');
        $this->addSql('ALTER TABLE user_follows RENAME INDEX idx_136e94791816e3a3 TO IDX_user_follows_following');
    }
}
