<?php

namespace simpleserv\webfilesframework\core\time;


class MTimespan extends MWebfile {

	private $start;
	private $end;

	public function __construct($start,$end) {
		$this->start = $start;
		$this->end = $end;
	}

	public function getStart() {
		return $this->start;
	}

	public function setStart($start) {
		$this->start = $start;
	}
	
	public function getEnd() {
		return $this->end;
	}
	
	public function setEnd($end) {
		$this->end = $end;
	}

}