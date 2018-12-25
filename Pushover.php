<?php
/**
 * PHP SDK for the pushover.net API
 *
 * @author Redjan Ymeraj <ymerajr@yahoo.com>
 */
class Pushover
{
    const API_URL = "https://api.pushover.net/1/messages.json";
    const VALIDATE_USER_URL = "https://api.pushover.net/1/users/validate.json";
    const SOUNDS_LIST_URL = "https://api.pushover.net/1/sounds.json";
    const RECEIPTS_URL = "https://api.pushover.net/1/receipts/%s.json?token=%s";
    const CANCEL_RECEIPT_URL = "https://api.pushover.net/1/receipts/%s/cancel.json";

    const PRIORITY_LOWEST = -2;
    const PRIORITY_LOW = -1;
    const PRIORITY_NORMAL = 0;
    const PRIORITY_HIGH = 1;
    const PRIORITY_HIGHEST = 2;

    // values in seconds
    const MINIMUM_RETRY_VALUE = 30;
    const MAXIMUM_EXPIRE_VALUE = 10800;

    /**
     * @var string
     */
    private $appToken;

    /**
     * @var array|[]string
     */
    private $userKeys;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string|null
     */
    private $device;

    /**
     * @var string|null
     */
    private $url;

    /**
     * @var string|null
     */
    private $urlTitle;

    /**
     * @var integer|null
     */
    private $priority;

    /**
     * @var integer|null
     */
    private $retry;

    /**
     * @var integer|null
     */
    private $expire;

    /**
     * @var string|null
     */
    private $sound;

    /**
     * @var integer|null
     */
    private $timestamp;

    /**
     * @var integer
     */
    private $html;

    /**
     * @var stdClass
     */
    private $response;

    /**
     * @var string|null
     */
    private $callback;

    /**
     * Pushover constructor.
     * @param null $appToken
     * @param null $userKey
     */
    public function __construct($appToken = null, $userKey = null)
    {
        $this->html = 0;
        $this->appToken = $appToken;
        $this->userKeys = array();

        if($userKey){
            $this->addUserKey($userKey);
        }
    }

    /**
     * @param $token
     * @return $this
     */
    public function setAppToken($token)
    {
        $this->appToken = $token;

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setUserKey($key)
    {
        // clear keys
        $this->userKeys = array();
        $this->addUserKey($key);

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function addUserKey($key)
    {
        $this->userKeys[] = $key;

        return $this;
    }

    /**
     * @param null|string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param null|string $device
     * @return $this
     */
    public function setDevice($device)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * @param null|string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param null|string $urlTitle
     * @return $this
     */
    public function setUrlTitle($urlTitle)
    {
        $this->urlTitle = $urlTitle;

        return $this;
    }

    /**
     * @param int|null $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = intval($priority);

        return $this;
    }

    /**
     * Frequency in seconds to retry the notification until user acknowledges.
     *
     * @param int|null $retry
     * @return $this
     */
    public function setRetry($retry)
    {
        $retry = intval($retry);

        if($retry < self::MINIMUM_RETRY_VALUE){
            throw new InvalidArgumentException("This parameter must have a value of at least 30 seconds between retries.");
        }

        $this->retry = $retry;

        return $this;
    }

    /**
     * @param int|null $expire
     * @return $this
     */
    public function setExpire($expire)
    {
        $expire = intval($expire);

        if($expire > self::MAXIMUM_EXPIRE_VALUE){
            throw new InvalidArgumentException("This parameter must have a maximum value of at most 10800 seconds (3 hours).");
        }

        $this->expire = intval($expire);

        return $this;
    }

    /**
     * @param null|string $sound
     * @return $this
     */
    public function setSound($sound)
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * @param int|null $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = intval($timestamp);

        return $this;
    }

    /**
     * @param int $html
     * @return $this
     */
    public function setHtml($html)
    {
        $this->html = intval($html);

        return $this;
    }

    /**
     * @param string|null $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return stdClass
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return integer
     * @throws InvalidArgumentException
     */
    public function getStatus()
    {
        if($this->response){
            return intval($this->response->status);
        }

        throw new \InvalidArgumentException('No response available. Did you forget to do the request first?');
    }

    /**
     * Send message notification
     *
     * @return Pushover
     * @throws InvalidArgumentException
     */
    public function send() {
        if(empty($this->appToken) || count($this->userKeys) == 0) {
            throw new \InvalidArgumentException("Missing App Token or User Key");
        }

        if(empty($this->message)) {
            throw new \InvalidArgumentException("Message is required!");
        }

        if($this->priority == 2 && (!$this->retry || !$this->expire)){
            throw new \InvalidArgumentException("To send an emergency-priority notification, the priority parameter must be set to 2 and the retry and expire parameters must be supplied.");
        }

        if(!$this->timestamp) {
            $this->timestamp = (new \DateTime())->getTimestamp();
        }

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, self::API_URL);
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, array(
            'token' => $this->appToken,
            'user' => implode(',', $this->userKeys),
            'title' => $this->title,
            'message' => $this->message,
            'html' => $this->html,
            'device' => $this->device,
            'priority' => $this->priority,
            'timestamp' => $this->timestamp,
            'expire' => $this->expire,
            'retry' => $this->retry,
            'callback' => $this->callback,
            'url' => $this->url,
            'url_title' => $this->urlTitle,
            'sound' => $this->sound
        ));
        $rawResponse = curl_exec($c);
        $this->response = json_decode($rawResponse);

        return $this;
    }

    /**
     * Get list of available sounds to be set as receiver tunes
     *
     * @return stdClass|null
     */
    public function getSoundsList()
    {
        if(empty($this->appToken)) {
            throw new \InvalidArgumentException("Missing App Token");
        }

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, self::SOUNDS_LIST_URL."?token=".$this->appToken);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $rawResponse = curl_exec($c);
        $this->response = json_decode($rawResponse);

        if(isset($this->response->sounds)){
            return $this->response->sounds;
        }

        return null;
    }

    /**
     * Validate a user from the User Key if is valid to be used
     *
     * @return bool
     */
    public function validateUser() {
        if(empty($this->appToken) || count($this->userKeys) == 0) {
            throw new \InvalidArgumentException("Missing App Token or User Key");
        }

        $c = curl_init();
        curl_setopt($c,CURLOPT_URL,self::VALIDATE_USER_URL);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POSTFIELDS,array(
            "token" => $this->appToken,
            "user" => $this->userKeys[0]
        ));

        $rawResponse = curl_exec($c);
        $this->response = json_decode($rawResponse);

        return $this->getStatus() == 1;
    }

    /**
     * Get detailed information about a receipt
     *
     * @param $receipt
     * @return mixed|stdClass
     */
    public function getReceipt($receipt)
    {
        if(empty($this->appToken)) {
            throw new \InvalidArgumentException("Missing App Token");
        }

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, sprintf(self::RECEIPTS_URL, $receipt, $this->appToken));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $rawResponse = curl_exec($c);
        $this->response = json_decode($rawResponse);

        return $this->response;
    }

    /**
     * Cancel a recurring notification by the receipt
     *
     * @param $receipt
     * @return bool
     */
    public function cancelEmergencyPriority($receipt) {
        if(empty($this->appToken)) {
            throw new \InvalidArgumentException("Missing App Token");
        }

        $c = curl_init();
        curl_setopt($c,CURLOPT_URL,sprintf(self::CANCEL_RECEIPT_URL, $receipt));
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POSTFIELDS,array(
            "token" => $this->appToken,
        ));

        $rawResponse = curl_exec($c);
        $this->response = json_decode($rawResponse);

        return $this->getStatus() == 1;
    }
}