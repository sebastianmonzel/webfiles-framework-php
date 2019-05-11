<?php

namespace webfilesframework\core\datastore\types\googlecalendar;


interface MISecretStore {

	public function store($secret);
	public function read();

}
