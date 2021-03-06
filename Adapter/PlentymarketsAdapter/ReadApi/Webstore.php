<?php

namespace PlentymarketsAdapter\ReadApi;

/**
 * Class Webstore
 */
class Webstore extends ApiAbstract
{
    /**
     * @return array
     */
    public function findAll()
    {
        $webstores = $this->client->request('GET', 'webstores');

        $result = [];
        foreach ($webstores as $webstore) {
            $result[$webstore['id']] = $webstore;
        }

        return $result;
    }
}
