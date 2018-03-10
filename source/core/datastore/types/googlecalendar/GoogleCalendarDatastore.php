<?php

namespace simpleserv\webfilesframework\core\datastore\types\googlecalendar;

use Google_Client;

use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use simpleserv\webfilesframework\core\datastore\MAbstractCachableDatastore;
use simpleserv\webfilesframework\core\datastore\MDatastoreException;
use simpleserv\webfilesframework\core\datastore\MISingleDatasourceDatastore;
use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;


class GoogleCalendarDatastore extends MAbstractCachableDatastore
    implements MISingleDatasourceDatastore
{
    // https://developers.google.com/google-apps/calendar/quickstart/php#step_2_install_the_google_client_library
    /** @var string */
    private $m_sCalendarId;
    private $authConfigAsJsonString;

    public function __construct($calendarId, $authConfigAsJsonString)
    {
        $this->m_sCalendarId = $calendarId;
        $this->authConfigAsJsonString = $authConfigAsJsonString;
    }

    /**
     * Checks if a connection is possible.
     */
    public function tryConnect()
    {
        // TODO: Implement tryConnect() method.
    }

    /**
     * Determines if the datastore is read-only or not.
     * @return boolean information if datastore is readonly or not.
     */
    public function isReadOnly()
    {
        return false;
    }

    /**
     * Returns a webfiles stream with all webfiles from
     * the actual datastore.
     * @return MWebfileStream
     */
    public function getWebfilesAsStream()
    {
        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);

        // Print the next 10 events on the user's calendar.
        $optParams = array(
            'maxResults' => 5,
            'orderBy' => 'startTime',
            'singleEvents' => TRUE,
            'timeMin' => date('c'),
        );
        $results = $service->events->listEvents($this->m_sCalendarId, $optParams);


        $webfiles = array();

        foreach ($results->getItems() as $event) {

            $webfile = $this->toWebfileEvent($event);
            array_push($webfiles, $webfile);
        }

        return new MWebfileStream($webfiles);
    }


    public function getClient()
    {

        $client = new Google_Client();
        $this->initClient($client);

        return $client;
    }

    public function getClientWithToken() {

    	$client = $this->getClient();

	    $credentialsPath = "credentials.json";

	    if (file_exists($credentialsPath)) {
		    $accessToken = json_decode(file_get_contents($credentialsPath), true);
	    } else {
		    $accessToken = $this->requestAccessToken($client, $credentialsPath);
	    }
	    $client->setAccessToken($accessToken);

	    $this->refreshAccessTokenIfExpired($client, $credentialsPath);

    }

    /**
     * @param $client
     * @param $credentialsPath
     */
    public function refreshAccessTokenIfExpired($client, $credentialsPath)
    {
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
    }


    /**
     * @param $client
     * @param $credentialsPath
     * @return mixed
     */
    private function requestAccessToken($client, $credentialsPath)
    {
        // Request authorization from the user.
        //
	    //echo $authUrl;
        //printf("Open the following link in your browser:\n%s\n", $authUrl);
        //print 'Enter verification code: ';
        $authCode = "";

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
        printf("Credentials saved to %s\n", $credentialsPath);
        return $accessToken;
    }

    public function getAuthUrl() {



		//$authUrl = $client->createAuthUrl();
	}

    /**
     * @param Google_Client $client
     * @throws \Google_Exception
     */
    public function initClient(Google_Client $client)
    {
        $client->setApplicationName("webfiles-framework");
        $client->setScopes(array(Google_Service_Calendar::CALENDAR_READONLY, Google_Service_Calendar::CALENDAR));
        $client->setAuthConfig(json_decode($this->authConfigAsJsonString, true));
        $client->setAccessType('offline');
        $client->setRedirectUri("http://localhost");
    }

    public function storeWebfile(MWebfile $webfile)
    {

        if (!$webfile instanceof MEvent) {
            throw new MDatastoreException("datastore only accepts instances of 'MEvent'.");
        }

        $nativeGoogleEvent = $this->toNativeGoogleEvent($webfile);

        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);

        $service->events->insert($this->m_sCalendarId, $nativeGoogleEvent);

    }


    /**
     * @param MEvent $webfile
     * @return Google_Service_Calendar_Event
     */
    private function toNativeGoogleEvent(MEvent $webfile)
    {
        $event = new Google_Service_Calendar_Event();
        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime($webfile->getStart());

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime("2018-02-26T10:00:00+01:00");

        $event->setSummary("summary");
        $event->setDescription("description");

        $event->setStart($start);
        $event->setEnd($end);

        return $event;
    }

    /**
     * @param Google_Service_Calendar_Event $event
     * @return MEvent
     */
    private function toWebfileEvent(Google_Service_Calendar_Event $event)
    {
        $webfile = new MEvent();

        $webfile->setStart($event->getStart());
        $webfile->setEnd($event->getEnd());
        $webfile->setDescription($event->getDescription());
        $webfile->setSummary($event->getDescription());

        return $webfile;

    }

    /**
     * Returns all webfiles from the actual datastore.
     * @return array list of webfiles
     */
    public function getWebfilesAsArray()
    {
        // TODO: Implement getWebfilesAsArray() method.
    }

    /**
     * Returns the latests webfiles. Sorting will
     * happen according to the time information of the webfiles.
     *
     * @param int $count Count of webfiles to be selected.
     * @return array list of webfiles
     */
    public function getLatestWebfiles($count = 5)
    {
        // TODO: Implement getLatestWebfiles() method.
    }

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
    public function searchByTemplate(MWebfile $template)
    {
        // TODO: Implement searchByTemplate() method.
    }
}