<?php

namespace webfilesframework\core\datastore\types\mail;

use webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Representation of a mail used in the imap datastore.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MMail extends MWebfile
{

    private $m_sFrom;
    private $m_sTo;

    private $m_dDate;

    private $m_sSubject;
    private $m_lMessage;

    private $m_bIsAnswered;
    private $m_bIsDeleted;
    private $m_bIsSeen;
    private $m_bIsDraft;

    public function __construct()
    {

    }

    public static function isMailAddressValid($p_sEmail)
    {
        return (preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $p_sEmail));
    }

    public function getTime()
    {
        return $this->m_dDate;
    }

    public function getGeograficPosition()
    {
        return NULL;
    }

    public function getFrom()
    {
        return $this->m_sFrom;
    }

    public function setFrom($from)
    {
        $this->m_sFrom = $from;
    }

    public function getTo()
    {
        return $this->m_sTo;
    }

    public function setTo($to)
    {
        $this->m_sTo = $to;
    }

    public function getDate()
    {
        return $this->m_dDate;
    }

    public function setDate($date)
    {
        $this->m_dDate = $date;
    }

    public function getSubject()
    {
        return $this->m_sSubject;
    }

    public function setSubject($subject)
    {
        $this->m_sSubject = $subject;
    }

    public function getMessage()
    {
        return $this->m_lMessage;
    }

    public function setMessage($message)
    {
        $this->m_lMessage = $message;
    }

    /**
     * @return bool
     */
    public function isAnswered()
    {
        return $this->m_bIsAnswered;
    }

    /**
     * @param bool $answered
     */
    public function setAnswered($answered)
    {
        $this->m_bIsAnswered = $answered;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->m_bIsDeleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted($deleted)
    {
        $this->m_bIsDeleted = $deleted;
    }

    /**
     * @return bool
     */
    public function isSeen()
    {
        return $this->m_bIsSeen;
    }

    /**
     * @param mixed $seen
     */
    public function setSeen($seen)
    {
        $this->m_bIsSeen = $seen;
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->m_bIsDraft;
    }

    /**
     * @param bool $draft
     */
    public function setDraft($draft)
    {
        $this->m_bIsDraft = $draft;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDate() . "<br /><b>" . $this->m_sFrom . "</b><br />" . $this->m_sSubject . "<br /><br />
				<div style=\"text-align:left; width:500px;margin-left: auto ;margin-right: auto ;\">" . $this->getMessage() . "</div>";
    }
}