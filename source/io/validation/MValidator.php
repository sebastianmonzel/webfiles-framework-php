<?php

namespace simpleserv\webfilesframework\core\io\form\validation;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MValidator
{

    /**
     *
     * @param unknown_type $urlParam
     * @return string
     */
    public static function validateUrlParam($urlParam)
    {
        return htmlspecialchars($urlParam);
    }

    /**
     *
     * @param unknown_type $comment
     * @param unknown_type $len
     * @return mixed
     */
    public static function cutLongWords($comment, $len = 25)
    {
        $returnvalue = $comment;
        do {
            $comment = $returnvalue;
            $returnvalue = preg_replace('~(^|\s)(\S{' . $len . '})(\S)~S', '\1\2 \3', $comment);
        } while ($returnvalue != $comment);
        return $returnvalue;
    }
}

