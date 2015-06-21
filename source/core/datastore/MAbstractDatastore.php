<?php

namespace simpleserv\webfilesframework\core\datastore;

use simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;

/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 */

/**
 * Base class for defining datastores to save and load webfiles
 * on a standarized way.<br />
 * More about the definition of a datastore can be found under
 * the folling <a href="http://simpleserv.de/webfiles/doc/doku.php?id=definitiondatastore">link</a>.
 *
 * @package    de.simpleserv.core.datastore
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
abstract class MAbstractDatastore extends MWebfile {
	
	/**
	 * checks if a connection is possible
	 */
	public abstract function tryConnect();
	
	/**
	 * determines if the datastore is read-only or not.
	 * @return boolean
	 */
	public abstract function isReadOnly();
	
	/**
	 * Returns the next webfile for the given timestamp
	 * @param $time timestamp (unix-format)
	 */
	public abstract function getNextWebfileForTime($time);
	
	/**
	 * Returns a webfiles stream from the actual datastore.
	 */
	public abstract function getWebfilestream();
	
	/**
	 * 
	 */
	public abstract function getDatasetsFromDatastore();
	
	public abstract function getLatestDatasets($count = 5, $reverse = true);
	
	public abstract function getWebfilesFromDatastore();
	
	public abstract function getLatestWebfiles($count = 5);
	
	/**
	 * Returns a set of webfiles in the actual datastore which matches
	 * with the given template.<br />
	 * Searching by template is devided in two steps:<br />
	 * <ol>
	 * 	<li>On the first step you define the template you want to search with</li>
	 *  <li>On the second step you put the template to the datastore to start the search</li>
	 * </ol>
	 * 
	 * @param MWebfile $template template to search for
	 */
	public abstract function getByTemplate(MWebfile $template);
	
	/**
	 * Stores all webfiles from a given webfilestream in the actual
	 * datastore.
	 * 
	 * @param MWebfileStream $webfileStream
	 * @throws MDatastoreException
	 */
	public function storeWebfilesFromWebfilestream(MWebfileStream $webfileStream) {
		if ( isReadOnly() ) {
			throw new MDatastoreException("cannot modify data on read-only datastore.");
		} else {
			throw new MDatastoreException("not implemented yet.");
		}
	}
	
	/**
	 * Stores a single webfile in the datastore.
	 * @param MWebfile $webfile
	 * @throws MDatastoreException
	 */
	public function storeWebfile(MWebfile $webfile) {
		if ( isReadOnly() ) {
			throw new MDatastoreException("cannot modify data on read-only datastore.");
		} else {
			throw new MDatastoreException("not implemented yet.");
		}
	}
	
	/**
	 * Deletes a set of webfiles in the actual datastore which can be
	 * applied to the given template.
	 * 
	 * @param MWebfile $webfile
	 * @throws MDatastoreException
	 */
	public function deleteByTemplate(MWebfile $tempate) {
		if ( isReadOnly() ) {
			throw new MDatastoreException("cannot modify data on read-only datastore.");
		} else {
			throw new MDatastoreException("not implemented yet.");
		}
	}
	
	/**
	 * Resolves a datastore which is localized in the folder
	 * <b>"./custom/datastore"</b> according to the given id.
	 * 
	 * @param string $datastoreId
	 * @throws MDatastoreException will be thrown if no datastore with
	 * the given id is available.
	 */
	public static function resolveCustomDatastoreById($datastoreId) {
		
		$datastoreDirectory = new MDirectory("./custom/datastore/");
		$datastoreHolder = new MDirectoryDatastore($datastoreDirectory,false);
		
		$webfiles = $datastoreHolder->getWebfilesFromDatastore();
		
		$selectedWebfile = null;
		$webfile = new MWebfile();
		
		
		foreach ($webfiles as $webfile) {
			if ( $webfile->getId() == $datastoreId ) {
				$selectedWebfile = $webfile;
			}
		}
		
		if ( $selectedWebfile == null ) {
			throw new MDatastoreException("Cannot find datastore for id '". $datastoreId . "'");
		}
		
		
		return $selectedWebfile;
		
	}
	
}