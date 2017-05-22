<?php
/** @var \Mgi\CoreBundle\Migration\Manager $this */

/** @var Doctrine\ORM\EntityManager $em */
$em = $this->getEntityManager();

$tableName = $this->em->getClassMetadata('MgiCoreBundle:Resource')->getTableName();

$sql = <<<SQL
DROP TABLE IF EXISTS $tableName;

CREATE TABLE $tableName (
  id               INT AUTO_INCREMENT NOT NULL,
  resource_name    VARCHAR(50)        NOT NULL,
  resource_version VARCHAR(10)        NOT NULL,
  UNIQUE INDEX UNIQ_RESOURCE_NAME (resource_name),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
SQL;

$this->em->getConnection()->executeQuery($sql);