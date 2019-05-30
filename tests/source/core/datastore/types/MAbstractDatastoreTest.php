<?php

use webfilesframework\core\datastore\types\database\MSampleWebfile;

abstract class MAbstractDatastoreTest extends PHPUnit_Framework_TestCase {


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

