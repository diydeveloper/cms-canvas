<?php 

namespace CmsCanvas\Exceptions;

class PermissionDenied extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $permission  The internal exception message
     * @param \Exception $previous    The previous exception
     * @param int        $code        The internal exception code
     */
    public function __construct($permission = null, $message = null, \Exception $previous = null, $code = 0)
    {
        if ($message == null) {
            $message = "You do not have permission to access this page, please refer to your system administrator.";
        }

        if ($permission != null) {
            $message .= " (Permission: $permission)";
        }

        $this->setHeading('Permission Denied');

        parent::__construct(403, $message, $previous, [], $code);
    }
}
