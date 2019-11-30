<?php

/**
 * PHP Mutex implementation.
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Mutex;

/**
 * Create a mutex using the file system as storage.
 */
final class FilesystemMutexFactory implements MutexFactory
{
    private string $storageDirectory;

    public function __construct(string $storageDirectory)
    {
        $this->storageDirectory = \rtrim($storageDirectory, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $id): Mutex
    {
        return new FilesystemMutex($id, \sprintf('%s/%s', $this->storageDirectory, \sha1($id)));
    }
}
