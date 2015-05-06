<?php
/**
 * @author Oliver Lorenz
 * @since 2015-04-28
 * Time: 18:33
 */

namespace oliverlorenz\reactphpmqtt\packet;

interface Message {
    public function getIdentifier();
}