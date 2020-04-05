<?php

namespace webfilesframework\core\datastore;

use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;
use webfilesframework\core\datasystem\file\system\MDirectory;

/**
 * Base class for defining datastores to save and load webfiles on a standardized way.<br />
 * More about the definition of a datastore can be found under
 * the following <a href="http://simpleserv.de/webfiles/doc/doku.php?id=definitiondatastore">link</a>.<br />
 * <br />
 * Implements the webfiles standard to be able to edit datastores with help of the webfile editor.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractDatastore extends MWebfile
{

    /**
     * Checks if a connection is possible.
     */
    public abstract function tryConnect();

    /**
     * Determines if the datastore is read-only or not.
     * @return boolean information if datastore is readonly or not.
     */
    public abstract function isReadOnly();

    /**
     * Some datastore cannot be sorted by time due to performance issues.
     * In this time cache can solve the problem. For letting the developer
     * decide if implementing sorting by timestamp this function sets
     * the sorting to true or false.
     *
     * @param $timestamp
     * @return MWebfile
     *
     * @throws MDatastoreException
     */
    public function getNextWebfileForTimestamp($timestamp) {
        throw new MDatastoreException("datastore cannot be sorted by timestamp.");
    }

    /**
     * Returns a webfiles stream with all webfiles from
     * the actual datastore.
     * @return MWebfileStream
     */
    public abstract function getWebfilesAsStream();

    /**
     * Returns all webfiles from the actual datastore.
     * @return array list of webfiles
     */
    public abstract function getWebfilesAsArray();

    /**
     * Returns the latests webfiles. Sorting will
     * happen according to the time information of the webfiles.
     *
     * @param int $count Count of webfiles to be selected.
     * @return array list of webfiles
     */
    // TODO webfilesstream als return nehmen
    public abstract function getLatestWebfiles($count = 5);


    /**
     * Returns a set of webfiles in the actual datastore which matches
     * with the given template.<br />
     * Searching by template is devided in two steps:<br />
     * <ol>
     *    <li>On the first step you define the template you want to search with. Here can help you the method
     *        <b>presetDefaultForTemplate</b> on the class <b>MWebfile</b>.</li>
     *    <li>On the second step you put the template to the datastore to start the search</li>
     * </ol>
     *
     * @param MWebfile $template template to search for
     * @return array list of webfiles
     */
    public abstract function searchByTemplate(MWebfile $template);/** @noinspection PhpUnusedParameterInspection */


	/**
	 *
	 * @param array    $webfiles
	 * @param MWebfile $template
	 *
	 * @return array
	 * @throws \ReflectionException
	 */
    protected function filterWebfilesArrayByTemplate($webfiles, MWebfile $template)
    {

        $filteredWebfiles = array();

        /** @var MWebfile $webfile */
        foreach ($webfiles as $webfile) {
            if ($webfile->matchesTemplate($template)) {
                $filteredWebfiles = $this->addWebfileSafetyToArray($webfile, $filteredWebfiles);
            }
        }

        return $filteredWebfiles;
    }

    /**@noinspection PhpUnusedParameterInspection*/
    /**
     * Stores a single webfile in the datastore.
     *
     * @param MWebfile $webfile
     * @throws MDatastoreException
     */
    public function storeWebfile(MWebfile $webfile)
    {
        if ($this->isReadOnly()) {
            throw new MDatastoreException("cannot modify data on read-only datastore.");
        } else {
            throw new MDatastoreException("not implemented yet.");
        }
    }

    /**
     * Stores all webfiles from a given webfilestream in the actual datastore
     *
     * @param MWebfileStream $webfileStream
     * @throws MDatastoreException
     */
    public function storeWebfilesFromStream(MWebfileStream $webfileStream)
    {

        if ($this->isReadOnly()) {
            throw new MDatastoreException("cannot modify data on read-only datastore.");
        }

        $webfiles = $webfileStream->getWebfiles();
        foreach ($webfiles as $webfile) {
            $this->storeWebfile($webfile);
        }
    }

    /**@noinspection PhpUnusedParameterInspection*/
    /**
     * Deletes a set of webfiles in the actual datastore which can be
     * applied to the given template.
     *
     * @param MWebfile $template
     * @throws MDatastoreException
     */
    public function deleteByTemplate(MWebfile $template)
    {
        if ($this->isReadOnly()) {
            throw new MDatastoreException("cannot modify data on read-only datastore.");
        } else {
            throw new MDatastoreException("not implemented yet.");
        }
    }

	/**
	 * Resolves a datastore which is localized in the folder
	 * <b>"./custom/datastore"</b> according to the given id.<br />
	 * Every file situated in the datastore folder will be converted
	 * to a webfile an the list of webfiles will be used to compare
	 * the id on each datastore in the folder.
	 *
	 * @param string $datastoreId
	 *
	 * @return MWebfile returns the found datastore
	 * @throws MDatastoreException will be thrown if no datastore with*@throws
	 *                             \webfilesframework\MWebfilesFrameworkException the given id is available.
	 * @throws \ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
    public static function resolveCustomDatastoreById($datastoreId)
    {

        $datastoreDirectory = new MDirectory("./custom/datastore/");
        $datastoreHolder = new MDirectoryDatastore($datastoreDirectory);

        $webfiles = $datastoreHolder->getWebfilesAsArray();

        $selectedWebfile = null;

        /**@var MWebfile $webfile **/
        foreach ($webfiles as $webfile) {
            if ($webfile->getId() == $datastoreId) {
                $selectedWebfile = $webfile;
            }
        }

        if ($selectedWebfile == null) {
            throw new MDatastoreException("Cannot find datastore for id '" . $datastoreId . "'");
        }

        return $selectedWebfile;
    }

	/**
	 * @param $webfilesArray
	 *
	 * @return array
	 * @throws \ReflectionException
	 */
    public static function extractDatasetsFromWebfilesArray($webfilesArray)
    {
        $webfilesDatasets = array();

        /** @var MWebfile $webfile */
        foreach ($webfilesArray as $webfile) {
            $dataset = $webfile->getDataset();
            $webfilesDatasets[] = $dataset;
        }
        return $webfilesDatasets;
    }

    protected function addWebfileSafetyToArray(MWebfile $webfile, $objectsArray) {

        $arrayKey = $webfile->getTime();
        $arrayKeyCount = 1;

        // make sure files with the same key (normally timetamp) have an unique array key
        while (isset($objectsArray[$arrayKey])) {
            $arrayKeyCount++;
            $arrayKey = $arrayKey . "," . $arrayKeyCount;
        }
        $objectsArray[$arrayKey] = $webfile;

        return $objectsArray;
    }

}