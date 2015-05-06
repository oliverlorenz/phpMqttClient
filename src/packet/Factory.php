<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-24
 * Time: 19:40
 */

namespace oliverlorenz\reactphpmqtt\packet;

class Factory {

    /**
     * @param $version
     * @param $input
     * @return null|Message
     */
    public static function get($version, $input)
    {
        $message = null;
        if(empty($input)) {
            throw new \InvalidArgumentException();
        }
        switch (ord($input{0})) {
            case ConnectionAck::IDENTIFIER:
                $message = new ConnectionAck($version, $input);
                break;
            case PingResponse::IDENTIFIER:
                $message = new PingResponse($version, $input);
                break;
            default:
                print_r($message);
            }
        return $message;
    }

}