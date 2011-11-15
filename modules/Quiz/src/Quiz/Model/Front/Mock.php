<?php
namespace Quiz\Model\Front;

use Quiz\Model\Front;

/**
 * Special created model to test application on local env.
 *
 * @author Gabriel Habryn <gabriel.habryn@me.com>
 */
class Mock extends Front
{
    public function isAuth()
    {
        return true;
    }

    public function getFacebookData()
    {
        return array(
            'username' => 'widmogrod',
            'first_name' => 'Gabriel'
        );
    }

    public function getFacebookUserId()
    {
        return 666;
    }
}
 
