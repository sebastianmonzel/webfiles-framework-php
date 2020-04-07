<?php

namespace test\webfilesframework\core\datastore\types;

use PHPUnit\Framework\TestCase;
use webfilesframework\core\datastore\types\database\MSampleWebfile;

abstract class MAbstractDatastoreTest extends TestCase {


	protected function createSampleWebfile() {
		$webfile = new MSampleWebfile();
		$webfile->setId(1);


		return $webfile;
	}

	/**
	 * @return MSampleWebfile
	 * @throws ReflectionException
	 */
	protected function createSampleTemplate() {
		$webfile = new MSampleWebfile();
		$webfile->presetForTemplateSearch();
		$webfile->setId(1);

		return $webfile;
	}


}

