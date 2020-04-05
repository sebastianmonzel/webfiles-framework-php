<?php

namespace webfilesframework\core\datastore;


use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;

/**
 * Combines different datastores in one datastore together.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MCombinedDatastore extends MAbstractDatastore
{

    private $registeredDatastores = array();

    public function tryConnect()
    {
        return true;
    }

    public function isReadOnly()
    {
        return true;
    }

    public function getNextWebfileForTimestamp($timestamp)
    {
        // nothing todo
    }

    public function registerDatastore(MAbstractDatastore $datastore)
    {
        array_push($this->registeredDatastores, $datastore);
    }

    /**
     *
     * @param int|number $days
     * @return array webfiles from the aggregated datastores
     */
    private function aggregateDatastores($days = 500)
    {

        $nextWebfiles = array();
        $aggregatedWebfiles = array();

        // SUBTRACT GIVEN DAYS FROM NOW
        $timeBeforeGivenDays = time() - ($days * 24 * 3600);

        // FILL ARRAY "nextWebfiles" WITH INITIAL VALUES
        foreach ($this->registeredDatastores as $datastore) {

            $nextWebfile = $datastore->getNextWebfileForTimestamp($timeBeforeGivenDays);
            if (isset($nextWebfile)) {

                $nextWebfileTime = $nextWebfile->getTime();

                $nextWebfiles[$nextWebfileTime]['datastore'] = $datastore;
                $nextWebfiles[$nextWebfileTime]['webfile'] = $nextWebfile;
            }
        }

        // SELECT FIRST WEBFILE TO BE ADDED TO THE AGGREGATED VALUES
        $nextWebfile = $this->selectNextWebfile($nextWebfiles);

        // SELECT THE OTHER WEBFILES
        while (isset($nextWebfile)) {

            array_push($aggregatedWebfiles, $nextWebfile);
            $nextWebfile = $this->selectNextWebfile($nextWebfiles);
        }

        return $aggregatedWebfiles;
    }

    /**
     *
     * @param array $nextWebfiles
     * @return MWebfile
     */
    private function selectNextWebfile(&$nextWebfiles)
    {

        $oldestTimestamp = time();
        $nextWebfile = null;
        $datastore = null;

        foreach ($nextWebfiles as $key => $value) {
            if ($key < $oldestTimestamp) {
                $oldestTimestamp = $key;
                $nextWebfile = $nextWebfiles[$key]['webfile'];
                $datastore = $nextWebfiles[$key]['datastore'];
            }
        }

        if (isset($datastore)) {
            $nextWebfileTemp = $datastore->getNextWebfileForTimestamp($oldestTimestamp);
            if (isset($nextWebfileTemp)) {

                $nextWebfileTempTime = $nextWebfileTemp->getTime();

                $nextWebfiles[$nextWebfileTempTime]['webfile'] = $nextWebfileTemp;
                $nextWebfiles[$nextWebfileTempTime]['datastore'] = $datastore;
            }

            unset($nextWebfiles[$oldestTimestamp]);
        }

        return $nextWebfile;
    }

    /**
     * @see MAbstractDatastore:getWebfilestream()
     */
    public function getAllWebfiles()
    {

        $webfiles = $this->getAllWebfiles()->getWebfiles();
        return new MWebfileStream($webfiles);
    }

    public function getLatestWebfiles($count = 5)
    {

    }

    public function searchByTemplate(MWebfile $template)
    {

    }


    public function getDatasetsFromDatastore()
    {

    }

    public function getLatestDatasets($count = 5, $reverse = true)
    {

    }

    private function getWebfilesAsArray()
    {
        if (count($this->registeredDatastores) == 0) {
            //throw new Exception
        }

        $aggregatedWebfiles = $this->aggregateDatastores();
        return $aggregatedWebfiles;
    }

}