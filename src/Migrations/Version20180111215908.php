<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Initial migration - auto generated for MySQL and SQLite3
 */
class Version20180111215908 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        if ($this->connection->getDatabasePlatform()->getName() === 'sqlite') {
	        $this->addSql('CREATE TABLE br_banner_group (id VARCHAR(255) NOT NULL, title VARCHAR(256) NOT NULL, active BOOLEAN DEFAULT \'0\' NOT NULL, date_added DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , description CLOB NOT NULL, PRIMARY KEY(id))');
	        $this->addSql('CREATE TABLE br_banner_element (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , banner_group VARCHAR(255) DEFAULT NULL, url VARCHAR(1024) NOT NULL, image_url VARCHAR(1024) NOT NULL, date_added DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , description CLOB NOT NULL, expires_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , active BOOLEAN DEFAULT \'0\' NOT NULL, title VARCHAR(256) NOT NULL, PRIMARY KEY(id))');
	        $this->addSql('CREATE INDEX IDX_2847899CC0AF75D2 ON br_banner_element (banner_group)');
        }

        // @todo: Add MySQL
    }

    public function down(Schema $schema)
    {
        if ($this->connection->getDatabasePlatform()->getName() === 'sqlite') {
            $this->addSql('DROP TABLE br_banner_group');
            $this->addSql('DROP TABLE br_banner_element');
        }

        // @todo: Add MySQL
    }
}
