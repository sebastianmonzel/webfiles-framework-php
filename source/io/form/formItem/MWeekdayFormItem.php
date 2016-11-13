<?php

namespace simpleserv\webfilesframework\io\form\formItem;

use simpleserv\webfilesframework\core\time\MWeekday;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MWeekdayFormItem extends MDropdownMenueFormItem
{

    public function init()
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