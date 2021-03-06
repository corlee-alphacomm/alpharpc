<?php
/**
 * This file is part of AlphaRPC (http://alpharpc.net/)
 *
 * @license BSD-3 (please see the LICENSE file distributed with this source code.
 * @copyright Copyright (c) 2010-2013, Alphacomm Group B.V. (http://www.alphacomm.nl/)
 *
 * @author Reen Lokum <reen@alphacomm.nl>
 * @package AlphaRPC
 * @subpackage StorageHandler
 */

namespace AlphaRPC\Manager\Storage;

/**
 * @author Reen Lokum <reen@alphacomm.nl>
 * @package AlphaRPC
 * @subpackage StorageHandler
 */
class MultiStorage extends AbstractStorage
{
    /**
     * Contains the different Storage backends to which the
     * results should be stored.
     *
     * @var AbstractStorage[]
     */
    protected $storages = array();

    /**
     * Add a new Storage backend.
     *
     * @param AbstractStorage $storage
     *
     * @return $this
     */
    public function addStorage(AbstractStorage $storage)
    {
        $this->storages[] = $storage;

        return $this;
    }

    public function get($key)
    {
        foreach ($this->storages as $storage) {
            $data = $storage->get($key);
            if ($data !== null) {
                return $data;
            }
        }

        return null;
    }

    public function has($key)
    {
        foreach ($this->storages as $storage) {
            if ($storage->has($key)) {
                return true;
            }
        }

        return false;
    }

    public function remove($key)
    {
        $throwMe = false;

        foreach ($this->storages as $storage) {
            try {
                $storage->remove($key);
            } catch (\RuntimeException $ex) {
                $throwMe = $ex;
                unset($ex);
            }
        }

        if ($throwMe) {
            throw $throwMe;
        }
    }

    public function set($key, $value)
    {
        $success = false;
        $throwMe = null;
        foreach ($this->storages as $storage) {
            try {
                $storage->set($key, $value);
                $success = true;
            } catch (\RuntimeException $ex) {
                $throwMe = $ex;
                unset($ex);
            }
        }

        if (!$success) {
            throw $throwMe;
        }

        return $value;
    }
}
