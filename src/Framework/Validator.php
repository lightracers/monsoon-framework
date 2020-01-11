<?php

/**
 * Form validation library.
 *
 * @author Tasos Bekos <tbekos@gmail.com>
 * @author Chris Gutierrez <cdotgutierrez@gmail.com>
 * @author Corey Ballou <ballouc@gmail.com>
 * @see https://github.com/blackbelt/php-validation
 * @see Based on idea: http://brettic.us/2010/06/18/form-validation-class-using-php-5-3/
 */

namespace Framework;
class Validator
{

    /** @var array  */
    protected $messages = [];

    /** @var array  */
    protected $errors = [];

    /** @var array  */
    protected $rules = [];

    /** @var array  */
    protected $fields = [];

    /** @var array  */
    protected $functions = [];

    /** @var array  */
    protected $arguments = [];

    /** @var array  */
    protected $filters = [];

    /** @var array  */
    protected $data = null;

    /** @var array  */
    protected $validData = [];

    /**
     * Constructor.
     * Define values to validate.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if (! empty($data)) {
            $this->setData($data);
        }
    }

    /**
     * set the data to be validated
     *
     * @access public
     * @param mixed $data
     * @return Validator
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    // ----------------- ADD NEW RULE FUNCTIONS BELOW THIS LINE ----------------
    /**
     * Field, if completed, has to be a valid email address.
     *
     * @param string $message
     * @return Validator
     */
    public function email($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($email) {
            if (strlen($email) == 0) {
                return true;
            }

            $isValid = true;
            $atIndex = strrpos($email, '@');
            if (is_bool($atIndex) && ! $atIndex) {
                $isValid = false;
            } else {
                $domain    = substr($email, ($atIndex + 1));
                $local     = substr($email, 0, $atIndex);
                $localLen  = strlen($local);
                $domainLen = strlen($domain);
                if ($localLen < 1 || $localLen > 64) {
                    $isValid = false;
                } else if ($domainLen < 1 || $domainLen > 255) {
                    // domain part length exceeded
                    $isValid = false;
                } else if ($local[0] == '.' || $local[($localLen - 1)] == '.') {
                    // local part starts or ends with '.'
                    $isValid = false;
                } else if (preg_match('/\\.\\./', $local)) {
                    // local part has two consecutive dots
                    $isValid = false;
                } else if (! preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                    // character not valid in domain part
                    $isValid = false;
                } else if (preg_match('/\\.\\./', $domain)) {
                    // domain part has two consecutive dots
                    $isValid = false;
                } else if (! preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
                    // character not valid in local part unless
                    // local part is quoted
                    if (! preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                        $isValid = false;
                    }
                }

                /*
                 * // check DNS
                 * if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
                 * $isValid = false;
                 * }
                 */
            }

            return $isValid;
            },
            $message
        );
            return $this;
    }

    /**
     * Field must be filled in.
     *
     * @param string $message
     * @return Validator
     */
    public function required($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            if (is_scalar($val)) {
                $val = trim($val);
            }

            return ! empty($val);
            },
            $message
        );
            return $this;
    }

    /**
     * Field must contain a valid float value.
     *
     * @param string $message
     * @return Validator
     */
    public function float($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return ! (filter_var($val, FILTER_VALIDATE_FLOAT) === false);
            },
            $message
        );
            return $this;
    }

    /**
     * Field must contain a valid integer value
     *
     * @param null $message
     * @return $this
     */
    public function integer($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return ! (filter_var($val, FILTER_VALIDATE_INT) === false);
            },
            $message
        );
            return $this;
    }

    /**
     * Every character in field, if completed, must be a digit.
     * This is just like integer(), except there is no upper limit.
     *
     * @param string $message
     * @return Validator
     */
    public function digits($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return (strlen($val) === 0 || ctype_digit((string) $val));
            },
            $message
        );
            return $this;
    }

    /**
     * Field must contain only alphabets
     *
     * @param null $message
     * @return $this
     */
    public function alphabets($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return (strlen($val) === 0 || ctype_alpha(str_replace(' ', 'MMMMMMMMMMMMMMMMMMMM', $val)));
            },
            $message
        );
            return $this;
    }

    /**
     * Field must contain alphabets and numbers
     *
     * @param null $message
     * @return $this
     */
    public function alnum($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            $val = str_replace(' ', 'MMMMMMMMMMMMMMMMMMMM', $val);
            return (strlen($val) === 0 || (ctype_alnum((string) $val)));
            },
            $message
        );
            return $this;
    }

    /**
     * Field must be a number greater than [or equal to] X.
     *
     * @param int $limit
     * @param bool $include
     *          Whether to include limit value.
     * @param string $message
     * @return Validator
     */
    public function min($limit, $include = true, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            if (strlen($val) === 0) {
                return true;
            }

            $val   = (float) $val;
            $limit = (float) $args[0];
            $inc   = (bool) $args[1];
            return ($val > $limit || ($inc === true && $val === $limit));
            },
            $message,
            [
                $limit,
                $include,
            ]
        );
            return $this;
    }

    /**
     * Field must be a number greater than [or equal to] X.
     *
     * @param int $limit
     * @param bool $include
     *          Whether to include limit value.
     * @param string $message
     * @return Validator
     */
    public function max($limit, $include = true, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            if (strlen($val) === 0) {
                return true;
            }

            $val   = (float) $val;
            $limit = (float) $args[0];
            $inc   = (bool) $args[1];
            return ($val < $limit || ($inc === true && $val === $limit));
            },
            $message,
            [
                $limit,
                $include,
            ]
        );
            return $this;
    }

    /**
     * Field must be a number between X and Y.
     *
     * @param int $min
     * @param int $max
     * @param bool $include
     *          Whether to include limit value.
     * @param string $message
     * @return Validator
     */
    public function between($min, $max, $include = true, $message = null)
    {
        $message = $message != null ? $this->_getDefaultMessage(__FUNCTION__, [$min, $max, $include]) : null;
        $this->min($min, $include, $message)->max($max, $include, $message);
        return $this;
    }

    /**
     * Field has to be greater than or equal to X characters long.
     *
     * @param int $len
     * @param string $message
     * @return Validator
     */
    public function minlength($len, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            return ! (strlen(trim($val)) < $args[0]);
            },
            $message,
            [$len]
        );
            return $this;
    }

    /**
     * Field has to be less than or equal to X characters long.
     *
     * @param int $len
     * @param string $message
     * @return Validator
     */
    public function maxlength($len, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            return ! (strlen(trim($val)) > $args[0]);
            },
            $message,
            [$len]
        );
            return $this;
    }

    /**
     * Field has to be between minlength and maxlength characters long.
     *
     * @param int $minlength
     * @param int $maxlength
     * @param string $message
     * @return Validator
     */
    public function betweenlength($minlength, $maxlength, $message = null)
    {
        $message = empty($message) ? self::_getDefaultMessage(__FUNCTION__, [$minlength, $maxlength]) : $message;
        $this->minlength($minlength, $message)->max($maxlength, $message);
        return $this;
    }

    /**
     * Field has to be X characters long.
     *
     * @param int $len
     * @param string $message
     * @return Validator
     */
    public function length($len, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            return (strlen(trim($val)) == $args[0]);
            },
            $message,
            [$len]
        );
            return $this;
    }

    /**
     * Field is the same as another one (password comparison etc).
     *
     * @param string $field
     * @param string $label
     * @param string $message
     * @return Validator
     */
    public function matches($field, $label, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            return ((string) $args[0] == (string) $val);
            },
            $message,
            [
                $this->_getVal($field),
                $label,
            ]
        );
            return $this;
    }

    /**
     * Field is different from another one.
     *
     * @param string $field
     * @param string $label
     * @param string $message
     * @return Validator
     */
    public function notmatches($field, $label, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            return ((string) $args[0] != (string) $val);
            },
            $message,
            [
                $this->_getVal($field),
                $label,
            ]
        );
            return $this;
    }

    /**
     * Field must start with a specific substring.
     *
     * @param string $sub
     * @param string $message
     * @return Validator
     */
    public function startsWith($sub, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            $sub = $args[0];
            return (strlen($val) === 0 || substr($val, 0, strlen($sub)) === $sub);
            },
            $message,
            [$sub]
        );
            return $this;
    }

    /**
     * Field must NOT start with a specific substring.
     *
     * @param string $sub
     * @param string $message
     * @return Validator
     */
    public function notstartsWith($sub, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            $sub = $args[0];
            return (strlen($val) === 0 || substr($val, 0, strlen($sub)) !== $sub);
            },
            $message,
            [$sub]
        );
            return $this;
    }

    /**
     * Field must end with a specific substring.
     *
     * @param string $sub
     * @param string $message
     * @return Validator
     */
    public function endsWith($sub, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            $sub = $args[0];
            return (strlen($val) === 0 || substr($val, - strlen($sub)) === $sub);
            },
            $message,
            [$sub]
        );
            return $this;
    }

    /**
     * Field must not end with a specific substring.
     *
     * @param string $sub
     * @param string $message
     * @return Validator
     */
    public function notendsWith($sub, $message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            $sub = $args[0];
            return (strlen($val) === 0 || substr($val, - strlen($sub)) !== $sub);
            },
            $message,
            [$sub]
        );
            return $this;
    }

    /**
     * Field has to be valid IP address.
     *
     * @param string $message
     * @return Validator
     */
    public function ip($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return (strlen(trim($val)) === 0 || filter_var($val, FILTER_VALIDATE_IP) !== false);
            },
            $message
        );
            return $this;
    }

    /**
     * Field has to be valid internet address.
     *
     * @param string $message
     * @return Validator
     */
    public function url($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return (strlen(trim($val)) === 0 || filter_var($val, FILTER_VALIDATE_URL) !== false);
            },
            $message
        );
            return $this;
    }

    /**
     * Date format.
     *
     * @return string
     */
    protected function _getDefaultDateFormat()
    {
        return 'd/m/Y';
    }

    /**
     * Field has to be a valid date.
     *
     * @param string $message
     * @param string $format
     * @param string $separator
     * @return Validator
     */
    public function date($message = null, $format = null, $separator = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            if (strlen(trim($val)) === 0) {
                return true;
            }

            try {
                if (is_object(new \DateTime($val, new \DateTimeZone("UTC")))) {
                    return true;
                }
            } catch (\Exception $e) {
                return false;
            }

            return false;
            },
            $message,
            [
                $format,
                $separator,
            ]
        );
            return $this;
    }

    /**
     * Field has to be a date later than or equal to X.
     *
     * @param string|int $date
     *          Limit date
     * @param string $format
     *          Date format
     * @param string $message
     * @return Validator
     */
    public function minDate($date = 0, $format = null, $message = null)
    {
        if (empty($format)) {
            $format = $this->_getDefaultDateFormat();
        }

        if (is_int($date)) {
            $date = new \DateTime($date . ' days');
// Days difference from today
        } else {
            $fieldValue = $this->_getVal($date);
            $date       = ($fieldValue == false) ? $date : $fieldValue;
            $date       = \DateTime::createFromFormat($format, $date);
        }

        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            $format    = $args[1];
            $limitDate = $args[0];
            return ($limitDate > \DateTime::createFromFormat($format, $val)) ? false : true;
            },
            $message,
            [
                $date,
                $format,
            ]
        );
            return $this;
    }

    /**
     * Field has to be a date later than or equal to X.
     *
     * @param string|integer $date
     *          Limit date.
     * @param string $format
     *          Date format.
     * @param string $message
     * @return Validator
     */
    public function maxDate($date = 0, $format = null, $message = null)
    {
        if (empty($format)) {
            $format = $this->_getDefaultDateFormat();
        }

        if (is_int($date)) {
            $date = new \DateTime($date . ' days');
// Days difference from today
        } else {
            $fieldValue = $this->_getVal($date);
            $date       = ($fieldValue == false) ? $date : $fieldValue;
            $date       = \DateTime::createFromFormat($format, $date);
        }

        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            $format    = $args[1];
            $limitDate = $args[0];
            return ! ($limitDate < \DateTime::createFromFormat($format, $val));
            },
            $message,
            [
                $date,
                $format,
            ]
        );
            return $this;
    }

    /**
     * Field has to be a valid credit card number format.
     *
     * @see https://github.com/funkatron/inspekt/blob/master/Inspekt.php
     * @param string $message
     * @return Validator
     */
    public function ccnum($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($value) {
            $value  = str_replace(' ', '', $value);
            $length = strlen($value);
            if ($length < 13 || $length > 19) {
                return false;
            }

            $sum    = 0;
            $weight = 2;
            for ($i = ($length - 2); $i >= 0; $i--) {
                $digit  = ($weight * $value[$i]);
                $sum   += (floor($digit / 10) + $digit % 10);
                $weight = ($weight % 2 + 1);
            }

            $mod = ((10 - $sum % 10) % 10);
            return ($mod == $value[($length - 1)]);
            },
            $message
        );
            return $this;
    }

    /**
     * Field has to be one of the allowed ones.
     *
     * @param string|array $allowed
     *          Allowed values.
     * @param string $message
     * @return Validator
     */
    public function oneOf($allowed, $message = null)
    {
        if (is_string($allowed)) {
            $allowed = explode(',', $allowed);
        }

        $this->setRule(
            __FUNCTION__,
            function ($val, $args) {
            return in_array($val, $args[0]);
            },
            $message,
            [$allowed]
        );
            return $this;
    }

    // --------------- END [ADD NEW RULE FUNCTIONS ABOVE THIS LINE] ------------
    /**
     * callback
     *
     * @param mixed $callback
     * @param string $message
     * @param mixed $params
     * @return Validator
     */
    public function callback($callback, $message = '', $params = [])
    {
        if (is_callable($callback)) {
            // If an array is callable, it is a method
            if (is_array($callback)) {
                $function = new \ReflectionMethod($callback[0], $callback[1]);
            } else {
                $function = new \ReflectionFunction($callback);
            }

            if (! empty($function)) {
                // needs a unique name to avoild collisions in the rules array
                $name = 'callback_' . sha1(uniqid());
                $this->setRule(
                    $name,
                    function ($value) use ($function, $params, $callback) {
                    // Creates merged arguments array with validation target as first argument
                    $args = array_merge([$value], (is_array($params) ? $params : []));
                    if (is_array($callback)) {
                        // If callback is a method, the object must be the first argument
                        return $function->invokeArgs($callback[0], $args);
                    } else {
                        return $function->invokeArgs($args);
                    }
                    },
                    $message,
                    $params
                );
            }
        } else {
            throw new \Exception(sprintf('%s is not callable.', $callback));
        }

        return $this;
    }

    // ------------------ PRE VALIDATION FILTERING -------------------
    /**
     * add a filter callback for the data
     *
     * @param mixed $callback
     * @return Validator
     */
    public function filter($callback)
    {
        if (is_callable($callback)) {
            $this->filters[] = $callback;
        }

        return $this;
    }

    /**
     * applies filters based on a data key
     *
     * @access protected
     * @param string $key
     * @return void
     */
    protected function _applyFilters($key)
    {
        $this->_applyFilter($this->data[$key]);
    }

    /**
     * recursively apply filters to a value
     *
     * @access protected
     * @param mixed $val
     *          reference
     * @return void
     */
    protected function _applyFilter(&$val)
    {
        if (is_array($val)) {
            foreach ($val as $key => &$item) {
                $this->_applyFilter($item);
            }
        } else {
            foreach ($this->filters as $filter) {
                $val = $filter($val);
            }
        }
    }

    /**
     * validate
     *
     * @param string $key
     * @param boolean $recursive
     * @param string $label
     * @return bool
     */
    public function validate($key, $recursive = false, $label = '')
    {
        // set up field name for error message
        $this->fields[$key] = (empty($label)) ? 'Field with the name of "' . $key . '"' : $label;
        // apply filters to the data
        $this->_applyFilters($key);
        $val = $this->_getVal($key);
        // validate the piece of data
        $this->_validate($key, $val, $recursive);
        // reset rules
        $this->rules   = [];
        $this->filters = [];
        return $val;
    }

    /**
     * recursively validates a value
     *
     * @access protected
     * @param string $key
     * @param mixed $val
     * @return bool
     */
    protected function _validate($key, $val, $recursive = false)
    {
        if ($recursive && is_array($val)) {
            // run validations on each element of the array
            foreach ($val as $index => $item) {
                if (! $this->_validate($key, $item, $recursive)) {
                    // halt validation for this value.
                    return false;
                }
            }

            return true;
        } else {
            // try each rule function
            foreach ($this->rules as $rule => $isTrue) {
                if ($isTrue) {
                    $function = $this->functions[$rule];
                    $args     = $this->arguments[$rule];
// Arguments of rule
                    $valid = (empty($args)) ? $function($val) : $function($val, $args);
                    if ($valid === false) {
                        $this->registerError($rule, $key);
                        $this->rules = [];
// reset rules
                        $this->filters = [];
                        return false;
                    }
                }
            }

            $this->validData[$key] = $val;
            return true;
        }
    }

    /**
     * Whether errors have been found.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (count($this->errors) > 0);
    }

    /**
     * Get specific error.
     *
     * @param string $field
     * @return string
     */
    public function getError($field)
    {
        return $this->errors[$field];
    }

    /**
     * Get all errors.
     *
     * @param boolean $keys
     * @return array
     */
    public function getAllErrors($keys = true)
    {
        return ($keys == true) ? $this->errors : array_values($this->errors);
    }

    public function getValidData()
    {
        return $this->validData;
    }

    /**
     * _getVal with added support for retrieving values from int and
     * associative multi-dimensional arrays.
     * When doing so, use DOT notation
     * to indicate a break in keys, i.e.:
     *
     * key = "one.two.three"
     *
     * would search the array:
     *
     * array('one' => array(
     * 'two' => array(
     * 'three' => 'RETURN THIS'
     * )
     * );
     *
     * @param string $key
     * @return mixed
     */
    protected function _getVal($key)
    {
        // handle multi-dimensional arrays
        if (strpos($key, '.') !== false) {
            $arrData = null;
            $keys    = explode('.', $key);
            $keyLen  = count($keys);
            for ($i = 0; $i < $keyLen; ++$i) {
                if (trim($keys[$i]) == '') {
                    return false;
                } else {
                    if (empty($arrData)) {
                        if (! isset($this->data[$keys[$i]])) {
                            return false;
                        }

                        $arrData = $this->data[$keys[$i]];
                    } else {
                        if (! isset($arrData[$keys[$i]])) {
                            return false;
                        }

                        $arrData = $arrData[$keys[$i]];
                    }
                }
            }

            return $arrData;
        } else {
            return (isset($this->data[$key])) ? $this->data[$key] : false;
        }
    }

    /**
     * Register error.
     *
     * @param string $rule
     * @param string $key
     * @param string $message
     */
    public function registerError($rule, $key, $message = null)
    {
        if (empty($message)) {
            $message = $this->messages[$rule];
        }

        $this->errors[$key] = sprintf($message, ($this->fields[$key] ?? null));
    }

    /**
     * Set rule.
     *
     * @param string $rule
     * @param closure $function
     * @param string $message
     * @param array $args
     */
    public function setRule($rule, $function, $message = '', $args = [])
    {
        if (! array_key_exists($rule, $this->rules)) {
            $this->rules[$rule] = true;
            if (! array_key_exists($rule, $this->functions)) {
                if (! is_callable($function)) {
                    die('Invalid function for rule: ' . $rule);
                }

                $this->functions[$rule] = $function;
            }

            $this->arguments[$rule] = $args;
// Specific arguments for rule
            $this->messages[$rule] = (empty($message)) ? $this->_getDefaultMessage($rule, $args) : $message;
        }
    }

    /**
     * Get default error message.
     *
     * @param string $rule
     * @param array $args
     * @return string
     */
    protected function _getDefaultMessage($rule, $args = null)
    {
        switch ($rule) {
            case 'email':
                $message = '%s is an invalid email address.';
                break;
            case 'ip':
                $message = '%s is an invalid IP address.';
                break;
            case 'url':
                $message = '%s is an invalid url.';
                break;
            case 'required':
                $message = '%s is required.';
                break;
            case 'float':
                $message = '%s must consist of numbers only.';
                break;
            case 'integer':
                $message = '%s must consist of integer value.';
                break;
            case 'digits':
                $message = '%s must consist only of digits.';
                break;
            case 'min':
                $message = '%s must be greater than ';
                if ($args[1] == true) {
                    $message .= 'or equal to ';
                }

                $message .= $args[0] . '.';
                break;
            case 'max':
                $message = '%s must be less than ';
                if ($args[1] == true) {
                    $message .= 'or equal to ';
                }

                $message .= $args[0] . '.';
                break;
            case 'between':
                $message = '%s must be between ' . $args[0] . ' and ' . $args[1] . '.';
                if ($args[2] == false) {
                    $message .= '(Without limits)';
                }
                break;
            case 'minlength':
                $message = '%s must be at least ' . $args[0] . ' characters or longer.';
                break;
            case 'maxlength':
                $message = '%s must be no longer than ' . $args[0] . ' characters.';
                break;
            case 'length':
                $message = '%s must be exactly ' . $args[0] . ' characters in length.';
                break;
            case 'matches':
                $message = '%s must match ' . $args[1] . '.';
                break;
            case 'notmatches':
                $message = '%s must not match ' . $args[1] . '.';
                break;
            case 'startsWith':
                $message = '%s must start with "' . $args[0] . '".';
                break;
            case 'notstartsWith':
                $message = '%s must not start with "' . $args[0] . '".';
                break;
            case 'endsWith':
                $message = '%s must end with "' . $args[0] . '".';
                break;
            case 'notendsWith':
                $message = '%s must not end with "' . $args[0] . '".';
                break;
            case 'date':
                $message = '%s is not valid date.';
                break;
            case 'mindate':
                $message = '%s must be later than ' . $args[0]->format($args[1]) . '.';
                break;
            case 'maxdate':
                $message = '%s must be before ' . $args[0]->format($args[1]) . '.';
                break;
            case 'oneof':
                $message = '%s must be one of ' . implode(', ', $args[0]) . '.';
                break;
            case 'ccnum':
                $message = '%s must be a valid credit card number.';
                break;
            default:
                $message = '%s has an error.';
                break;
        }

        return $message;
    }

    // -------- Additional methods

    public function reset()
    {
        $this->rules   = [];
        $this->filters = [];
    }

    /**
     * Add custom error message
     *
     * @param $message
     */
    public function addError($message)
    {
        $this->registerError('custom', 'custom_field', $message);
    }

    /**
     * Vaidate a GUID
     *
     * @param null $message
     * @return $this
     */
    public function guid($message = null)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return (preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $val) ? true : false);
            },
            $message
        );
            return $this;
    }

    /**
     * Validate formatted DateTime
     *
     * @param $message
     * @return $this
     */
    public function formattedDateTime($message)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return (\DateTime::createFromFormat('d-M-Y H:i', $val)) ? true : false;
            },
            $message
        );
            return $this;
    }

    /**
     * Validate formatted Date
     *
     * @param $message
     * @return $this
     */
    public function formattedDate($message)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {

            $date = \DateTime::createFromFormat('d-M-Y', $val);
            if (! $date) {
                $date = \DateTime::createFromFormat('m/d/Y', $val);
            //fallback
                if (! $date) {
                    return false;
                }
            }

            if (! is_object($date) || ($date->format('d-M-Y') != $val && $date->format('m/d/Y') != $val )) {
                return false;
            } else {
                return true;
            }
            },
            $message
        );
            return $this;
    }

    /**
     * Validate Hexadecimal digits
     *
     * @param $message
     * @return $this
     */
    public function hex($message)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return (ctype_xdigit($val)) ? true : false;
            },
            $message
        );
            return $this;
    }

    /**
     * Validate color code
     *
     * @param $message
     * @return $this
     */
    public function colorCode($message)
    {
        $this->setRule(
            __FUNCTION__,
            function ($val) {
            return (ctype_xdigit(str_replace('#', '', $val))) ? true : false;
            },
            $message
        );
            return $this;
    }

    /**
     * is email?
     *
     * @param string $emailID
     * @return boolean
     */
    public static function isEmail($emailID)
    {
        $valid = filter_var($emailID, FILTER_VALIDATE_EMAIL);
        return ! empty($valid);
        // return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $email); //old way
    }

    /**
     * Is string?
     *
     * @param $text
     * @param bool $onlyAlphabets
     * @return false|int
     */
    public static function isString($text, $onlyAlphabets = false)
    {
        if ($onlyAlphabets) {
            return preg_match("/^([a-zA-Z ]+)+$/", $text);
        } else {
            return preg_match("/^([a-zA-Z0-9.,_;: -]+)+$/", $text);
        }
    }

    /**
     * is Number?
     *
     * @param $number
     * @param bool $checkIntFloat
     * @return bool
     */
    public static function isNumber(&$number, $checkIntFloat = false)
    {
        if ($checkIntFloat) {
            return is_int($number);
        } else {
            return (preg_match("^[0-9]+^", $number) && $number > 0);
        }
    }

    /**
     * is GUID?
     *
     * @param $string
     * @return bool
     */
    public static function isGUID($string)
    {
        return (preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $string) ? true : false);
    }

    /**
     * is Date?
     *
     * @param $date
     * @return bool
     */
    public function isDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') == $date;
    }

    /**
     * is DateTime?
     *
     * @param $dateTime
     * @return bool
     */
    public static function isDateTime($dateTime)
    {
        try {
            if (is_object(new \DateTime($dateTime, new \DateTimeZone("UTC")))) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * is short GUID?
     *
     * @param $string
     * @return bool
     */
    public static function isShortGUID($string)
    {
        return ctype_alnum($string);
    }
}
