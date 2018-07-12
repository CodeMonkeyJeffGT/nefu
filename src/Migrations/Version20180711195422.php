<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180711195422 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `student` ADD UNIQUE KEY `student_unique_account`(`account`)');
        $this->addSql('ALTER TABLE `student` ADD KEY `student_openid`(`openid`)');
        $this->addSql('ALTER TABLE `student` ADD KEY `student_major_id`(`major_id`)');
        $this->addSql('ALTER TABLE `score_all` ADD KEY `score_all_account`(`account`)');
        $this->addSql('ALTER TABLE `score_item` ADD KEY `score_item_account`(`account`)');
        $this->addSql('ALTER TABLE `permission` ADD KEY `permission_account`(`account`)');
        $this->addSql('ALTER TABLE `lesson` ADD UNIQUE KEY `lesson_code`(`code`)');
        $this->addSql('ALTER TABLE `major` ADD KEY `major_name_college_id`(`name`, `college_id`)');
        $this->addSql('ALTER TABLE `college` ADD UNIQUE KEY `college_name`(`name`)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `student` DROP KEY `student_unique_account`');
        $this->addSql('ALTER TABLE `student` DROP KEY `student_openid`');
        $this->addSql('ALTER TABLE `student` DROP KEY `student_major_id`');
        $this->addSql('ALTER TABLE `student` DROP KEY `score_all_account`');
        $this->addSql('ALTER TABLE `student` DROP KEY `score_item_account`');
        $this->addSql('ALTER TABLE `student` DROP KEY `permission_account`');
        $this->addSql('ALTER TABLE `student` DROP KEY `lesson_code`');
        $this->addSql('ALTER TABLE `student` DROP KEY `major_name_college_id`');
        $this->addSql('ALTER TABLE `student` DROP KEY `college_name`');
    }
}
