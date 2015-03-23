<?php

namespace simpleserv\webfilesframework\core\io\form\formItem;

/**
 * 
 * @author semo
 *
 */
class MWeekdayFormItem extends MDropdownMenueFormItem {
	
	public function init() {
		$this->possibleValues = array(
						new MWeekday(0, "Sonntag"),
						new MWeekday(1, "Montag"),
						new MWeekday(2, "Dienstag"),
						new MWeekday(3, "Mittwoch"),
						new MWeekday(4, "Donnerstag"),
						new MWeekday(5, "Freitag"),
						new MWeekday(6, "Samstag")
						);
		parent::init();
	}
	
}