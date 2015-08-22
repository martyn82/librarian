<?php

namespace AppBundle\EventSourcing\ReadStore;

class MemoryStorage implements Storage
{
    /**
     * @var Document[]
     */
    private $data = [];

    /**
     * @param string $identity
     * @param Document $document
     */
    public function upsert($identity, Document $document)
    {
        $this->data[$identity] = $document;
    }

    /**
     * @param string $identity
     */
    public function delete($identity)
    {
        if (!array_key_exists($identity, $this->data)) {
            return;
        }

        unset($this->data[$identity]);
    }

    /**
     * @param string $identity
     * @return Document
     */
    public function find($identity)
    {
        if (!array_key_exists($identity, $this->data)) {
            return null;
        }

        return $this->data[$identity];
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return Document[]
     */
    public function findAll($offset = 0, $limit = 500)
    {
        return array_values(
            array_slice($this->data, $offset, $limit)
        );
    }

    /**
     */
    public function clear()
    {
       $this->data = [];
    }

    /**
     * @param array $criteria
     * @param integer $offset
     * @param integer $limit
     * @return Document[]
     */
    public function findBy(array $criteria, $offset = 0, $limit = 500)
    {
        return array_slice(
            array_reduce(
                $this->data,
                function (array $result, Document $item) use ($criteria) {
                    foreach ($criteria as $key => $value) {
                        $getterMethod = 'get' . ucfirst($key);

                        if ($item->{$getterMethod}() != $value) {
                            return $result;
                        }
                    }

                    $result[] = $item;
                    return $result;
                },
                []
            ),
            $offset,
            $limit
        );
    }
}
