<?php

namespace simpleserv\webfilesframework\core\datasystem\file\system\dropbox;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDropboxAccount
{

    private $consumerKey = '...';  // Your Consumer Key
    private $consumerSecret = '...';  // Your Consumer Secret

    private $callback = 'http://localhost/webdev/source';
    //private $callback       = 'http://galerie.pfarrei-altenkessel.de/downloadAndCompress.php';
    private $storage;
    private $OAuth;
    private $dropboxApi;

    public function __construct($consumerKey, $consumerSecret, $callback)
    {

        $this->storage = new \Dropbox\OAuth\Storage\Session;

        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->callback = $callback;

        $oauthAccessTokenFile = "tmp/dropbox-oauth-access.token";
        $oauthRequestTokenFile = "tmp/dropbox-oauth-request.token";

        if (file_exists($oauthAccessTokenFile) && file_exists($oauthRequestTokenFile)) {

            //TOKENS ARE AVAILABLE

            $acc_token = unserialize(file_get_contents($oauthAccessTokenFile));
            $this->storage->set($acc_token, 'access_token');
            $req_token = unserialize(file_get_contents($oauthRequestTokenFile));
            $this->storage->set($req_token, 'request_token');

            $this->OAuth = new \Dropbox\OAuth\Consumer\Curl(
                $this->consumerKey,
                $this->consumerSecret,
                $this->storage,
                $this->callback);
            $this->dropboxApi = new \Dropbox\API($this->OAuth, 'dropbox');

        } else {

            //TOKENS ARE NOT AVAILABLE

            $this->OAuth = new \Dropbox\OAuth\Consumer\Curl(
                $this->consumerKey,
                $this->consumerSecret,
                $this->storage,
                $this->callback);
            $this->dropboxApi = new \Dropbox\API($this->OAuth, 'dropbox');

            $acc_token = serialize($this->storage->get('access_token'));
            file_put_contents($oauthAccessTokenFile, $acc_token);

            $req_token = serialize($this->storage->get('request_token'));
            file_put_contents($oauthRequestTokenFile, $req_token);

        }

    }

    /**
     *
     * Enter description here ...
     */
    public function getDropboxApi()
    {
        return $this->dropboxApi;
    }

    /**
     *
     * Enter description here ...
     */
    public function getOAuth()
    {
        return $this->OAuth;
    }

    /**
     *
     * Enter description here ...
     */
    public function getStorage()
    {
        return $storage;
    }

}
