<?php

namespace webfilesframework\core\datastore\types\googlecalendar;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use webfilesframework\core\datastore\MAbstractCachableDatastore;
use webfilesframework\core\datastore\MDatastoreException;
use webfilesframework\core\datastore\MISingleDatasourceDatastore;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;


class MGoogleCalendarDatastore extends MAbstractCachableDatastore
	implements MISingleDatasourceDatastore
{
	// https://developers.google.com/google-apps/calendar/quickstart/php#step_2_install_the_google_client_library
	/** @var string */
	private $m_sCalendarId;
	private $authConfigAsJsonString;
	private $redirectUrl;
	private $secretStore;

	public function __construct($calendarId, $authConfigAsJsonString, $redirectUrl, MISecretStore $secretStore)
	{
		$this->m_sCalendarId = $calendarId;
		$this->authConfigAsJsonString = $authConfigAsJsonString;
		$this->redirectUrl = $redirectUrl;
		$this->secretStore = $secretStore;

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
	 * @throws MDatastoreException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	public function getAllWebfiles()
	{
		$client = $this->getClientWithToken();
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

	/**
	 * @return Google_Client
	 * @throws MDatastoreException
	 */
	public function getClientWithToken() {

		$client = $this->getClient();

		$secret = $this->secretStore->read();

		if (isset($secret)) {
			$accessToken = json_decode($secret,true);
		} else {
			throw new MDatastoreException("No credentials set. Please request new Credentials.");
		}
		$client->setAccessToken($accessToken);

		$this->refreshAccessTokenIfExpired($client);

		return $client;

	}

	/**
	 * @param $client Google_Client
	 */
	public function refreshAccessTokenIfExpired($client)
	{
		if ($client->isAccessTokenExpired()) {
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			$this->secretStore->store(json_encode($client->getAccessToken()));
		}
	}


	/**
	 * @param $client
	 * @return mixed
	 */
	private function requestAccessToken($authCode)
	{
		$client = $this->getClient();
		$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
		$this->secretStore->store(json_encode($accessToken));
		return $accessToken;
	}

	public function initAccessToken($authcode) {
		$this->requestAccessToken($authcode);
	}

	/**
	 * @param Google_Client $client
	 * @throws \Google_Exception
	 */
	private function initClient(Google_Client $client)
	{
		$client->setApplicationName("webfiles-framework");
		$client->setScopes(array(Google_Service_Calendar::CALENDAR_READONLY, Google_Service_Calendar::CALENDAR));
		$client->setAuthConfig(json_decode($this->authConfigAsJsonString, true));
		$client->setAccessType('offline');
		$client->setApprovalPrompt('force');
		$client->setRedirectUri($this->redirectUrl);
	}

	public function storeWebfile(MWebfile $webfile)
	{

		if (!$webfile instanceof MEvent) {
			throw new MDatastoreException("datastore only accepts instances of 'MEvent'.");
		}

		$nativeGoogleEvent = $this->toNativeGoogleEvent($webfile);

		$client = $this->getClientWithToken();
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
		$end->setDateTime($webfile->getEnd());

		$event->setSummary($webfile->getSummary());
		$event->setDescription($webfile->getDescription());

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