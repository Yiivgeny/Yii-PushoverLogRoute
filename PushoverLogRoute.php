<?php

class PushoverLogRoute extends CLogRoute {

    const API_URL = 'https://api.pushover.net/1/messages.json';

    /**
     * @var string alphanumeric Pushover Application API Token
     */
    public $token;
    /**
     * @var string user/group key (not e-mail address) on Pushover
     */
    public $userKey;
    /**
     * @var array associative array with additional API params (e.g.: device, timestamp)
     * @link https://pushover.net/api Pushover API documentation
     */
    public $options = array();
    /**
     * @var array level-based params for notification
     * @link https://pushover.net/api Pushover API documentation
     */
    protected $levelOptions = array(
        CLogger::LEVEL_PROFILE => array(
            'priority' => -1,
        ),
        CLogger::LEVEL_TRACE   => array(
            'priority' => -1,
        ),
        CLogger::LEVEL_INFO    => array(
            'priority' => -1,
        ),
        CLogger::LEVEL_WARNING => array(
            'sound' => 'siren',
            'priority' => 0
        ),
        CLogger::LEVEL_ERROR   => array(
            'sound' => 'tugboat',
            'priority' => 1
        ),
    );
    /**
     * Not recomended include trace, profile or info levels. It's cause flood your device.
     * @var string list of levels separated by comma or space. Defaults to 'error', meaning all levels.
     */
    public $levels = 'error';

    protected function getMaxLevelEntry($logs) {
        $levelPriority = array_flip(array_keys($this->levelOptions));
        $lastLevel     = -1;
        $result        = null;
        foreach ($logs AS $entry) {
            if ($lastLevel < $levelPriority[$entry[1]]) {
                $result    = $entry;
                $lastLevel = $levelPriority[$entry[1]];
            }
        }
        return $result;
    }

    protected function sendNotification($message, $level, $options = array()) {
        $params = array(
            'token'   => $this->token,
            'user'    => $this->userKey,
            'message' => $message
        );
        $params += $this->levelOptions[$level] + $options + $this->options;

        $context = stream_context_create(array(
            'http' => array(
                'method'        => 'POST',
                'header'        => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
                'user_agent'    => 'Yii Framework Pushover log route',
                'content'       => http_build_query(array_filter($params), null, '&', PHP_QUERY_RFC3986),
                'ignore_errors' => true,
            )
        ));
        $response = file_get_contents(self::API_URL, false, $context);
        if (!$response || !($answer = CJSON::decode($response)) || $answer['status'] !== 1){
            trigger_error('Pushover not delivered notifivation: '.$response, E_USER_NOTICE);
        }
    }

    public function getLevelOptions(){
        return $this->levelOptions;
    }

    public function setLevelOptions($value){
        $this->levelOptions = CMap::mergeArray($this->levelOptions, $value);
    }

    public function init() {
        if (!$this->token)
            throw new Exception ('Pushover Application token required');
        if (!$this->userKey)
            throw new Exception ('Pushover User key required');
    }

    protected function processLogs($logs) {
        $log = $this->getMaxLevelEntry($logs);
        list($message) = explode("\n", $log[0], 2);

        $options = array(
            'title' => Yii::app()->name.' ['.$log[1].']'
        );
        $this->sendNotification($message, $log[1], $options);
    }

}