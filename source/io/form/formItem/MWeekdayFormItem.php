<?php

namespace webfilesframework\io\form\formItem;

use webfilesframework\core\time\MWeekday;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWeekdayFormItem extends MDropdownMenueFormItem
{

	public function init($useLabel = true)
    {
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