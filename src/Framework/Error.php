<?php

namespace Framework;

/**
 * Error class to generate 404 pages.
 */
class Error extends Controller
{

    /**
     * $error holder.
     *
     * @var string
     */
    private $error = null;

    /**
     * Save error to $this->error.
     *
     * @param string $error
     */
    public function __construct($error)
    {
        parent::__construct();
        $this->error = $error;
    }

    /**
     * Load a 404 page with the error message.
     */
    public function index()
    {
        header("HTTP/1.0 404 Not Found");

        $data['title'] = '404';
        $data['error'] = $this->error;

        View::renderLayout('header', $data);
        View::render('error/404', $data);
        View::renderLayout('footer', $data);
    }

    /**
     * Display errors.
     *
     * @param array|string $error
     *            an error of errors
     * @param string $class
     *            name of class to apply to div
     *
     * @return string return the errors inside divs
     */
    public static function display($error, $class = 'alert alert-danger')
    {
        $errorString = '';
        if (is_array($error)) {
            foreach ($error as $errorMessage) {
                $errorString .= "<div class='$class'>$errorMessage</div>";
            }
        } else {
            $errorString = "<div class='$class'>$error</div>";
        }

        return $errorString;
    }

}
