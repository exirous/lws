<?php
/**
 * @property string device
 * @property string method
 */
class AHttpRequest extends CHttpRequest
{
    const PARAM_TYPE_STRING  = 'string';
    const PARAM_TYPE_NUMERIC = 'numeric';
    const PARAM_TYPE_ARRAY   = 'array';

    const IS_PHONE      = 'phone';
    const IS_TABLET     = 'tablet';
    const IS_DESKTOP    = 'desktop';
    const DEVICE_COOKIE = 'kp_device';

    const METHOD_POST   = 'POST';
    const METHOD_GET    = 'GET';
    const METHOD_DELETE = 'DELETE';

    /**
     * @var array of routes or route patterns where csrf token validation event should be
     * detached. Exact route can be defined (eg. "fb/door/index") or pattern using wildcat
     * (eg. "fb/door/*" prevents csrf validation for all DoorController actions)
     */
    public $noCsrfValidationRoutes = array();
    private $_csrfToken;

    protected function normalizeRequest()
    {
        parent::normalizeRequest();
        // @todo: get rid of "Yii::app()->params['interface'] == 'web'"
        if ($this->enableCsrfValidation && $this->noCsrfValidationRoutes &&
            (empty(Yii::app()->params['interface']) || Yii::app()->params['interface'] == 'web')
        )
        {
            $currentRoute = Yii::app()->getUrlManager()->parseUrl($this);
            $detachEvent  = false;
            foreach ($this->noCsrfValidationRoutes as $route)
            {
                $routeMatchesPattern = false;
                if ((strpos($route, '*')))
                    $routeMatchesPattern = (strpos($currentRoute, str_replace('*', '', $route)) === 0);

                if ($routeMatchesPattern || ($route === $currentRoute))
                {
                    $detachEvent = true;
                    break;
                }
            }

            if ($detachEvent)
                Yii::app()->detachEventHandler('onBeginRequest', array($this, 'validateCsrfToken'));
        }
    }

    public function getCsrfToken()
    {
        if ($this->_csrfToken === null)
        {
            $session   = Yii::app()->session;
            $csrfToken = $session->itemAt($this->csrfTokenName);
            if ($csrfToken === null)
            {
                $csrfToken = sha1(uniqid(mt_rand(), true));
                $session->add($this->csrfTokenName, $csrfToken);
            }
            $this->_csrfToken = $csrfToken;
        }

        return $this->_csrfToken;
    }

    public function validateCsrfToken($event)
    {
        if ($this->getIsPostRequest())
        {
            // only validate POST requests
            $session = Yii::app()->session;
            if ($session->contains($this->csrfTokenName) && isset($_POST[$this->csrfTokenName]))
            {
                $tokenFromSession = $session->itemAt($this->csrfTokenName);
                $tokenFromPost    = $_POST[$this->csrfTokenName];
                $valid            = $tokenFromSession === $tokenFromPost;
            }
            else
                $valid = false;
            if (!$valid)
                throw new CHttpException(400, Yii::t('yii', 'The CSRF token could not be verified.'));
        }
    }

    public function getRequiredParam($name, $defaultValue = null, $paramType = null, $throwException = false)
    {
        $param = self::getParam($name, $defaultValue, $paramType, $throwException);

        if (!$param)
            throw new CHttpException(403, Yii::t('base', 'Param {paramName} is not defined'));

        return $param;
    }

    public function getParam($name, $defaultValue = null, $paramType = null, $throwException = false)
    {
        $param = parent::getParam($name, $defaultValue);

        if ($paramType && $param !== null && !$this->_getParamTypeIsCorrect($paramType, $param))
        {
            if ($throwException)
                throw new Exception('Request param "' . $name . '" is incorrect');
            else
                return $defaultValue;
        }

        return $param;
    }

    public function getDevice()
    {
        $device = false;
        if (isset($this->cookies[self::DEVICE_COOKIE]))
            $device = $this->cookies[self::DEVICE_COOKIE]->value;
        if (!$device || !in_array($device, array(self::IS_PHONE, self::IS_TABLET, self::IS_DESKTOP)))
        {
            $device = $this->_getDevice();

            Yii::app()->request->cookies[self::DEVICE_COOKIE] = new CHttpCookie(self::DEVICE_COOKIE, $device, array(
                'domain' => '.' . str_replace('http://', '', Yii::app()->params['hosts']['client']),
                'expire' => time() + 60*60*24*30
            ));
        }
        return $device;
    }

    private function _getDevice()
    {
        $userAgent = $this->userAgent;
        if (preg_match('/tablet|pad|gt\-p/i', $userAgent))
            return self::IS_TABLET;
        if (preg_match('/phone/i', $userAgent))
            return self::IS_PHONE;
        if (preg_match('/android|iemobile/i', $userAgent))
        {
            if (preg_match('/ mobile/i', $userAgent))
                return self::IS_PHONE;
            else if (preg_match('/desktop/i', $userAgent))
                return self::IS_DESKTOP;
            else
                return self::IS_TABLET;
        }
        return self::IS_DESKTOP;
    }

    public function isAndroid2xBrowser()
    {
        return preg_match('/Android 2\./i', $this->userAgent);
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    protected  function _getParamTypeIsCorrect($paramType, $param)
    {
        switch ($paramType)
        {
            case self::PARAM_TYPE_STRING:
                return is_string($param);
            case self::PARAM_TYPE_NUMERIC:
                return is_numeric($param);
            case self::PARAM_TYPE_ARRAY:
                return is_array($param);
            default:
                throw new Exception('Defined param type is incorrect');
        }
    }


    public function getJsonData()
    {
        return CJSON::decode($this->getRawBody());
    }

    public function getRequiredRawBodyParam($name, $defaultValue = null, $paramType = null, $throwException = true)
    {
        $param = self::getRawBodyParam($name, $defaultValue, $paramType, $throwException);

        if (!$param)
            throw new Exception('Request param "' . $name . '" is undefined');

        return $param;
    }

    public function getRawBodyParam($name, $defaultValue = null, $paramType = null, $throwException = false)
    {
        $body = $this->getJsonData();

        $param = isset($body[$name]) ? $body[$name] : $defaultValue;

        if ($paramType && $param !== null && !$this->_getParamTypeIsCorrect($paramType, $param))
        {
            if ($throwException)
                throw new Exception('Request param "' . $name . '" is incorrect');
            else
                return $defaultValue;
        }

        return $param;
    }
}