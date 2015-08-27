<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Paginator;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\EntityInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\SoftDeletableInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\DoctrineManager\Model\TimestampableInterface;
use Symfonian\Indonesia\CoreBundle\Toolkit\Util\ArrayUtil\ArrayNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class Manager
{
    protected $class;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var ManagerFactory
     */
    protected $factory;

    abstract protected function isSupportedObject($object);

    abstract public function serialize($object);

    abstract public function getName();

    /**
     * @param ManagerFactory $factory
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param ObjectManager $objectManager
     * @param string $class
     */
    public function __construct(ManagerFactory $factory, Request $request, TokenStorageInterface $tokenStorage, ObjectManager $objectManager, $class)
    {
        $this->manager = $objectManager;
        $this->repository = $objectManager->getRepository($class);
        $this->class = $objectManager->getClassMetadata($class)->getName();
        $this->request = $request;
        $this->identifier = 'id';

        $cache = $objectManager->getConfiguration()->getHydrationCacheImpl();
        $this->cache = $cache ?: new ArrayCache();
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function createNew()
    {
        return new $this->class();
    }

    /**
     * @param EntityInterface $object
     * @param array $data
     */
    public function save(EntityInterface $object, array $data = array())
    {
        $this->bindData($object, $data);

        if ($object instanceof TimestampableInterface) {
            if (!$object->getId()) {
                $object->setCreatedAt(new \DateTime());
                $object->setCreatedBy($this->getUser());
            }

            $object->setUpdatedAt(new \DateTime());
            $object->setUpdatedBy($this->getUser());
        }

        $this->commit($object);
    }

    /**
     * @param EntityInterface $object
     */
    public function delete(EntityInterface $object)
    {
        $cacheId = sprintf('%s_%s', $this->getEntityShortName(), $object->getId());
        if ($object instanceof SoftDeletableInterface) {
            $object->isDelete(true);
            $object->setDeletedAt(new \DateTime());
            $object->setDeletedBy($this->getUser());

            $this->save($object);
        } else {
            $this->manager->remove($object);
            $this->manager->flush();

            $this->deleteCache($cacheId);
        }
    }

    /**
     * @param $id
     * @return mixed object of $class
     */
    public function find($id)
    {
        $cacheId = sprintf('%s_%s', $this->getEntityShortName(), $id);
        $object = $this->fetchFromCache($cacheId);

        if (!$object) {
            $object = $this->repository->find($id);
            $this->saveCache($cacheId, $object);
        }

        return $object;
    }

    /**
     * @param $id
     * @return array
     */
    public function findArray($id)
    {
        $cacheId = sprintf('%s_%s', $this->getEntityShortName(), $id);
        $result = $this->fetchFromCache($cacheId);

        if (!$result) {
            $queryBuilder = $this->repository->createQueryBuilder('o');
            $queryBuilder->andWhere($queryBuilder->expr()->eq('o.'.$this->identifier, $id));
            $result = $this->getOneOrNullResult($queryBuilder, Query::HYDRATE_ARRAY);

            $this->saveCache($cacheId, $result);
        }

        return $result;
    }

    /**
     * @param array $criteria
     * @param bool|false $softDelete
     * @param bool|false $isDelete
     * @param string $fieldId
     * @return array
     */
    public function findByArray(array $criteria, $softDelete = false, $isDelete = false, $fieldId = 'isDelete')
    {
        $queryBuilder = $this->repository->createQueryBuilder('o');
        foreach ($criteria as $key => $value) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq(sprintf('o.%s', $key), sprintf(':%s', $key)));
            $queryBuilder->setParameter($key, $value);
        }

        if ($softDelete) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('o.'.$fieldId, $queryBuilder->expr()->literal($isDelete)));
        }

        return $this->getOneOrNullResult($queryBuilder, Query::HYDRATE_ARRAY);
    }

    /**
     * @param array $criteria
     * @param bool|false $softDelete
     * @param bool|false $isDelete
     * @param string $fieldId
     * @return mixed object of $class
     */
    public function findBy(array $criteria, $softDelete = false, $isDelete = false, $fieldId = 'isDelete')
    {
        if ($softDelete && $isDelete) {
            $criteria = array_merge($criteria, array($fieldId => true));
        }

        $object = $this->repository->findOneBy($criteria);
        if ($object) {
            $cacheId = sprintf('%s_%s', $this->getEntityShortName(), $object->getId());
            $this->saveCache($cacheId, $object);

            return $object;
        }
    }

    /**
     * @param array $data
     * @return mixed object of $class
     */
    public function unserialize(array $data)
    {
        $object = $this->createNew();

        return $this->bindData($object, $data);
    }

    /**
     * @param Paginator $paginator
     * @param Query $query
     * @param $page
     * @param $perPage
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function paginate(Paginator $paginator, Query $query, $page, $perPage)
    {
        return $paginator->paginate($query, $page, $perPage);
    }

    protected function commit($object)
    {
        if (!$this->isSupportedObject($object)) {
            throw new \InvalidArgumentException(sprintf('The class must be instance of %s', $this->class));
        }

        $this->manager->persist($object);

        $cacheId = sprintf('%s_%s', $this->getEntityShortName(), serialize($object->getId()));
        if ($this->isExistCache($cacheId)) {
            $this->deleteCache($cacheId);
        }

        $this->manager->flush();
        $this->manager->clear();
    }

    protected function getResult(QueryBuilder $queryBuilder, $hydration = Query::HYDRATE_OBJECT, $useCache = true, $lifetime = 1)
    {
        $query = $queryBuilder->getQuery();
        $query->useResultCache($useCache, $lifetime, sprintf('%s_%s', $this->class, serialize($query->getParameters())));
        $query->useQueryCache($useCache);
        $result = $query->getResult($hydration);

        return $result;
    }

    protected function getOneOrNullResult(QueryBuilder $queryBuilder, $hydration = Query::HYDRATE_OBJECT, $useCache = true, $lifetime = 1)
    {
        $query = $queryBuilder->getQuery();
        $query->useResultCache($useCache, $lifetime, sprintf('%s_%s', $this->class, serialize($query->getParameters())));
        $query->useQueryCache($useCache);
        $result = $query->getOneOrNullResult($hydration);

        return $result;
    }

    protected function generateCacheKey($value)
    {
        return md5($value);
    }

    protected function saveCache($id, $object, $lifetime = 2700)
    {
        $this->cache->save($this->generateCacheKey($id), $object, $lifetime);
    }

    protected function fetchFromCache($id)
    {
        $object = $this->cache->fetch($this->generateCacheKey($id));

        if (!$object) {
            return null;
        }

        if (is_object($object)) {
            return $this->manager->merge($object);
        }

        return $object;
    }

    protected function isExistCache($id)
    {
        return $this->cache->contains($this->generateCacheKey($id));
    }

    protected function deleteCache($id)
    {
        $this->cache->delete($this->generateCacheKey($id));
    }

    protected function bindData($object, array $data = array())
    {
        return ArrayNormalizer::convertToObject($data, $object);
    }

    protected function getEntityShortName()
    {
        $reflectionClass = new \ReflectionClass($this->class);

        return $reflectionClass->getShortName();
    }

    protected function getUser()
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user) {
            return $user->getUsername();
        }

        return 'NOT_AUTHETICATED_USER';
    }

    protected function get($manager)
    {
        $this->factory->getManager($manager);
    }
}
