<?php

namespace webfilesframework\core\datastore\types\mail;

use ReflectionException;
use webfilesframework\core\datastore\MAbstractDatastore;
use webfilesframework\core\datastore\MDatastoreException;
use webfilesframework\core\datastore\MISingleDatasourceDatastore;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;
use webfilesframework\core\datasystem\file\system\MDirectory;
use webfilesframework\core\datasystem\file\system\MFile;
use webfilesframework\MWebfilesFrameworkException;

/**
 * Class to connect to a datastore based on a imap mailaccount.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MImapDatastore extends MAbstractDatastore
    implements MISingleDatasourceDatastore
{

    /**@var MMailAccount **/
    private $m_oMailAccount;

    private $connection;


    public function __construct(MMailAccount $mailAccount = null)
    {
        if ($mailAccount != null) {
            $this->m_oMailAccount = $mailAccount;
            $this->connection = $this->imap_login(
                $mailAccount->getHost(),
                $mailAccount->getPort(),
                $mailAccount->getUser(),
                $mailAccount->getPassword(),
                "INBOX",
                true);
        }
    }

    public function tryConnect()
    {
        return true;
    }

    public function isReadOnly()
    {
        return true;
    }

	/**
	 * According to the given timestamp the next matching webfile will be searched and returned.
	 *
	 * @param $timestamp
	 *
	 * @return MWebfile|null
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 */
    public function getNextWebfileForTimestamp($timestamp)
    {
        $webfiles = $this->getAllWebfiles()->getArray();

        ksort($webfiles);

        foreach ($webfiles as $key => $value) {
	        if ( $key > $timestamp) {
                return $value;
            }
        }
        return null;
    }

	/**
	 * @return MWebfileStream
	 * @throws ReflectionException
	 * @throws MWebfilesFrameworkException
	 */
    public function getAllWebfiles()
    {

        $webfileArray = array();

        $mails = $this->imap_getlist();
        foreach ($mails as $mailItem) {

            $mail = new MMail();

            // FROM
            $from = mb_decode_mimeheader($mailItem['from']);
            $from = str_replace("_", " ", $from);
            $from = str_replace("<", "&lt;", $from);
            $from = str_replace(">", "&gt;", $from);
            $mail->setFrom($from);

            // DATE
            $mail->setDate(strtotime($mailItem['date']));

            // SUBJECT
            $subject = $mailItem['subject'];
            mb_internal_encoding('UTF-8');
            $subject = str_replace("_", " ", mb_decode_mimeheader($subject));
            $mail->setSubject($subject);

            // MESSAGE
            $bodyText = imap_fetchbody($this->connection, $mailItem['msgno'], 2);
            if (empty($bodyText)) {
                $bodyText = imap_fetchbody($this->connection, $mailItem['msgno'], 1.1);
            }
            $bodyText = quoted_printable_decode($bodyText);
            $bodyText = $this->cleanOutHtml($bodyText);
            $bodyText = preg_replace('/\<p(.*)\>/', '<p>', $bodyText); // REMOVE ANY ATTRIBUTES FROM <P>ARAGRAPH-TAG
            $bodyText = preg_replace('/[\s]+/', ' ', $bodyText); // REMOVE DOUBLE WHITESPACE
            $mail->setMessage($bodyText);

            $time = $mail->getTime();
            $webfileArray[$time] = $mail;
        }

        return new MWebfileStream($webfileArray);
    }

    private function cleanOutHtml($value)
    {
        $value = preg_replace('/(<|>)\1{2}/is', '', $value);
        $value = preg_replace(
            array(// Remove invisible content
                '@<head[^>]*?>.*?</head>@siu',
                '@<style[^>]*?>.*?</style>@siu',
                '@<script[^>]*?.*?</script>@siu',
                '@<noscript[^>]*?.*?</noscript>@siu',
            ),
            "", //replace above with nothing
            $value);
        return strip_tags($value, "<p><br><br /><img><a>");
    }


    public function getLatestWebfiles($count = 5)
    {

        // todoooo
    }

    public function hasItem(MWebfile $item)
    {
        $directoryPath = (new MDirectory($this->m_sDirectoryPath))->getPath();
        $file = new MFile($directoryPath . "/" . $item->getId() . ".webfile");
        return $file->exists();
    }

    public function searchByTemplate(MWebfile $template)
    {
        if (!$template instanceof MMail) {
            throw new MDatastoreException("Cannot search in imap datastore for webfiles appart of type 'MMail'.");
        }
    }

    /**
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $pass
     * @param string $folder
     * @param bool|string $ssl
     * @return resource
     */
    private function imap_login($host, $port, $user, $pass, $folder = "INBOX", $ssl = false)
    {

        $ssl = ($ssl == false) ? "/novalidate-cert" : "/ssl";
        return (imap_open("{" . "$host:$port/imap4$ssl" . "}$folder", $user, $pass));
    }

    /**
     *
     * @param unknown $connection
     * @return array
     */
    private function pop3_stat($connection)
    {
        $check = imap_mailboxmsginfo($connection);
        return ((array)$check);
    }

    /**
     * @return array
     */
    private function imap_getlist()
    {

        $result = array();

        $MC = imap_check($this->connection);

        $completeNumberOfMessages = $MC->Nmsgs;
        $numberOfMessagesToShow = 4;

        $start = $completeNumberOfMessages - $numberOfMessagesToShow + 1;
        $end = $completeNumberOfMessages;

        $range = $start . ":" . $end;

        $response = imap_fetch_overview($this->connection, $range);
        foreach ($response as $msg) {
            $result[$msg->msgno] = (array)$msg;
        }
        return $result;
    }

    /**
     *
     * @param unknown $connection
     * @param unknown $message
     * @return string
     */
    private function pop3_retr($connection, $message)
    {

        return (imap_fetchheader($connection, $message, FT_PREFETCHTEXT));
    }

    /**
     *
     * @param unknown $connection
     * @param unknown $message
     * @return boolean
     */
    private function pop3_dele($connection, $message)
    {

        return (imap_delete($connection, $message));
    }

    /**
     * @param $headers
     * @return array
     */
    private function mail_parse_headers($headers)
    {
        $result = array();

        $headers = preg_replace('/\r\n\s+/m', '', $headers);
        preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches);
        foreach ($matches[1] as $key => $value) {
            $result[$value] = $matches[2][$key];
        }

        return $result;
    }

    /**
     *
     * @param string $imap
     * @param string $mid
     * @param bool|string $parse_headers
     * @return unknown
     */
    private function mail_mime_to_array($imap, $mid, $parse_headers = false)
    {

        $mail = imap_fetchstructure($imap, $mid);
        $mail = mail_get_parts($imap, $mid, $mail, 0);
        if ($parse_headers) $mail[0]["parsed"] = mail_parse_headers($mail[0]["data"]);
        return ($mail);
    }

    /**
     *
     * @param string $imap
     * @param string $mid
     * @param string $part
     * @param string $prefix
     * @return Ambigous <multitype:, multitype:NULL >
     */
    private function mail_get_parts($imap, $mid, $part, $prefix)
    {
        $attachments = array();
        $attachments[$prefix] = mail_decode_part($imap, $mid, $part, $prefix);
        if (isset($part->parts)) // multipart
        {
            $prefix = ($prefix == "0") ? "" : "$prefix.";
            foreach ($part->parts as $number => $subpart)
                $attachments = array_merge($attachments, mail_get_parts($imap, $mid, $subpart, $prefix . ($number + 1)));
        }
        return $attachments;
    }

    /**
     *
     * @param unknown $connection
     * @param unknown $message_number
     * @param unknown $part
     * @param unknown $prefix
     * @return array
     */
    private function mail_decode_part($connection, $message_number, $part, $prefix)
    {
        $attachment = array();

        if ($part->ifdparameters) {
            foreach ($part->dparameters as $object) {
                $attachment[strtolower($object->attribute)] = $object->value;
                if (strtolower($object->attribute) == 'filename') {
                    $attachment['is_attachment'] = true;
                    $attachment['filename'] = $object->value;
                }
            }
        }

        if ($part->ifparameters) {
            foreach ($part->parameters as $object) {
                $attachment[strtolower($object->attribute)] = $object->value;
                if (strtolower($object->attribute) == 'name') {
                    $attachment['is_attachment'] = true;
                    $attachment['name'] = $object->value;
                }
            }
        }

        $attachment['data'] = imap_fetchbody($connection, $message_number, $prefix);
        if ($part->encoding == 3) { // 3 = BASE64
            $attachment['data'] = base64_decode($attachment['data']);
        } elseif ($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
            $attachment['data'] = quoted_printable_decode($attachment['data']);
        }
        return $attachment;
    }

    /**
     * @return MMailAccount
     */
    public function getMailAccount()
    {
        return $this->m_oMailAccount;
    }


}